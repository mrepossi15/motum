<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Park;
use App\Models\Training;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // Guardar un parque o entrenamiento como favorito
    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'favoritable_id' => 'required|integer',
            'favoritable_type' => 'required|string|in:park,training',
        ]);

        $user = Auth::user();

        // Buscar si ya existe en favoritos
        $favorite = Favorite::where('user_id', $user->id)
            ->where('favoritable_id', $request->favoritable_id)
            ->where('favoritable_type', $request->favoritable_type) // Guardamos "park" o "training"
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'Eliminado de favoritos', 'status' => 'removed']);
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'favoritable_id' => $request->favoritable_id,
                'favoritable_type' => $request->favoritable_type, // Guardamos "park" o "training"
            ]);
            return response()->json(['message' => 'Agregado a favoritos', 'status' => 'added']);
        }
    }

    // Mostrar favoritos
    public function index()
    {
        $user = Auth::user();

        $favoriteParks = Favorite::where('user_id', $user->id)
            ->where('favoritable_type', 'park') // Comparamos con "park" en lugar de la clase
            ->with('favoritable')
            ->get();

        $favoriteTrainings = Favorite::where('user_id', $user->id)
            ->where('favoritable_type', 'training') // Comparamos con "training" en lugar de la clase
            ->with('favoritable')
            ->get();

        return view('favorites.index', compact('favoriteParks', 'favoriteTrainings'));
    }
}