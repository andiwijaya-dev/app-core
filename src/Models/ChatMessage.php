<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Andiwijaya\AppCore\Events\ChatEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ChatMessage extends Model
{
  use LoggedTraitV3;

  protected $table = 'chat_message';

  protected $fillable = [ 'discussion_id', 'unread', 'direction', 'from_id', 'to_id', 'text', 'images', 'extra' ];

  protected $attributes = [
    'unread'=>1,
    'unsent'=>1
  ];

  protected $casts = [
    'images'=>'array',
    'extra'=>'array'
  ];

  const DIRECTION_IN = 1;
  const DIRECTION_OUT = 2;

  public function discussion()
  {
    return $this->belongsTo('Andiwijaya\AppCore\Models\ChatDiscussion', 'discussion_id', 'id');
  }


  public function getHasPrevAttribute(){

    return ChatMessage::whereDiscussionId($this->discussion_id)
      ->where('created_at', '<', $this->created_at)
      ->count() > 0 ? true : false;

  }

  public function getPreviousMessagesAttribute(){

    return ChatMessage::whereDiscussionId($this->discussion_id)
      ->where('created_at', '<', $this->created_at)
      ->orderBy('created_at')
      ->limit(10)
      ->get();

  }

  public function preSave()
  {
    if(isset($this->fill_attributes['images'])){

      $images = [];
      foreach($this->fill_attributes['images'] as $image)
        $images[] = save_image($image);

      $this->images = $images;

    }

    $extra = array_merge($this->extra ?? [], [ 'user'=>User::find(Session::get('user_id'))->name ?? '', 'user_id'=>Session::get('user_id') ]);
    $this->extra = $extra;

  }

  public function postSave()
  {
    if($this->wasRecentlyCreated){
      event(new ChatEvent(ChatEvent::TYPE_NEW_CHAT_MESSAGE, $this->discussion, $this));
      event(new ChatEvent(ChatEvent::TYPE_UPDATE_CHAT, $this->discussion));
    }
  }

  public function calculate()
  {
    $this->discussion->calculate();
  }

}
