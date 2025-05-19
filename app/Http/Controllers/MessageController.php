<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Message;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class MessageController extends Controller
{
    /**
     * Mostrar bandeja de entrada con todas las conversaciones.
     */
    public function index()
    {
        $user = Auth::user();

        // Obtenemos usuarios con los que se ha conversado
        $conversations = DB::table('messages')
            ->select(
                DB::raw('CASE
                    WHEN sender_id = ' . $user->id . ' THEN receiver_id
                    ELSE sender_id
                END as contact_id'),
                DB::raw('MAX(created_at) as last_message_at')
            )
            ->where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->groupBy('contact_id')
            ->orderByDesc('last_message_at')
            ->get();

        $contactIds = $conversations->pluck('contact_id')->toArray();
        $contacts = User::whereIn('id', $contactIds)->get()->keyBy('id');

        // Datos para mostrar resumen de conversaciones
        $conversationData = [];
        foreach ($conversations as $conversation) {
            $contactId = $conversation->contact_id;
            if (isset($contacts[$contactId])) {
                $contact = $contacts[$contactId];

                // Último mensaje para mostrar en la vista previa
                $lastMessage = Message::where(function($query) use ($user, $contactId) {
                    $query->where(function($q) use ($user, $contactId) {
                        $q->where('sender_id', $user->id)
                            ->where('receiver_id', $contactId);
                    })
                        ->orWhere(function($q) use ($user, $contactId) {
                            $q->where('sender_id', $contactId)
                                ->where('receiver_id', $user->id);
                        });
                })
                    ->latest()
                    ->first();

                // Contar mensajes no leídos
                $unreadCount = Message::where('sender_id', $contactId)
                    ->where('receiver_id', $user->id)
                    ->where('read', false)
                    ->count();

                // Para la interfaz necesitamos el ítem relacionado cuando existe
                $item = null;
                if ($lastMessage && $lastMessage->item_id) {
                    $item = Item::find($lastMessage->item_id);
                }

                $conversationData[] = [
                    'contact' => [
                        'id' => $contact->id,
                        'name' => $contact->name
                    ],
                    'last_message' => [
                        'content' => $lastMessage ? substr($lastMessage->content, 0, 50) . (strlen($lastMessage->content) > 50 ? '...' : '') : '',
                        'date' => $lastMessage ? $lastMessage->created_at : null,
                        'is_mine' => $lastMessage && $lastMessage->sender_id === $user->id
                    ],
                    'unread_count' => $unreadCount,
                    'item' => $item ? [
                        'id' => $item->id,
                        'title' => $item->title,
                        'image' => $item->images && count($item->images) > 0 ? $item->images[0] : null
                    ] : null
                ];
            }
        }

        return Inertia::render('Messages/Index', [
            'conversations' => $conversationData
        ]);
    }

    /**
     * Mostrar los mensajes de una conversación específica.
     */
    public function show(User $contact)
    {
        $user = Auth::user();

        // Verificar que no es el mismo usuario
        if ($user->id === $contact->id) {
            return redirect()->route('messages.index')->with('error', 'No puedes enviarte mensajes a ti mismo');
        }

        // Cargar mensajes entre los dos usuarios
        $messages = Message::where(function($query) use ($user, $contact) {
            $query->where(function($q) use ($user, $contact) {
                $q->where('sender_id', $user->id)
                    ->where('receiver_id', $contact->id);
            })
                ->orWhere(function($q) use ($user, $contact) {
                    $q->where('sender_id', $contact->id)
                        ->where('receiver_id', $user->id);
                });
        })
            ->with(['item', 'interest.item'])
            ->orderBy('created_at')
            ->get();

        // Marcar mensajes como leídos
        Message::where('sender_id', $contact->id)
            ->where('receiver_id', $user->id)
            ->where('read', false)
            ->update(['read' => true, 'read_at' => now()]);

        // Buscar ítems relacionados con la conversación
        $itemsRelated = [];
        foreach ($messages as $message) {
            if ($message->item_id && !isset($itemsRelated[$message->item_id])) {
                $itemsRelated[$message->item_id] = Item::with('user')->find($message->item_id);
            }

            if ($message->interest && $message->interest->item_id && !isset($itemsRelated[$message->interest->item_id])) {
                $itemsRelated[$message->interest->item_id] = Item::with('user')->find($message->interest->item_id);
            }
        }

        // Buscar ítems en los que el contacto está interesado
        $interestsInMyItems = ItemInterest::whereHas('item', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->where('user_id', $contact->id)
            ->with('item')
            ->get();

        // Mis intereses en ítems del contacto
        $myInterestsInTheirItems = ItemInterest::whereHas('item', function($q) use ($contact) {
            $q->where('user_id', $contact->id);
        })
            ->where('user_id', $user->id)
            ->with('item')
            ->get();

        return Inertia::render('Messages/Show', [
            'contact' => $contact,
            'messages' => $messages,
            'itemsRelated' => array_values($itemsRelated),
            'interestsInMyItems' => $interestsInMyItems,
            'myInterestsInTheirItems' => $myInterestsInTheirItems
        ]);
    }

    /**
     * Obtener mensajes recientes para polling.
     * Optimizado para reducir carga en el servidor.
     */
    public function getRecentMessages(User $contact)
    {
        $user = Auth::user();
        $lastMessageId = request('last_id', 0);
        $timestamp = request('timestamp', 0);

        // Verificar el timestamp para implementar throttling (limitar frecuencia)
        $now = now()->timestamp;
        $timeDiff = $now - $timestamp;

        // Si han pasado menos de 5 segundos desde la última llamada
        // y no hay nuevos mensajes, devolver un código 304 Not Modified
        if ($timeDiff < 5) {
            // Verificar rápidamente si hay algún mensaje nuevo sin cargar todo
            $hasNewMessages = Message::where(function($query) use ($user, $contact) {
                $query->where(function($q) use ($user, $contact) {
                    $q->where('sender_id', $user->id)
                        ->where('receiver_id', $contact->id);
                })->orWhere(function($q) use ($user, $contact) {
                    $q->where('sender_id', $contact->id)
                        ->where('receiver_id', $user->id);
                });
            })
                ->where('id', '>', $lastMessageId)
                ->exists();

            if (!$hasNewMessages) {
                return response()->json([
                    'messages' => [],
                    'timestamp' => $now,
                    'throttled' => true
                ], 200);
            }
        }

        // Obtener solo mensajes más recientes que el último ID conocido por el cliente
        $messages = Message::where(function($query) use ($user, $contact) {
            $query->where(function($q) use ($user, $contact) {
                $q->where('sender_id', $user->id)
                    ->where('receiver_id', $contact->id);
            })->orWhere(function($q) use ($user, $contact) {
                $q->where('sender_id', $contact->id)
                    ->where('receiver_id', $user->id);
            });
        })
            ->where('id', '>', $lastMessageId)
            ->with(['item', 'interest.item'])
            ->orderBy('id', 'asc')
            ->get();

        // Marcar como leídos los mensajes recibidos
        if ($messages->count() > 0) {
            Message::where('sender_id', $contact->id)
                ->where('receiver_id', $user->id)
                ->where('read', false)
                ->update(['read' => true, 'read_at' => now()]);
        }

        return response()->json([
            'messages' => $messages,
            'timestamp' => $now,
            'throttled' => false
        ]);
    }

    /**
     * Enviar un nuevo mensaje.
     */
    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
            'item_id' => 'nullable|exists:items,id',
            'item_interest_id' => 'nullable|exists:item_interests,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Verificar que no es el mismo usuario
        if ($user->id == $request->receiver_id) {
            return redirect()->back()->with('error', 'No puedes enviarte mensajes a ti mismo');
        }

        // Si hay un ID de interés, verificarlo
        if ($request->filled('item_interest_id')) {
            $interest = ItemInterest::find($request->item_interest_id);

            if (!$interest) {
                return redirect()->back()->with('error', 'El interés seleccionado no existe');
            }

            // El usuario debe ser o el interesado o el propietario del ítem
            $isInterested = $interest->user_id === $user->id;
            $isItemOwner = $interest->item->user_id === $user->id;

            if (!$isInterested && !$isItemOwner) {
                return redirect()->back()->with('error', 'No tienes permiso para enviar mensajes sobre este interés');
            }
        }

        // Si hay un ID de ítem, verificarlo
        if ($request->filled('item_id')) {
            $item = Item::find($request->item_id);

            if (!$item) {
                return redirect()->back()->with('error', 'El ítem seleccionado no existe');
            }
        }

        try {
            // Crear mensaje con asignación de propiedades explícita
            $message = new Message();
            $message->sender_id = $user->id;
            $message->receiver_id = $request->receiver_id;
            $message->content = $request->content;
            $message->item_id = $request->item_id;
            $message->item_interest_id = $request->item_interest_id;
            $message->read = false;

            // Procesar imágenes si existen
            $imagesPaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('messages', 'public');
                    $imagesPaths[] = $path;
                }
                $message->images = $imagesPaths;
            }

            $message->save();

            // Notificaciones y broadcasting
            if (class_exists('App\Services\NotificationService')) {
                NotificationService::createMessageReceivedNotification($message);
            }

            // Mantener intento de broadcasting (aunque usemos polling)
            try {
                broadcast(new NewMessage($message));
            } catch (\Exception $e) {
                \Log::error('Error al emitir evento NewMessage (no crítico): ' . $e->getMessage());
            }

            // Devolver también el mensaje para actualizaciones optimistas
            return redirect()->back()->with([
                'success' => 'Mensaje enviado correctamente',
                'message' => $message->load(['item', 'interest.item'])
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al crear mensaje: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al enviar mensaje: ' . $e->getMessage());
        }
    }

    /**
     * Marcar toda la conversación como leída.
     */
    public function markAsRead(User $contact)
    {
        $user = Auth::user();

        Message::where('sender_id', $contact->id)
            ->where('receiver_id', $user->id)
            ->where('read', false)
            ->update(['read' => true, 'read_at' => now()]);

        return redirect()->back();
    }

    /**
     * Obtener la cantidad de mensajes no leídos.
     */
    public function getUnreadCount()
    {
        $count = Auth::user()->unreadMessagesCount();

        return response()->json(['count' => $count]);
    }

    // Los otros métodos se mantienen igual
    public function createFromItem(Item $item)
    {
        $user = Auth::user();
        if ($item->user_id === $user->id) {
            return redirect()->route('items.show', $item->id)->with('error', 'No puedes iniciar una conversación contigo mismo');
        }
        return redirect()->route('messages.show', $item->user_id)->with('itemContext', $item->id);
    }

    public function createFromInterest(ItemInterest $interest)
    {
        $user = Auth::user();
        $isOwner = $interest->item->user_id === $user->id;
        $isInterested = $interest->user_id === $user->id;
        if (!$isOwner && !$isInterested) {
            return redirect()->back()->with('error', 'No tienes permiso para acceder a esta conversación');
        }
        $contactId = $isOwner ? $interest->user_id : $interest->item->user_id;
        return redirect()->route('messages.show', $contactId)->with('interestContext', $interest->id);
    }
}
