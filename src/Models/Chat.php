<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Andiwijaya\AppCore\Events\ChatEvent;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
  use LoggedTraitV3;

  protected $table = 'chat';

  protected $fillable = [ 'image_url', 'title', 'extra', 'status', 'unread_count', 'last_message_at' ];

  protected $attributes = [
    'status'=>self::STATUS_OPEN,
    'unread_count'=>0
  ];

  protected $casts = [
    'extra'=>'array'
  ];

  const STATUS_OPEN = 1;
  const STATUS_CLOSED = -1;

  public function messages()
  {
    return $this->hasMany('Andiwijaya\AppCore\Models\ChatMessage', 'chat_id', 'id');
  }


  public function getLatestMessageAttribute(){

    return $this->messages->sortByDesc('created_at')->first();

  }

  public function getLatestMessagesAttribute(){

    return $this->messages->sortByDesc('created_at')->take(10)->sortBy('created_at');

  }

  public function postSave()
  {
    event(new ChatEvent($this->wasRecentlyCreated ? ChatEvent::TYPE_NEW_CHAT : ChatEvent::TYPE_UPDATE_CHAT, $this));
  }

  public function calculate()
  {
    $this->unread_count = $this->messages->sum('unread');

    $this->last_message_at = isset(($last_message = $this->messages->sortByDesc('created_at')->first())->created_at) ?
      $last_message->created_at : null;

    parent::save();
  }

}
