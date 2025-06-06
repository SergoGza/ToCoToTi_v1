<?php

namespace App\Http\Controllers;

use App\Models\Request as ItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Services\MatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;



class RequestController extends Controller
{
    protected $matchingService;

    public function __construct(MatchingService $matchingService = null)
    {
        $this->matchingService = $matchingService ?: new MatchingService();
    }

    public function index(Request $request)
    {
        $query = ItemRequest::with(['user', 'category'])
            ->where('status', 'active');

        // Filtro por categoría
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
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

        $allowedSortFields = ['title', 'created_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->latest(); // Orden por defecto
        }

        $requests = $query->paginate(9)->withQueryString();

        $categories = Category::all();

        return Inertia::render('Requests/Index', [
            'requests' => $requests,
            'filters' => [
                'search' => $request->search,
                'category' => $request->category,
                'location' => $request->location,
                'sort_by' => $sortField,
                'sort_order' => $sortOrder
            ],
            'categories' => $categories
        ]);
    }

    public function create()
    {
        $categories = Category::all();

        return Inertia::render('Requests/Create', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'location' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $itemRequest = new ItemRequest();
        $itemRequest->user_id = Auth::id();
        $itemRequest->title = $request->title;
        $itemRequest->description = $request->description;
        $itemRequest->category_id = $request->category_id;
        $itemRequest->location = $request->location;
        $itemRequest->expires_at = $request->expires_at;
        $itemRequest->status = 'active';

        $itemRequest->save();

        // Buscar ítems que coincidan con esta nueva solicitud
        $matchingItems = $this->matchingService->findMatchingItems($itemRequest);

        // Redirigir y mostrar un mensaje sobre ítems coincidentes si los hay
        if ($matchingItems->count() > 0) {
            return redirect()->route('requests.show', $itemRequest->id)
                ->with('success', "Solicitud creada. ¡Encontramos {$matchingItems->count()} ítems que podrían interesarte!");
        }

        return redirect()->route('requests.index')->with('success', 'Solicitud creada correctamente');
    }

    public function show(ItemRequest $request)
    {
        // Cargamos las relaciones necesarias
        $request->load(['user', 'category']);

        // Verificamos si hay items que coincidan con la categoría de la solicitud
        $matchingItems = $this->matchingService->findMatchingItems($request);

        return Inertia::render('Requests/Show', [
            'request' => $request,
            'matchingItems' => $matchingItems->take(5), // Limitamos a 5 resultados
            'isOwner' => Auth::id() === $request->user_id
        ]);
    }

    public function edit(ItemRequest $request)
    {
        // Verificar que el usuario actual es el propietario
        if (Auth::id() !== $request->user_id) {
            return redirect()->route('requests.index')->with('error', 'No tienes permiso para editar esta solicitud');
        }

        $categories = Category::all();

        return Inertia::render('Requests/Edit', [
            'request' => $request,
            'categories' => $categories
        ]);
    }

    public function update(Request $httpRequest, ItemRequest $request)
    {
        // Verificar que el usuario actual es el propietario
        if (Auth::id() !== $request->user_id) {
            return redirect()->route('requests.index')->with('error', 'No tienes permiso para editar esta solicitud');
        }

        $httpRequest->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'location' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date',
            'status' => 'sometimes|in:active,fulfilled,cancelled'
        ]);

        $request->title = $httpRequest->title;
        $request->description = $httpRequest->description;
        $request->category_id = $httpRequest->category_id;
        $request->location = $httpRequest->location;
        $request->expires_at = $httpRequest->expires_at;

        // Actualizar estado si se proporciona
        if ($httpRequest->has('status')) {
            $request->status = $httpRequest->status;
        }

        $request->save();

        // Si la solicitud sigue activa y se modificaron campos clave, buscar nuevas coincidencias
        if ($request->status === 'active' &&
            ($request->wasChanged('title') ||
                $request->wasChanged('description') ||
                $request->wasChanged('category_id'))) {

            $matchingItems = $this->matchingService->findMatchingItems($request);

            if ($matchingItems->count() > 0) {
                return redirect()->route('requests.show', $request->id)
                    ->with('success', "Solicitud actualizada. ¡Encontramos {$matchingItems->count()} ítems que podrían interesarte!");
            }
        }

        return redirect()->route('requests.show', $request->id)->with('success', 'Solicitud actualizada correctamente');
    }

    public function destroy(ItemRequest $request)
    {
        // Verificar que el usuario actual es el propietario
        if (Auth::id() !== $request->user_id) {
            return redirect()->route('requests.index')->with('error', 'No tienes permiso para eliminar esta solicitud');
        }

        $request->delete();

        return redirect()->route('requests.index')->with('success', 'Solicitud eliminada correctamente');
    }

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
