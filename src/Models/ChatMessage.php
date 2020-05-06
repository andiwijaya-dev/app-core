<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Andiwijaya\AppCore\Events\ChatEvent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ChatDiscussion extends Model
{
  use LoggedTraitV3;

  protected $table = 'chat_discussion';

  protected $fillable = [ 'status', 'avatar_image_url', 'key', 'name', 'title', 'extra', 'unreplied_count', 'last_replied_at' ];

  protected $attributes = [
    'status'=>self::STATUS_OPEN,
    'unreplied_count'=>0
  ];

  protected $casts = [
    'extra'=>'array'
  ];

  const STATUS_OPEN = 1;
  const STATUS_CLOSED = -1;

  public function messages()
  {
    return $this->hasMany('Andiwijaya\AppCore\Models\ChatMessage', 'discussion_id', 'id');
  }

  public function getLatestMessagesAttribute(){

    return ChatMessage::whereDiscussionId($this->id)
      ->orderBy('created_at', 'desc')
      ->limit(5)
      ->get()
      ->reverse();

  }

  public function getLatestMessageAttribute(){

    return ChatMessage::whereDiscussionId($this->id)->orderBy('created_at', 'desc')->first();

  }


  public function end(){

    if($this->status == self::STATUS_CLOSED)
      exc(__('models.chat-discussion-already-closed'));

    try{
      $this->status = self::STATUS_CLOSED;

      parent::save();
    }
    catch(\Exception $ex){
      exc(__('models.chat-discussion-error', [ 'message'=>$ex->getMessage() ]));
    }

  }

  public function postSave()
  {
    event(new ChatEvent($this->wasRecentlyCreated ? ChatEvent::TYPE_NEW_CHAT : ChatEvent::TYPE_UPDATE_CHAT, $this));
  }

  public function calculate()
  {
    $last_replied = ChatMessage::where([
      'discussion_id'=>$this->id,
      'direction'=>ChatMessage::DIRECTION_OUT
    ])
      ->orderBy('created_at', 'desc')
      ->first();

    $this->unreplied_count = isset($last_replied->id) ?
      ChatMessage::whereDiscussionId($this->id)->where('id', '>', $last_replied->id)->where('direction', ChatMessage::DIRECTION_IN)->count() :
      ChatMessage::whereDiscussionId($this->id)->where('direction', ChatMessage::DIRECTION_IN)->count();

    parent::save();
  }

}
