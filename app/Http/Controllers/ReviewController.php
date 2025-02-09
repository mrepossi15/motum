<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        // Verifica que el usuario tenga el rol de alumno
        if (Auth::user()->role !== 'alumno') {
            abort(403, 'Solo los alumnos pueden dejar reseñas.');
        }

        $request->validate([
            'comment' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'trainer_id' => 'nullable|exists:users,id',
            'training_id' => 'nullable|exists:trainings,id',
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'trainer_id' => $request->trainer_id,
            'training_id' => $request->training_id,
            'comment' => $request->comment,
            'rating' => $request->rating,
        ]);

        return back()->with('success', 'Reseña agregada con éxito.');
    }
}

