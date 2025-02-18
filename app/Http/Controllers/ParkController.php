<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Park;
use Illuminate\Support\Facades\Auth;
use Algolia\AlgoliaSearch\Api\SearchClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\Activity;
use App\Models\Favorite;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ParkController extends Controller
{
    /**
     * Muestra la vista para agregar un parque.
     */
    public function map()
    {
        $activities = Activity::all(); // 🔥 Obtener todas las actividades
        return view('mapa', compact('activities'));
    }
    public function create()
    {
        return view('trainer.add-park'); // Vista para agregar un parque
    }

    public function store(Request $request)
    {
        $request->validate([
            'park_name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location' => 'required|string|max:255',
            'opening_hours' => 'nullable|string',
            'photo_reference' => 'nullable|array',
        ]);
    
        // Procesar opening_hours si no está vacío
        $openingHours = null;
        if (!empty($request->opening_hours)) {
            // Intentar decodificar el JSON proporcionado
            $decodedHours = json_decode($request->opening_hours, true);
    
            // Validar que sea un array
            if (is_array($decodedHours)) {
                $openingHours = json_encode([
                    'Lunes' => $decodedHours[0] ?? 'No especificado',
                    'Martes' => $decodedHours[1] ?? 'No especificado',
                    'Miércoles' => $decodedHours[2] ?? 'No especificado',
                    'Jueves' => $decodedHours[3] ?? 'No especificado',
                    'Viernes' => $decodedHours[4] ?? 'No especificado',
                    'Sábado' => $decodedHours[5] ?? 'No especificado',
                    'Domingo' => $decodedHours[6] ?? 'No especificado',
                ]);
            } else {
                // Si no es JSON válido, almacenarlo como texto plano
                $openingHours = $request->opening_hours;
            }
        }
        // Convertir `photo_references` de JSON a array
        $photoReferences = json_decode($request->photo_references, true);

        if (!is_array($photoReferences)) {
            $photoReferences = [];
        }

        // Descargar y guardar hasta 4 fotos
        $photoUrls = [];
        foreach (array_slice($photoReferences, 0, 4) as $photoReference) {
            try {
                // Descargar la imagen desde la URL proporcionada
                $imageContents = Http::get($photoReference)->body();

                // Generar un nombre único para la imagen
                $imageName = 'parks/' . uniqid() . '.jpg';

                // Guardar la imagen en storage/app/public/parks
                Storage::disk('public')->put($imageName, $imageContents);

                // Guardar la ruta pública
                $photoUrls[] = Storage::url($imageName);
            } catch (\Exception $e) {
                \Log::error("❌ Error al guardar imagen: " . $e->getMessage());
            }
        }
        
            // Crear o buscar el parque
            $park = Park::firstOrCreate(
                ['name' => $request->park_name],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'location' => $request->location,
                    'opening_hours' => $openingHours,
                    'photo_urls' => json_encode($photoUrls),  // Guardar la URL de la imagen en la BD
                ]
            );
        
            // Asociar el parque al usuario autenticado
            $user = Auth::user();
        
            if ($user->parks()->where('park_id', $park->id)->exists()) {
                return redirect()->back()->with('error', 'Este parque ya está asociado a tu cuenta.');
            }
        
            $user->parks()->attach($park->id);
        
            return redirect()->route('trainer.calendar')->with('success', 'Parque agregado exitosamente.');
    }

    public function show(Request $request, $id)
    {
        $user = auth()->user();
        $park = Park::with('trainings.activity')->findOrFail($id);
    
        // Verificar si el parque está en favoritos
        $isFavorite = false;
        if ($user) {
            $isFavorite = Favorite::where('user_id', $user->id)
            ->where('favoritable_id', $park->id)
            ->where('favoritable_type', Park::class)
            ->exists();
        }
    
        return view('parks.show', compact('park', 'isFavorite'));
    }

public function getNearbyParks(Request $request)
{
    $request->validate([
        'lat' => 'required|numeric',
        'lng' => 'required|numeric',
        'radius' => 'nullable|numeric|min:1000|max:10000', // Mínimo 1km, máximo 10km
        'activity_id' => 'nullable|exists:activities,id', // Validar actividad opcionalmente
    ]);

    $lat = $request->lat;
    $lng = $request->lng;
    $radius = $request->input('radius', 5000); // Valor por defecto 5km
    $activityId = $request->input('activity_id'); // Recibir el filtro de actividad

    // 🔥 Obtener solo parques con entrenamientos de la actividad seleccionada
    $query = Park::selectRaw("
        parks.id, parks.name, parks.location, parks.latitude, parks.longitude,
        (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
    ", [$lat, $lng, $lat])
    ->having("distance", "<", $radius / 1000) // Convertimos metros a km
    ->orderBy("distance");

    // 🔥 Si se seleccionó una actividad, filtrar parques con esa actividad
    if ($activityId) {
        $query->whereHas('trainings', function ($q) use ($activityId) {
            $q->where('activity_id', $activityId);
        });
    }

    $parks = $query->get();

    return response()->json($parks);
}
}
