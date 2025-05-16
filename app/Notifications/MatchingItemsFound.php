<?php

namespace App\Notifications;

use App\Models\Request as ItemRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class MatchingItemsFound extends Notification
{
    use Queueable;

    protected $items;
    protected $request;

    /**
     * Create a new notification instance.
     */
    public function __construct(Collection $items, ItemRequest $request)
    {
        $this->items = $items;
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $itemCount = $this->items->count();

        $mailMessage = (new MailMessage)
            ->subject("Encontramos {$itemCount} " . ($itemCount == 1 ? 'ítem' : 'ítems') . " para tu solicitud")
            ->line("Hemos encontrado {$itemCount} " . ($itemCount == 1 ? 'ítem que coincide' : 'ítems que coinciden') . " con tu solicitud:")
            ->line('"' . $this->request->title . '"')
            ->line('');

        // Incluir los primeros 3 ítems en el correo
        foreach ($this->items->take(3) as $item) {
            $mailMessage->line('• ' . $item->title);
        }

        if ($itemCount > 3) {
            $mailMessage->line('... y ' . ($itemCount - 3) . ' más.');
        }

        return $mailMessage
            ->action('Ver ítems coincidentes', url('/requests/' . $this->request->id))
            ->line('¡Gracias por usar ToCoToTi!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $itemCount = $this->items->count();

        return [
            'type' => 'matching_items',
            'message' => "Encontramos {$itemCount} " . ($itemCount == 1 ? 'ítem' : 'ítems') . " para tu solicitud: \"{$this->request->title}\"",
            'link' => route('requests.show', $this->request->id),
            'related_id' => $this->request->id,
            'related_type' => 'request',
            'items_count' => $itemCount,
            'first_item_id' => $this->items->first()->id
        ];
    }
}
