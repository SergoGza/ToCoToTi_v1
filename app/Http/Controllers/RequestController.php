<?php

namespace App\Http\Controllers;

use App\Models\Request as ItemRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requests = ItemRequest::with(['user', 'category'])
            ->where('status', 'active')
            ->latest()
            ->paginate(10);

        return Inertia::render('Requests/Index', [
            'requests' => $requests
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return Inertia::render('Requests/Create', [
            'categories' => $categories
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
            'category_id' => 'required|exists:categories,id',
            'location' => 'nullable|string|max:255',
        ]);

        $itemRequest = new ItemRequest();
        $itemRequest->user_id = Auth::id();
        $itemRequest->title = $request->title;
        $itemRequest->description = $request->description;
        $itemRequest->category_id = $request->category_id;
        $itemRequest->location = $request->location;
        $itemRequest->status = 'active';

        // Si se proporciona una fecha de expiración
        if ($request->expires_at) {
            $itemRequest->expires_at = $request->expires_at;
        }

        $itemRequest->save();

        return redirect()->route('requests.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemRequest $request)
    {
        // Cargamos las relaciones necesarias
        $request->load(['user', 'category']);

        // Verificamos si hay items que coincidan con la categoría de la solicitud
        $matchingItems = \App\Models\Item::where('category_id', $request->category_id)
            ->where('status', 'available')
            ->where('user_id', '!=', Auth::id()) // No mostrar los propios items del usuario
            ->with('user')
            ->take(5)
            ->get();

        return Inertia::render('Requests/Show', [
            'request' => $request,
            'matchingItems' => $matchingItems,
            'isOwner' => Auth::id() === $request->user_id
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ItemRequest $request)
    {
        // Verificar que el usuario actual es el propietario
        if (Auth::id() !== $request->user_id) {
            return redirect()->route('requests.index');
        }

        $categories = Category::all();

        return Inertia::render('Requests/Edit', [
            'request' => $request,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $httpRequest, ItemRequest $request)
    {
        // Verificar que el usuario actual es el propietario
        if (Auth::id() !== $request->user_id) {
            return redirect()->route('requests.index');
        }

        $httpRequest->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'location' => 'nullable|string|max:255',
            'status' => 'sometimes|in:active,fulfilled,cancelled'
        ]);

        $request->title = $httpRequest->title;
        $request->description = $httpRequest->description;
        $request->category_id = $httpRequest->category_id;
        $request->location = $httpRequest->location;

        // Actualizar estado si se proporciona
        if ($httpRequest->has('status')) {
            $request->status = $httpRequest->status;
        }

        $request->save();

        return redirect()->route('requests.show', $request->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemRequest $request)
    {
        // Verificar que el usuario actual es el propietario
        if (Auth::id() !== $request->user_id) {
            return redirect()->route('requests.index');
        }

        $request->delete();

        return redirect()->route('requests.index');
    }

    /**
     * Listar las solicitudes del usuario autenticado.
     */
    public function myRequests()
    {
        $requests = ItemRequest::where('user_id', Auth::id())
            ->with('category')
            ->latest()
            ->paginate(10);

        return Inertia::render('Requests/MyRequests', [
            'requests' => $requests
        ]);
    }
}
