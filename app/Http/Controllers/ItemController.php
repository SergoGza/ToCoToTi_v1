<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::with(['user', 'category'])
            ->where('status', 'available')
            ->latest()
            ->paginate(10);

        return Inertia::render('Items/Index', [
            'items' => $items,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return Inertia::render('Items/Create', [
            'categories' => $categories,
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

        return redirect()->route('items.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        // Cargamos las relaciones necesarias
        $item->load(['user', 'category', 'interests']);

        return Inertia::render('Items/Show', [
            'item' => $item,
            // Solo pasamos información sobre si el usuario actual ha mostrado interés
            'hasInterest' => $item->interests->contains('user_id', Auth::id()),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        // Verificar que el usuario actual es el propietario
        if (Auth::id() !== $item->user_id) {
            return redirect()->route('items.index');
        }

        $categories = Category::all();

        return Inertia::render('Items/Edit', [
            'item' => $item,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        // Verificar que el usuario actual es el propietario
        if (Auth::id() !== $item->user_id) {
            return redirect()->route('items.index');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'condition' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'location' => 'nullable|string|max:255',
        ]);

        $item->title = $request->title;
        $item->description = $request->description;
        $item->condition = $request->condition;
        $item->category_id = $request->category_id;
        $item->location = $request->location;

        // Manejo de imágenes
        if ($request->hasFile('images')) {
            $imagesPaths = $item->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('items', 'public');
                $imagesPaths[] = $path;
            }
            $item->images = $imagesPaths;
        }

        $item->save();

        return redirect()->route('items.show', $item->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        // Verificar que el usuario actual es el propietario
        if (Auth::id() !== $item->user_id) {
            return redirect()->route('items.index');
        }

        $item->delete();

        return redirect()->route('items.index');
    }
}
