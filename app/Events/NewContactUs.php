<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class NewContactUs implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $contact;

    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    public function broadcastOn()
    {
        return new Channel('contact-channel');
    }

    public function broadcastAs()
    {
        return 'new-contact';
    }

    // Force clean JSON
    public function broadcastWith()
    {
        return [
            'name' => $this->contact['name'] ?? 'Guest',
            'email' => $this->contact['email'] ?? null,
        ];
    }
}

