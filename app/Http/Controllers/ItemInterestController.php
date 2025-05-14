<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ItemInterestController extends Controller
{
    /**
     * Muestra los intereses del usuario autenticado.
     */
    public function index()
    {
        $interests = ItemInterest::with(['item.user', 'item.category'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return Inertia::render('Interests/Index', [
            'interests' => $interests
        ]);
    }

    /**
     * Almacena un nuevo interés en un item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'message' => 'nullable|string|max:500',
        ]);

        // Verificar que el usuario no es el propietario del item
        $item = Item::findOrFail($request->item_id);
        if ($item->user_id === Auth::id()) {
            return redirect()->back()->with('error', 'No puedes mostrar interés en tus propios items.');
        }

        // Verificar que no existe ya un interés
        $existingInterest = ItemInterest::where('item_id', $request->item_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingInterest) {
            return redirect()->back()->with('error', 'Ya has mostrado interés en este item.');
        }

        // Crear el nuevo interés
        $interest = new ItemInterest();
        $interest->item_id = $request->item_id;
        $interest->user_id = Auth::id();
        $interest->message = $request->message;
        $interest->status = 'pending';
        $interest->save();

        return redirect()->back()->with('success', 'Has mostrado interés en este item. El propietario será notificado.');
    }

    /**
     * Actualiza el estado de un interés (para el propietario del item).
     */
    public function update(Request $request, ItemInterest $interest)
    {
        // Verificar que el usuario es el propietario del item
        if (Auth::id() !== $interest->item->user_id) {
            return redirect()->back()->with('error', 'No tienes permiso para actualizar este interés.');
        }

        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $interest->status = $request->status;
        $interest->save();

        // Si se acepta el interés, actualizamos el estado del item
        if ($request->status === 'accepted') {
            $interest->item->status = 'reserved';
            $interest->item->save();
        }

        return redirect()->back()->with('success', 'Estado de interés actualizado correctamente.');
    }
}
