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

  protected $fillable = [ 'chat_id', 'unread', 'direction', 'from_id', 'to_id', 'topic', 'message', 'extra' ];

  protected $attributes = [
    'unread'=>1
  ];

  protected $casts = [
    'topic'=>'array',
    'extra'=>'array'
  ];

  const DIRECTION_IN = 1;
  const DIRECTION_OUT = 2;

  public function chat()
  {
    return $this->belongsTo('Andiwijaya\AppCore\Models\Chat', 'chat_id', 'id');
  }

  public function preSave()
  {
    $validator = Validator::make($this->attributes,
      [
        'message'=>'required:min:1'
      ],
      [
        'message.required'=>'Pesan harus diisi'
      ]
    );
    if($validator->fails()) throw new \Exception($validator->errors()->first());


    if(isset($this->fill_attributes['images'])){

      $images = [];
      foreach($this->fill_attributes['images'] as $image)
        $images[] = save_image($image);

      $extra = $this->extra;
      $extra['images'] = $images;
      $this->extra = $extra;

    }

  }

  public function postSave()
  {
    event(new ChatEvent(ChatEvent::TYPE_NEW_CHAT_MESSAGE, $this->chat, $this));
    event(new ChatEvent(ChatEvent::TYPE_UPDATE_CHAT, $this->chat));
  }

  public function calculate()
  {
    $this->chat->calculate();
  }

}
