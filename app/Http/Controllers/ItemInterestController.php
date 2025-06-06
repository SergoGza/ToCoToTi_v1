<?php

namespace App\Http\Controllers;

use App\Models\ItemInterest;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\NotificationService;

class ItemInterestController extends Controller
{
    public function index(Request $request)
    {
        $query = ItemInterest::with(['item.user', 'item.category'])
            ->where('user_id', Auth::id());

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por categoría de item
        if ($request->filled('category')) {
            $query->whereHas('item', function($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        // Búsqueda por título o descripción del item
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->whereHas('item', function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm);
            });
        }

        // Ordenación
        $query->latest();

        $interests = $query->paginate(10)->withQueryString();

        $categories = Category::all();

        return Inertia::render('Interests/Index', [
            'interests' => $interests,
            'filters' => [
                'search' => $request->search,
                'category' => $request->category,
                'status' => $request->status
            ],
            'categories' => $categories
        ]);
    }

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

        // Crear notificación para el propietario del item
        NotificationService::createInterestReceivedNotification($interest);

        return redirect()->back()->with('success', 'Has mostrado interés en este item. El propietario será notificado.');
    }

    public function update(Request $request, ItemInterest $interest)
    {
        // Verificar que el usuario es el propietario del item
        if (Auth::id() !== $interest->item->user_id) {
            return redirect()->back()->with('error', 'No tienes permiso para actualizar este interés.');
        }

        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        // Guardar el estado anterior para la notificación
        $oldStatus = $interest->status;

        // Actualizar el estado
        $interest->status = $request->status;
        $interest->save();

        // Si se acepta el interés, actualizamos el estado del item
        if ($request->status === 'accepted') {
            $interest->item->status = 'reserved';
            $interest->item->save();

            // Notificar a otros usuarios interesados que el item ha sido reservado
            NotificationService::createItemReservedNotification($interest->item);
        }

        // Crear notificación para el usuario interesado
        NotificationService::createInterestStatusNotification($interest, $oldStatus);

        return redirect()->back()->with('success', 'Estado de interés actualizado correctamente.');
    }

    public function receivedInterests(Request $request)
    {
        // Obtener todos los ítems del usuario actual
        $items = Item::where('user_id', Auth::id())->pluck('id');

        // Construir la consulta para obtener intereses en esos ítems
        $query = ItemInterest::whereIn('item_id', $items)
            ->with(['user', 'item.category']);

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por ítem específico
        if ($request->filled('item')) {
            $query->where('item_id', $request->item);
        }

        // Ordenación
        $query->latest();

        $interests = $query->paginate(10)->withQueryString();

        // Obtener lista de items únicos para el filtro
        $uniqueItems = Item::where('user_id', Auth::id())
            ->whereHas('interests')
            ->select('id', 'title')
            ->get();

        return Inertia::render('Interests/Received', [
            'interests' => $interests,
            'filters' => [
                'status' => $request->status,
                'item' => $request->item
            ],
            'uniqueItems' => $uniqueItems
        ]);
    }
}
