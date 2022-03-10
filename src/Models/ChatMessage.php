<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Andiwijaya\AppCore\Events\ChatEvent;
use Carbon\Carbon;
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

  const STATUS_ERROR = -1;
  const STATUS_SENT = 1;
  const STATUS_DELIVERED = 2;

  protected $fillable = [ 'uid', 'status', 'type', 'discussion_id', 'session_id', 'reply_of', 'initial', 'first_reply_at', 'first_reply_after',
    'unread', 'direction', 'text', 'images', 'extra', 'notified', 'unsent',
    'context', 'context_id', 'is_bot', 'is_system', 'notified_at', 'ref_id', 'ref_type', 'template_id', 'pickyassist_pid' ];

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

  public function prev_message_in(){

    return $this->hasOne(ChatMessage::class, 'discussion_id', 'discussion_id')
      ->where('id', '<', $this->id)
      ->where('direction', ChatMessage::DIRECTION_IN)
      ->orderBy('id', 'desc');
  }

  public function prev_message_out(){

    return $this->hasOne(ChatMessage::class, 'discussion_id', 'discussion_id')
      ->where('id', '<', $this->id)
      ->where('direction', ChatMessage::DIRECTION_OUT)
      ->orderBy('id', 'desc');
  }


  public function getHtmlAttribute(){

    // Find link
    //https://www.google.com
    // www.google.com

    $text = $this->text;

    preg_match_all('/([http:\/\/|https:\/\/]*\w+\.\w+\.\w+[\.\w]*[\/]*[\S]+)/', $text, $matches);
    if(isset($matches[0]) && isset($matches[0][0])){
      foreach($matches[0] as $link){
        $text = str_replace($link, "<a href=\"{$link}\">{$link}</a>", $text);
      }
    }

    preg_match_all('/loc\:\{(.*?(?=\}))\}/', $text, $matches);
    if(isset($matches[1]) && isset($matches[1][0])){
      $latlng = json_decode('{' . $matches[1][0] . '}', true);
      $lat = $latlng['lat'] ?? '';
      $lng = $latlng['long'] ?? '';

      $tag = "<a href='http://www.google.com/maps/place/{$lat},{$lng}' target='_blank'><span class='img unloaded' style='border-radius:.3rem;width:64px;height:64px' data-src='/images/chat-message-map-thumb.jpeg'></span></a>";
      $text = str_replace($matches[0][0], $tag, $text);
    }

    $text = nl2br($text);

    return $text;
  }

  public function getShortTextAttribute()
  {
    $text = $this->text;

    preg_match_all('/loc\:\{(.*?(?=\}))\}/', $text, $matches);
    if(isset($matches[1]) && isset($matches[1][0])){
      $text = str_replace($matches[0][0], '(location)', $text);
    }

    return $text;
  }

  public function getIsInitialAttribute(){

    if($this->type == ChatMessage::TYPE_WHATSAPP)
      //return strpos($this->text, '[Initial]') !== false;
      //return (!isset($this->prev_message_in->id) || $this->prev_message_in->created_at->diffInMinutes(Carbon::now()) > 0) ? 1 : 0;
      return !isset($this->prev_message_in->id) ? 1 : 0;
    else
      return $this->initial;
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

    if(!$this->id){
      $extra = array_merge($this->extra ?? [], [ 'user'=>User::find(Session::get('user_id'))->name ?? '', 'user_id'=>Session::get('user_id') ]);
      $this->extra = $extra;
    }
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


  public static function businessNearestStart(&$time){

    $chat_online_start = config('chat.online-start');
    $chat_online_end = config('chat.online-end');
    $chat_online_days = config('chat.online-days');

    if($time->format('Hi') > date('Hi', strtotime($chat_online_end))){
      $time = Carbon::createFromFormat('Y-m-d H:i:s', $time->addDays(1)->format('Y-m-d') . ' ' . date('H:i:s', strtotime($chat_online_start)));
    }
    else if($time->format('Hi') < date('Hi', strtotime($chat_online_start))){
      $time = Carbon::createFromFormat('Y-m-d H:i:s', $time->format('Y-m-d') . ' ' . date('H:i:s', strtotime($chat_online_start)));
    }

    $counter = 0;
    while(!in_array($time->format('w'), $chat_online_days)){
      $time->addDays(1);
      $counter++;
      if($counter > 7)
        exc('System failure');
    }
  }

  public static function businessDiffInSeconds($start, $end){

    /*
     * 26 = sat, 27 = sun
     * 04:30 08:30 -> 06:00 - 08:30
     * 17:30 23:30 -> 17:30 - 06:00 -8
     * start: convert to nearest online business start
     * end: convert to nearest online business start
     * end - start - substractMinutes
     * substractMinutes = loop start to end, if weekdays -8, if holiday -24
     */

    $start = Carbon::createFromFormat('Y-m-d H:i:s', $start->format('Y-m-d H:i:s'));
    $end = Carbon::createFromFormat('Y-m-d H:i:s', $end->format('Y-m-d H:i:s'));

    $chat_online_start = config('chat.online-start');
    $chat_online_end = config('chat.online-end');
    $chat_online_days = config('chat.online-days'); // 16:49 vs 09:26
    $offline_hour_in_seconds = 86400 - (strtotime($chat_online_end) - strtotime($chat_online_start));

    self::businessNearestStart($start);
    self::businessNearestStart($end);

    $substractMinutes = 0;
    $current = $start->copy();
    $debug = [];
    while($current->format('Ymd') < $end->format('Ymd')){
      if(!in_array($current->format('w'), $chat_online_days)){
        $substractMinutes += 86400;
        $debug[] = 86400;
      }
      else{
        $substractMinutes += $offline_hour_in_seconds;
        $debug[] = $offline_hour_in_seconds;
      }
      $current->addDays(1);
    }

    $seconds = $end->diffInSeconds($start);

    return $seconds - $substractMinutes;
  }

}
