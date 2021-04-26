<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Andiwijaya\AppCore\Events\ChatEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ChatMessage extends Model
{
  use LoggedTraitV3;

  protected $table = 'chat_message';

  const TYPE_WEB = 1;
  const TYPE_WHATSAPP = 2;

  protected $fillable = [ 'type', 'discussion_id', 'session_id', 'reply_of', 'initial', 'first_reply_at', 'first_reply_after',
    'unread', 'direction', 'text', 'images', 'extra', 'notified', 'unsent',
    'context', 'context_id', 'is_bot', 'is_system', 'notified_at', 'ref_id' ];

  protected $attributes = [
    'unread'=>1,
    'unsent'=>1,
    'notified'=>0,
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


  public function getHtmlAttribute(){

    // Find link
    //https://www.google.com
    // www.google.com

    preg_match_all('/([http:\/\/|https:\/\/]*\w+\.\w+\.\w+[\.\w]*[\/]*[\S]+)/', $this->text, $matches);

    if(isset($matches[0]) && is_array($matches[0])){

      foreach($matches[0] as $link){

        $this->text = str_replace($link, "<a href=\"{$link}\">{$link}</a>", $this->text);
      }
    }


    return nl2br($this->text);
  }

  public function reply_message(){

    return $this->belongsTo(ChatMessage::class, 'reply_of');
  }


  public function getHasPrevAttribute(){

    return ChatMessage::whereDiscussionId($this->discussion_id)
      ->where('created_at', '<', $this->created_at)
      ->count() > 0 ? true : false;
  }

  public function getPreviousMessageAttribute(){

    return ChatMessage::whereDiscussionId($this->discussion_id)
      ->where('id', '<', $this->id)
      ->orderBy('created_at', 'desc')
      ->first();
  }

  public function getPreviousMessagesAttribute(){

    return ChatMessage::whereDiscussionId($this->discussion_id)
      ->where('created_at', '<', $this->created_at)
      ->orderBy('created_at')
      ->limit(10)
      ->get();
  }

  public function preSave(){

    $validator = Validator::make(
      $this->attributes,
      [
        'text'=>'required'
      ],
      [
        'text.required'=>'Harap masukkan pesan yang mau dikirim'
      ]
    );
    if($validator->fails()) exc($validator->errors()->first());

    if(isset($this->fill_attributes['images'])){

      $images = [];
      $disk = isset($this->fill_attributes['image_disk']) ? $this->fill_attributes['image_disk'] : 'images';

      foreach($this->fill_attributes['images'] as $image){

        if(is_object($image) &&  get_class($image) == UploadedFile::class){

          $file_name = get_md5_filename($image);
          if(!Storage::disk($disk)->exists($file_name))
            Storage::disk($disk)->put($file_name, file_get_contents($image));

          $images[] = $file_name;
        }

        else if(isset($image['file'])){

          $file_name = get_md5_filename($image['file']);

          if(!Storage::disk($disk)->exists($file_name))
            Storage::disk($disk)->put($file_name, file_get_contents($image['file']));

          unset($image['file']);
          $images[] = $file_name;
        }

      }

      $this->images = $images;
    }

    $extra = array_merge($this->extra ?? [], [ 'user'=>User::find(Session::get('user_id'))->name ?? '', 'user_id'=>Session::get('user_id') ]);
    $this->extra = $extra;
  }

  public function postSave()
  {
    if($this->wasRecentlyCreated)
      event(new ChatEvent(ChatEvent::TYPE_NEW_CHAT_MESSAGE, $this->discussion, $this));
  }

  public function calculate()
  {
    $this->discussion->calculate();
  }

}
