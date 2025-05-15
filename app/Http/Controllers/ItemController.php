<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource with search and filtering.
     */
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

    /**
     * Show the form for creating a new resource.
     */
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

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        // Cargamos las relaciones necesarias
        $item->load(['user', 'category', 'interests.user']);

        // Verificamos si el usuario actual ha mostrado interés
        $hasInterest = false;
        if (Auth::check()) {
            $hasInterest = $item->interests->contains('user_id', Auth::id());
        }

        return Inertia::render('Items/Show', [
            'item' => $item,
            'hasInterest' => $hasInterest,
            'isOwner' => Auth::id() === $item->user_id
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
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
}
