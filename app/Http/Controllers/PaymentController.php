<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Cart;
use App\Models\Training;
use Illuminate\Support\Facades\Log;
use App\Payment\MercadoPagoPayment;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;


class PaymentController extends Controller
{
    public function createSplitPayment(Request $request)
    {
        $user = auth()->user();

        // Validar que el alumno tenga apto médico antes de comprar
        if (!$user->medical_fit) {
            return redirect()->route('student.profile')->with('error', 'Debes subir tu apto médico antes de comprar un entrenamiento.');
        }
        // Obtener los ítems del carrito para el usuario autenticado
        $cartItems = Cart::with('training.trainer')->where('user_id', auth()->id())->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'El carrito está vacío.'], 422);
        }

        // Crear instancia de MercadoPagoPayment
        $mercadoPago = new MercadoPagoPayment();

        // Construir los ítems dinámicamente desde el carrito
        $items = [];
        foreach ($cartItems as $item) {
            $price = (float) $item->training->prices->where('weekly_sessions', $item->weekly_sessions)->first()->price;

            $items[] = [
                'title' => $item->training->title,
                'quantity' => 1,
                'unit_price' => $price,
                'currency_id' => 'ARS',
            ];
        }

        $mercadoPago->setItems($items);

        // Configurar las URLs de retorno
        $mercadoPago->setBackUrls(
            success: url('/payment/success'),
            pending: url('/payment/pending'),
            failure: url('/payment/failure')
        );

        // Configurar la comisión para la empresa
        $mercadoPago->setApplicationFee(10); // Ajusta este valor según tu lógica de negocio

        // Obtener el collector_id del primer entrenador en el carrito
        $trainer = $cartItems->first()->training->trainer;
        $collectorId = $trainer->collector_id ?? null;

        if (!$collectorId) {
            return response()->json(['error' => 'El entrenador no tiene configurado un Collector ID.'], 422);
        }

        try {
            $mercadoPago->setNotificationUrl(url('/api/payment/webhook'));
            // Crear la preferencia de pago
            $externalReference = auth()->id(); // Usamos el ID del usuario autenticado
            $mercadoPago->setExternalReference($externalReference);
            $preference = $mercadoPago->createSplitPayment($collectorId);

            // Redirigir al usuario a la URL de pago
            return redirect($preference->init_point);
        } catch (\Exception $e) {
            \Log::error('Error al procesar el pago:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'Hubo un problema al procesar el pago.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function handleWebhook(Request $request)
    {
        Log::info('Webhook recibido de Mercado Pago:', $request->all());
    
        $topic = $request->input('topic'); // Saber qué tipo de notificación es
        $resourceId = $request->input('id'); // ID de la orden o el pago
    
        if (!$topic || !$resourceId) {
            Log::error('Error: No se recibió un topic o ID en el webhook.');
            return response()->json(['error' => 'No se recibió un topic o ID en el webhook.'], 400);
        }
    
        try {
            // Autenticación con Mercado Pago
            MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));
    
            // 📌 SI RECIBIMOS UNA ORDEN (merchant_order)
            if ($topic === 'merchant_order') {
                Log::info("Consultando orden de compra: $resourceId");
    
                // Obtener la orden de Mercado Pago
                $orderUrl = "https://api.mercadopago.com/merchant_orders/{$resourceId}";
                $orderResponse = file_get_contents($orderUrl, false, stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'header' => 'Authorization: Bearer ' . config('mercadopago.access_token')
                    ]
                ]));
    
                $order = json_decode($orderResponse, true);
    
                if (!isset($order['payments']) || count($order['payments']) === 0) {
                    Log::error("La orden $resourceId no tiene pagos asociados.");
                    return response()->json(['error' => 'La orden no tiene pagos asociados.'], 400);
                }
    
                // 📌 Buscar el primer pago aprobado en la orden
                foreach ($order['payments'] as $payment) {
                    if ($payment['status'] === 'approved') {
                        $paymentId = $payment['id'];
                        Log::info("Pago aprobado encontrado en la orden: $paymentId");
    
                        // Procesar el pago y guardarlo en la base de datos
                        return $this->processPayment($paymentId);
                    }
                }
    
                return response()->json(['message' => 'No se encontraron pagos aprobados en la orden.'], 400);
            }
    
            // 📌 SI RECIBIMOS UN PAGO (payment)
            if ($topic === 'payment') {
                Log::info("Consultando pago: $resourceId");
                return $this->processPayment($resourceId);
            }
    
            return response()->json(['message' => 'Webhook recibido pero no procesado.'], 200);
    
        } catch (\Exception $e) {
            Log::error('Error al procesar la notificación de pago:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al procesar el pago.'], 500);
        }
    }

    private function processPayment($paymentId)
{
    try {
        // Autenticación con Mercado Pago
        MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));
        $mercadoPago = new \MercadoPago\Client\Payment\PaymentClient();
        
        // Obtener los detalles del pago
        $paymentInfo = $mercadoPago->get($paymentId);
        Log::info('Detalles del pago recibido:', (array) $paymentInfo);

        if (!$paymentInfo || empty($paymentInfo->status)) {
            Log::error('Error: No se pudo obtener la información del pago.', ['payment_id' => $paymentId]);
            return response()->json(['error' => 'No se pudo obtener la información del pago.'], 400);
        }

        if ($paymentInfo->status === 'approved') {
            Log::info("El pago $paymentId ha sido aprobado.");

            // 📌 Recuperamos el usuario desde `external_reference`
            $externalReference = $paymentInfo->external_reference ?? null;
            if (!$externalReference) {
                Log::error("No se encontró `external_reference` en el pago $paymentId.");
                return response()->json(['error' => 'No se encontró referencia externa en el pago.'], 400);
            }

            // 📌 Buscar los items en el carrito de ese usuario
            $cartItems = Cart::with('training.trainer')
                ->where('user_id', $externalReference)
                ->get();

            if ($cartItems->isEmpty()) {
                Log::error("No se encontraron items en el carrito para el usuario $externalReference.");
                return response()->json(['error' => 'No se encontraron items en el carrito.'], 400);
            }

            $totalAmount = $paymentInfo->transaction_amount;
            $companyFee = 10;
            $trainerAmount = $totalAmount - $companyFee;
            // 📌 Obtener la cantidad de sesiones semanales desde el carrito
            $weeklySessions = $cartItems->first()->weekly_sessions ?? 1; // Si no está definido, por defecto 1
            // Registrar pago en `payments`
            $payment = Payment::create([
                'user_id' => $externalReference,
                'training_id' => $cartItems->first()->training->id,
                'total_amount' => $totalAmount,
                'company_fee' => $companyFee,
                'trainer_amount' => $trainerAmount,
                'status' => 'approved',
                'payment_id' => $paymentId,
                'external_reference' => $externalReference,
                'weekly_sessions' => $weeklySessions, // 👈 Agregamos esta línea
            ]);

            // Registrar el pago en `payment_details`
            foreach ($cartItems as $item) {
                $trainer = $item->training->trainer;
                PaymentDetail::create([
                    'payment_id' => $payment->id,
                    'user_id' => $trainer->id,
                    'amount' => $trainerAmount,
                    'type' => 'trainer',
                ]);
            }

            // Vaciar carrito del usuario
            Cart::where('user_id', $externalReference)->delete();

            return response()->json(['message' => 'Pago registrado con éxito'], 200);
        }

        return response()->json(['message' => 'El pago aún no ha sido aprobado'], 400);
    } catch (\Exception $e) {
        Log::error('Error al procesar el pago:', ['message' => $e->getMessage()]);
        return response()->json(['error' => 'Error al procesar el pago.'], 500);
    }
}

    /**
     * Redirección cuando el pago es exitoso.
     */
    public function success()
    {
        return redirect('/')->with('success', 'El pago se realizó con éxito.');
    }

    /**
     * Redirección cuando el pago falla.
     */
    public function failure()
    {
        return redirect('/')->with('error', 'Hubo un problema con el pago.');
    }

    /**
     * Redirección cuando el pago queda pendiente.
     */
    public function pending()
    {
        return redirect('/')->with('warning', 'El pago está pendiente de confirmación.');
    }

    public function userPayments()
{
    // Obtener los pagos del usuario autenticado
    $payments = Payment::with('training')
        ->where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();

    return view('payment.index', compact('payments'));
}
}