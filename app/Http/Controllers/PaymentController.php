<?php
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Payment\MercadoPagoPayment;
use MercadoPago\MercadoPagoConfig;
use Illuminate\Support\Facades\Mail;
use App\Mail\PurchaseConfirmationMail;
use App\Mail\TrainerNotificationMail;
use MercadoPago\Client\Payment\PaymentClient;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
{
    $user = auth()->user();

    if (!$user->medical_fit) {
        return redirect()->route('student.profile')->with('error', 'Debes subir tu apto médico antes de comprar un entrenamiento.');
    }

    $cartItems = Cart::with('training.trainer')->where('user_id', auth()->id())->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['error' => 'El carrito está vacío.'], 422);
    }

    $mercadoPago = new MercadoPagoPayment();
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
    $mercadoPago->setBackUrls(
        success: url('/payment/success'),
        pending: url('/payment/pending'),
        failure: url('/payment/failure')
    );

    $totalAmount = array_sum(array_column($items, 'unit_price'));

    $payment = Payment::create([
        'user_id' => $user->id,
        'training_id' => $cartItems->first()->training->id,
        'total_amount' => $totalAmount,
        'company_fee' => 10,
        'trainer_amount' => $totalAmount - 10,
        'status' => 'pending',
        'payment_id' => null,
    ]);
    
    // 🔥 Ahora sí puedes asignar external_reference
    $payment->update(['external_reference' => $payment->id]);
    
    // ✅ Asignamos external_reference a MercadoPago
    $mercadoPago->setExternalReference((string) $payment->id);

    try {
        $mercadoPago->setNotificationUrl(url('/api/payment/webhook'));
        $preference = $mercadoPago->createPayment(); // 🔥 No pasamos el email del entrenador, la empresa recibe el pago

        return redirect($preference->init_point);
    } catch (\Exception $e) {
        Log::error('❌ Error al procesar el pago:', ['message' => $e->getMessage()]);
        return response()->json(['error' => 'Hubo un problema al procesar el pago.'], 500);
    }
}


private function processPayment($paymentId)
{
    try {
        MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));
        $mercadoPago = new \MercadoPago\Client\Payment\PaymentClient();
        $paymentInfo = $mercadoPago->get($paymentId);

        if (!$paymentInfo || empty($paymentInfo->status)) {
            Log::error('❌ No se pudo obtener la información del pago.', ['payment_id' => $paymentId]);
            return response()->json(['error' => 'No se pudo obtener la información del pago.'], 400);
        }

        if ($paymentInfo->status === 'approved') {
            Log::info("✅ El pago $paymentId ha sido aprobado.");

            $externalReference = $paymentInfo->external_reference ?? null;
            if (!$externalReference) {
                Log::error("❌ No se encontró `external_reference` en el pago.");
                return response()->json(['error' => 'No se encontró referencia externa.'], 400);
            }

            // 🔹 Buscar el pago `PENDING` para actualizarlo
            $payment = Payment::where(function ($query) use ($externalReference, $paymentId) {
                if (!empty($externalReference)) {
                    $query->orWhere('id', $externalReference);
                }
                $query->orWhere('payment_id', $paymentId);
            })->first(); // 🔥 Ahora busca el pago en cualquier estado

            if (!$payment) {
                Log::error("❌ No se encontró un pago pendiente en la base de datos con external_reference: $externalReference.");
                return response()->json(['error' => 'No se encontró el pago en la base de datos.'], 400);
            }

            $payment->update([
                'payment_id' => $paymentId,
                'status' => 'approved'
            ]);
            $cartItems = Cart::where('user_id', $payment->user_id)->with('training')->get();

            // Verifica si el carrito está vacío antes de continuar
            if ($cartItems->isEmpty()) {
                Log::error("❌ No se encontraron ítems en el carrito para el usuario {$payment->user_id}");
                return response()->json(['error' => 'No se encontraron ítems en el carrito.'], 400);
            }
            
            // Obtener el primer entrenamiento del carrito
            $training = $cartItems->first()->training;
            $trainer = $training->trainer;
            $user = User::findOrFail($payment->user_id);
            $trainer = User::find($payment->training->trainer_id);

if (!$trainer || !$trainer->mercado_pago_email) {
    Log::error("❌ ERROR: El entrenador no tiene un email de Mercado Pago válido.");
    return response()->json(['error' => 'El entrenador no tiene una cuenta válida de Mercado Pago.'], 400);
}
            // **📩 Enviar correos de confirmación**
            Mail::to($user->email)->send(new PurchaseConfirmationMail($user, $training));
            Mail::to($trainer->email)->send(new TrainerNotificationMail($trainer, $user, $training));
            Cart::where('user_id', $payment->user_id)->delete();
            $transferSuccess = $this->payTrainerByEmail($trainer->mercado_pago_email, $payment->trainer_amount);

            if ($transferSuccess) {
                
                Log::info("🛒 Carrito vaciado para el usuario {$payment->user_id}");
            } else {
                Log::error("❌ No se pudo transferir el dinero al entrenador, el carrito NO se vació.");
            }

    
            
        }

        return response()->json(['message' => 'Pago registrado con éxito'], 200);

    } catch (\Exception $e) {
        Log::error('❌ Error al procesar el pago:', ['message' => $e->getMessage()]);
        return response()->json(['error' => 'Error al procesar el pago.'], 500);
    }
}
private function payTrainerByEmail($trainer_email, $amount)
{
    try {
        MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));
        $client = new \MercadoPago\Client\Payment\PaymentClient();

        $paymentData = [
            "transaction_amount" => $amount,
            "description" => "Pago al entrenador por entrenamiento",
            "payment_method_id" => "wallet_purchase", // 🔥 Esto usa saldo de Mercado Pago correctamente
            "payer" => [
                "email" => config('mercadopago.company_email') // 💰 Email de la cuenta de empresa
            ],
            "receiver_email" => $trainer_email, // 📩 Email del entrenador registrado en Mercado Pago
            "external_reference" => uniqid(), // Referencia única
            "notification_url" => url('/api/payment/webhook')
        ];

        $payment = $client->create($paymentData);
        Log::info("✅ Pago realizado al entrenador con email $trainer_email por $amount ARS.");

        return $payment;

    } catch (\Exception $e) {
        Log::error("❌ Error en el pago al entrenador: " . $e->getMessage());
        return false;
    }
}
    public function success()
    {
        return redirect('/my-trainings')->with('success', 'El pago se realizó con éxito.');
    }

    public function failure()
    {
        return redirect('/payment/failure')->with('error', 'Hubo un problema con el pago.');
    }

    public function pending()
    {
        return redirect('/payment/pending')->with('warning', 'El pago está pendiente de confirmación.');
    }

    public function userPayments()
    {
        $payments = Payment::with('training')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('payment.index', compact('payments'));
    }
    public function handleWebhook(Request $request)
{
    Log::info('📌 Webhook recibido de Mercado Pago:', $request->all());

    $topic = $request->input('topic');
    $paymentId = $request->input('data.id') ?? $request->input('id');

    if (!$paymentId) {
        Log::error('❌ Error: Webhook recibido sin ID de pago.');
        return response()->json(['error' => 'No se recibió un ID de pago.'], 400);
    }

    if ($topic === 'payment') {
        Log::info("✅ Procesando pago directo con ID: $paymentId");
        return $this->processPayment($paymentId);
    }

    if ($topic === 'merchant_order') {
        $resourceUrl = $request->input('resource'); 

        if (!$resourceUrl) {
            Log::error('❌ Error: Webhook de merchant_order sin URL de recurso.');
            return response()->json(['error' => 'No se recibió URL de recurso.'], 400);
        }

        try {
            Log::info("📌 Consultando merchant order en: $resourceUrl");

            MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));

            $response = file_get_contents($resourceUrl, false, stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => 'Authorization: Bearer ' . config('mercadopago.access_token')
                ]
            ]));

            $order = json_decode($response, true);

            if (!isset($order['payments']) || empty($order['payments'])) {
                Log::error("❌ La orden no tiene pagos asociados.");
                return response()->json(['error' => 'La orden no tiene pagos asociados.'], 400);
            }

            foreach ($order['payments'] as $payment) {
                if ($payment['status'] === 'approved') {
                    $paymentId = $payment['id'];
                    Log::info("✅ Pago aprobado encontrado en la orden: $paymentId");

                    // 🔥 🔹 Verificar que `external_reference` llega bien
                    Log::info("🔎 external_reference recibido en webhook: " . ($order['external_reference'] ?? 'NULO'));

                    return $this->processPayment($paymentId);
                }
            }

            return response()->json(['message' => 'No se encontraron pagos aprobados en la orden.'], 400);

        } catch (\Exception $e) {
            Log::error('❌ Error al procesar la notificación de merchant_order:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al procesar la orden de pago.'], 500);
        }
    }

    Log::error('❌ Webhook sin información válida.');
    return response()->json(['error' => 'No se recibió información válida.'], 400);
}
}