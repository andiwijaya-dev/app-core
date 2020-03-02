<?php

namespace Andiwijaya\AppCore\Events;

use Andiwijaya\AppCore\Models\ChatDiscussion;
use Andiwijaya\AppCore\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChatEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $discussion;
    public $message;
    public $type;

    const TYPE_NEW_CHAT = 1;
    const TYPE_UPDATE_CHAT = 2;
    const TYPE_NEW_CHAT_MESSAGE = 3;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($type, ChatDiscussion $discussion, ChatMessage $message = null)
    {
      $this->type = $type;
      $this->discussion = $discussion;
      $this->message = $message;
    }

}
