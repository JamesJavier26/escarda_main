<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use SerializesModels;          // keeps the payload JSON‑able when queued

    /** @var \App\Models\Message */
    public $message;

    public function __construct(Message $message)
    {
        // Eager‑load sender so the client immediately has the user data
        $this->message = $message->load('sender');
    }

    /**
     * Every listener in the same conversation gets the event
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('conversation.' . $this->message->conversation_id);
    }

    /**
     * Optional — let Echo listen for ‘message.sent’ instead of the class name
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Optional — trim the payload to only what your UI needs
     */
    public function broadcastWith(): array
    {
        return [
            'id'           => $this->message->id,
            'body'         => $this->message->body,
            'message_type' => $this->message->message_type,
            'attachment'   => $this->message->attachment,
            'sender'       => [
                'id'       => $this->message->sender->id,
                'name'     => $this->message->sender->username,
                // 'avatar'   => $this->message->sender->avatar,   
            ],
            'created_at'   => $this->message->created_at->toIsoString(),
        ];
    }
}
