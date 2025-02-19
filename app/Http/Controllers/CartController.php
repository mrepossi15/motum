<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'training_id' => 'required|exists:trainings,id',
            'weekly_sessions' => 'required|integer|min:1',
        ]);
    
        Cart::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'training_id' => $request->training_id,
            ],
            [
                'weekly_sessions' => $request->weekly_sessions,
            ]
        );
    
        return redirect()->back()->with('cart_success', 'El entrenamiento ha sido agregado al carrito.');
    }

    public function viewCart()
    {
        $cartItems = Cart::with('training.trainer')->where('user_id', auth()->id())->get();
        return view('cart.view', compact('cartItems'));
    }
    
    public function remove(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:carts,id',
        ]);

        Cart::where('id', $request->cart_item_id)
            ->where('user_id', auth()->id())
            ->delete();

        return redirect()->back()->with('success', 'Ítem eliminado del carrito.');
    }

    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();

        return redirect()->back()->with('success', 'Carrito vaciado correctamente.');
    }

}