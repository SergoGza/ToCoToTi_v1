<?php

namespace App\Http\Controllers;

use App\Events\NewMessageEvent;
use App\Models\Conversation;
use App\Models\Item;
use App\Models\ItemInterest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MessageController extends Controller
{
    /**
     * Mostrar listado de conversaciones del usuario.
     */
    public function index()
    {
        $user = Auth::user();

        // Obtener todas las conversaciones del usuario con el último mensaje
        $conversations = Conversation::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->with(['user1', 'user2', 'latestMessage'])
            ->latest('last_message_at')
            ->get()
            ->map(function ($conversation) use ($user) {
                // Obtener el otro usuario de la conversación
                $otherUser = $conversation->user1_id == $user->id
                    ? $conversation->user2
                    : $conversation->user1;

                // Contar mensajes no leídos
                $unreadCount = $conversation->unreadMessages($user->id)->count();

                return [
                    'id' => $conversation->id,
                    'title' => $conversation->title ?? $otherUser->name,
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                    ],
                    'latest_message' => $conversation->latestMessage,
                    'unread_count' => $unreadCount,
                    'last_message_at' => $conversation->last_message_at,
                ];
            });

        return Inertia::render('Messages/Index', [
            'conversations' => $conversations
        ]);
    }

    /**
     * Mostrar una conversación específica.
     */
    public function show(Conversation $conversation)
    {
        $user = Auth::user();

        // Verificar que el usuario es parte de la conversación
        if ($conversation->user1_id !== $user->id && $conversation->user2_id !== $user->id) {
            return redirect()->route('messages.index')->with('error', 'No tienes permiso para ver esta conversación');
        }

        // Obtener al otro usuario
        $otherUser = $conversation->user1_id == $user->id
            ? $conversation->user2
            : $conversation->user1;

        // Cargar mensajes
        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Marcar mensajes como leídos
        $conversation->unreadMessages($user->id)
            ->update(['read' => true]);

        return Inertia::render('Messages/Show', [
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title ?? $otherUser->name,
                'other_user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                ],
            ],
            'messages' => $messages
        ]);
    }

    /**
     * Enviar un mensaje en una conversación.
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $user = Auth::user();

        // Verificar que el usuario es parte de la conversación
        if ($conversation->user1_id !== $user->id && $conversation->user2_id !== $user->id) {
            return redirect()->route('messages.index')->with('error', 'No tienes permiso para enviar mensajes en esta conversación');
        }

        // Crear mensaje
        $message = new Message([
            'content' => $request->content,
            'user_id' => $user->id,
        ]);

        $conversation->messages()->save($message);

        // Actualizar timestamp de último mensaje
        $conversation->last_message_at = now();
        $conversation->save();

        // Emitir evento
//        broadcast(new NewMessageEvent($message))->toOthers();

        return back();
    }

    /**
     * Crear o acceder a una conversación con otro usuario.
     */
    public function startConversation(User $otherUser)
    {
        $user = Auth::user();

        // No permitir conversaciones con uno mismo
        if ($user->id === $otherUser->id) {
            return redirect()->route('messages.index')->with('error', 'No puedes iniciar una conversación contigo mismo');
        }

        // Buscar si ya existe una conversación entre estos usuarios
        $conversation = $user->getConversationWith($otherUser->id);

        // Si no existe, crear una nueva
        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => $user->id,
                'user2_id' => $otherUser->id,
                'last_message_at' => now(),
            ]);
        }

        return redirect()->route('messages.show', $conversation->id);
    }

    /**
     * Iniciar conversación desde un ítem.
     */
    public function fromItem(Item $item)
    {
        $user = Auth::user();

        // Verificar que el usuario no es el propietario del ítem
        if ($user->id === $item->user_id) {
            return redirect()->route('items.show', $item->id)->with('error', 'No puedes contactar contigo mismo');
        }

        // Buscar o crear conversación con el propietario del ítem
        $otherUser = User::find($item->user_id);
        $conversation = $user->getConversationWith($otherUser->id);

        if (!$conversation) {
            $conversation = Conversation::create([
                'title' => "Sobre: {$item->title}",
                'user1_id' => $user->id,
                'user2_id' => $otherUser->id,
                'last_message_at' => now(),
            ]);

            // Crear mensaje inicial automático
            $message = new Message([
                'content' => "Hola, estoy interesado en tu item: {$item->title}",
                'user_id' => $user->id,
            ]);

            $conversation->messages()->save($message);

            // Emitir evento
//            broadcast(new NewMessageEvent($message))->toOthers();
        }

        return redirect()->route('messages.show', $conversation->id);
    }

    /**
     * Iniciar conversación desde un interés.
     */
    public function fromInterest(ItemInterest $interest)
    {
        $user = Auth::user();
        $item = $interest->item;

        // Verificar que el usuario es el propietario del ítem
        if ($user->id !== $item->user_id) {
            return redirect()->route('interests.received')->with('error', 'No tienes permiso para contactar a esta persona');
        }

        // Buscar o crear conversación con el usuario interesado
        $otherUser = User::find($interest->user_id);
        $conversation = $user->getConversationWith($otherUser->id);

        if (!$conversation) {
            $conversation = Conversation::create([
                'title' => "Sobre: {$item->title}",
                'user1_id' => $user->id,
                'user2_id' => $otherUser->id,
                'last_message_at' => now(),
            ]);

            // Crear mensaje inicial automático
            $message = new Message([
                'content' => "Hola, soy el propietario del item '{$item->title}' en el que mostraste interés.",
                'user_id' => $user->id,
            ]);

            $conversation->messages()->save($message);

            // Emitir evento
//            broadcast(new NewMessageEvent($message))->toOthers();
        }

        return redirect()->route('messages.show', $conversation->id);
    }
}
