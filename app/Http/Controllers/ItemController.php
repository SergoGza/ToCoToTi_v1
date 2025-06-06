<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\ItemInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;
use App\Services\MatchingService;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['user', 'category'])
            ->where('status', 'available');

        // Filtro por categoría
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filtro por condición
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // Búsqueda por término
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhere('location', 'like', $searchTerm);
            });
        }

        // Filtro por ubicación
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Ordenar resultados
        $sortField = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $allowedSortFields = ['title', 'created_at', 'condition'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->latest(); // Orden por defecto
        }

        $items = $query->paginate(9)->withQueryString();

        $categories = Category::all();
        $conditions = [
            ['value' => 'nuevo', 'label' => 'Nuevo'],
            ['value' => 'como_nuevo', 'label' => 'Como nuevo'],
            ['value' => 'buen_estado', 'label' => 'Buen estado'],
            ['value' => 'usado', 'label' => 'Usado'],
            ['value' => 'necesita_reparacion', 'label' => 'Necesita reparación']
        ];

        return Inertia::render('Items/Index', [
            'items' => $items,
            'filters' => [
                'search' => $request->search,
                'category' => $request->category,
                'condition' => $request->condition,
                'location' => $request->location,
                'sort_by' => $sortField,
                'sort_order' => $sortOrder
            ],
            'categories' => $categories,
            'conditions' => $conditions
        ]);
    }

    public function create()
    {
        $categories = Category::all();

        $conditions = [
            ['value' => 'nuevo', 'label' => 'Nuevo'],
            ['value' => 'como_nuevo', 'label' => 'Como nuevo'],
            ['value' => 'buen_estado', 'label' => 'Buen estado'],
            ['value' => 'usado', 'label' => 'Usado'],
            ['value' => 'necesita_reparacion', 'label' => 'Necesita reparación']
        ];

        return Inertia::render('Items/Create', [
            'categories' => $categories,
            'conditions' => $conditions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'condition' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'location' => 'nullable|string|max:255',
        ]);

        $item = new Item();
        $item->user_id = Auth::id();
        $item->title = $request->title;
        $item->description = $request->description;
        $item->condition = $request->condition;
        $item->category_id = $request->category_id;
        $item->location = $request->location;
        $item->status = 'available';

        // Manejo de imágenes
        if ($request->hasFile('images')) {
            $imagesPaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('items', 'public');
                $imagesPaths[] = $path;
            }
            $item->images = $imagesPaths;
        }

        $item->save();

        return redirect()->route('items.index')->with('success', 'Item creado correctamente');
    }

    public function show(Item $item, MatchingService $matchingService)
    {
        // Cargamos las relaciones necesarias
        $item->load(['user', 'category', 'interests.user']);

        // Verificamos si el usuario actual ha mostrado interés
        $hasInterest = false;
        if (Auth::check()) {
            $hasInterest = $item->interests->contains('user_id', Auth::id());
        }

        // Buscar solicitudes que coincidan con este ítem
        $matchingRequests = $matchingService->findMatchingRequests($item);

        return Inertia::render('Items/Show', [
            'item' => $item,
            'hasInterest' => $hasInterest,
            'isOwner' => Auth::id() === $item->user_id,
            'matchingRequests' => $matchingRequests->take(3) // Limitamos a 3 resultados
        ]);
    }

    public function edit(Item $item)
    {
        // Verificar que el usuario actual es el propietario
        if (Auth::id() !== $item->user_id) {
            return redirect()->route('items.index')->with('error', 'No tienes permiso para editar este item');
        }

        $categories = Category::all();
        $conditions = [
            ['value' => 'nuevo', 'label' => 'Nuevo'],
            ['value' => 'como_nuevo', 'label' => 'Como nuevo'],
            ['value' => 'buen_estado', 'label' => 'Buen estado'],
            ['value' => 'usado', 'label' => 'Usado'],
            ['value' => 'necesita_reparacion', 'label' => 'Necesita reparación']
        ];

        return Inertia::render('Items/Edit', [
            'item' => $item,
            'categories' => $categories,
            'conditions' => $conditions
        ]);
    }

    public function update(Request $request, Item $item)
    {
        // Verificar que el usuario actual es el propietario
        if (Auth::id() !== $item->user_id) {
            return redirect()->route('items.index')->with('error', 'No tienes permiso para editar este item');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'condition' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'location' => 'nullable|string|max:255',
            'status' => 'sometimes|in:available,reserved,given'
        ]);

        $item->title = $request->title;
        $item->description = $request->description;
        $item->condition = $request->condition;
        $item->category_id = $request->category_id;
        $item->location = $request->location;

        // Actualizar estado si se proporciona
        if ($request->has('status')) {
            $item->status = $request->status;
        }

        // Manejo de imágenes
        if ($request->hasFile('images')) {
            $imagesPaths = $item->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('items', 'public');
                $imagesPaths[] = $path;
            }
            $item->images = $imagesPaths;
        }

        // Si se solicita eliminar una imagen específica
        if ($request->has('remove_image') && is_numeric($request->remove_image)) {
            $images = $item->images ?? [];
            if (isset($images[$request->remove_image])) {
                // Eliminar el archivo del almacenamiento
                Storage::disk('public')->delete($images[$request->remove_image]);
                // Eliminar la entrada del array
                array_splice($images, $request->remove_image, 1);
                $item->images = $images;
            }
        }

        $item->save();

        return redirect()->route('items.show', $item->id)->with('success', 'Item actualizado correctamente');
    }

    public function destroy(Item $item)
    {
        // Verificar que el usuario actual es el propietario
        if (Auth::id() !== $item->user_id) {
            return redirect()->route('items.index')->with('error', 'No tienes permiso para eliminar este item');
        }

        // Eliminar imágenes del almacenamiento
        if ($item->images) {
            foreach ($item->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item eliminado correctamente');
    }

    public function updateStatus(Request $request, Item $item)
    {
        // Verificar que el usuario actual es el propietario
        if (Auth::id() !== $item->user_id) {
            return redirect()->back()->with('error', 'No tienes permiso para modificar este item');
        }

        $request->validate([
            'status' => 'required|in:available,reserved,given',
            'interest_id' => 'nullable|exists:item_interests,id',
        ]);

        $oldStatus = $item->status;
        $item->status = $request->status;

        // Si cambia a disponible, asegurar que no haya intereses aceptados
        if ($request->status === 'available' && $oldStatus === 'reserved') {
            // Buscar intereses aceptados
            $acceptedInterests = $item->interests()->where('status', 'accepted')->get();

            // Cambiar intereses aceptados a pendientes
            foreach ($acceptedInterests as $interest) {
                $interest->status = 'pending';
                $interest->save();

                // Notificar a los usuarios
                // Podríamos crear una notificación específica de "item vuelve a estar disponible"
            }
        }

        // Si cambia a reservado, actualizar el interés seleccionado
        if ($request->status === 'reserved' && $request->interest_id) {
            $selectedInterest = ItemInterest::findOrFail($request->interest_id);

            // Verificar que el interés pertenece al item
            if ($selectedInterest->item_id !== $item->id) {
                return redirect()->back()->with('error', 'El interés seleccionado no pertenece a este item');
            }

            // Aceptar el interés seleccionado
            $selectedInterest->status = 'accepted';
            $selectedInterest->save();

            // Rechazar los demás intereses
            $item->interests()
                ->where('id', '!=', $selectedInterest->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            // Notificar al usuario seleccionado
            NotificationService::createInterestAcceptedNotification($selectedInterest);

            // Notificar a los demás usuarios
            NotificationService::createItemReservedForOthersNotification($item, $selectedInterest->user_id);
        }

        // Si cambia a entregado, notificar al usuario al que fue entregado
        if ($request->status === 'given' && $oldStatus === 'reserved') {
            $acceptedInterest = $item->interests()->where('status', 'accepted')->first();

            if ($acceptedInterest) {
                NotificationService::createItemGivenNotification($item, $acceptedInterest->user_id);
            }
        }

        $item->save();

        return redirect()->back()->with('success', "Estado del item actualizado a '{$this->getStatusText($request->status)}'");
    }

    private function getStatusText($status)
    {
        $statusMap = [
            'available' => 'Disponible',
            'reserved' => 'Reservado',
            'given' => 'Entregado'
        ];

        return $statusMap[$status] ?? $status;
    }



    /*
     * Mostrar los items del usuario autenticado (Todo Tiro)
     */
    public function myItems(Request $request)
    {
        $user = auth()->user();

        // Obtener filtros
        $search = $request->get('search');
        $categoryId = $request->get('category');
        $status = $request->get('status');

        // Query base para los items del usuario
        $query = Item::where('user_id', $user->id)
            ->with(['category', 'user'])
            ->withCount('interests');

        // Aplicar filtros
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Obtener items paginados
        $items = $query->latest()->paginate(10)->withQueryString();

        // Estadísticas del usuario
        $stats = [
            'total' => Item::where('user_id', $user->id)->count(),
            'available' => Item::where('user_id', $user->id)->where('status', 'available')->count(),
            'reserved' => Item::where('user_id', $user->id)->where('status', 'reserved')->count(),
            'given' => Item::where('user_id', $user->id)->where('status', 'given')->count(),
        ];

        // Obtener categorías para el filtro
        $categories = Category::orderBy('name')->get();

        return Inertia::render('Items/MyItems', [
            'items' => $items,
            'stats' => $stats,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'category' => $categoryId,
                'status' => $status
            ]
        ]);
    }

}
