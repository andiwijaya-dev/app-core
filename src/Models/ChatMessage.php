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

  protected $fillable = [ 'discussion_id', 'unread', 'direction', 'text', 'images', 'extra', 'notified', 'unsent' ];

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

  public function preSave(){

    if(isset($this->fill_attributes['images'])){

      $images = [];
      $disk = isset($this->fill_attributes['image_disk']) ? $this->fill_attributes['image_disk'] : 'images';

      foreach($this->fill_attributes['images'] as $image){

        if(is_object($image) &&  get_class($image) == UploadedFile::class){

          $file_name = get_md5_filename($image);
          if(!Storage::disk($disk)->exists($file_name))
            Storage::putFile($disk, $image, $file_name);

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
