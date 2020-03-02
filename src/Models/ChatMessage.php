<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Andiwijaya\AppCore\Events\ChatEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class ChatMessage extends Model
{
  use LoggedTraitV3;

  protected $table = 'chat_message';

  protected $fillable = [ 'discussion_id', 'unread', 'direction', 'from_id', 'to_id', 'text', 'images', 'extra' ];

  protected $attributes = [
    'unread'=>1
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

  public function preSave()
  {
    if(isset($this->fill_attributes['images'])){

      $images = [];
      foreach($this->fill_attributes['images'] as $image)
        $images[] = save_image($image);

      $this->images = $images;

    }
  }

  public function postSave()
  {
    event(new ChatEvent(ChatEvent::TYPE_NEW_CHAT_MESSAGE, $this->discussion, $this));
    event(new ChatEvent(ChatEvent::TYPE_UPDATE_CHAT, $this->discussion));
  }

  public function calculate()
  {
    $this->discussion->calculate();
  }

}
