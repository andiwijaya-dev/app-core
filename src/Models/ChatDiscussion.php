<?php

namespace Andiwijaya\AppCore\Models;

use Andiwijaya\AppCore\Models\Traits\FilterableTrait;
use Andiwijaya\AppCore\Models\Traits\LoggedTraitV3;
use Andiwijaya\AppCore\Events\ChatEvent;
use App\Mail\ChatDiscussionCustomerNotification;
use App\Models\Customer;
use App\Models\FAQ;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class ChatDiscussion extends Model
{
  use LoggedTraitV3, FilterableTrait;

  protected $table = 'chat_discussion';

  protected $filter_searchable = [
    'id:=',
    'key:like',
    'name:like',
  ];

  protected $fillable = [ 'status', 'avatar_image_url', 'key', 'name', 'mobile_number', 'whatsapp_number', 'title', 'extra', 'unreplied_count',
    'handled_by', 'last_replied_at', 'context', 'is_new_customer', 'is_on_outgoing', 'pickyassist_pid' ];

  protected $attributes = [
    'status'=>self::STATUS_OPEN,
    'unreplied_count'=>0,
    'is_new_customer'=>1,
    'context'=>1
  ];

  protected $casts = [
    'extra'=>'array'
  ];

  const STATUS_OPEN = 1;
  const STATUS_CLOSED = -1;

  public function handled_by_user(){

    return $this->belongsTo('Andiwijaya\AppCore\Models\User', 'handled_by');
  }

  public function messages()
  {
    return $this->hasMany('Andiwijaya\AppCore\Models\ChatMessage', 'discussion_id', 'id');
  }

  public function customer(){

    if(substr($this->key, 0, 1) == '+')
      return $this->belongsTo('App\Models\Customer', 'key', 'whatsapp_number');
    return $this->belongsTo('App\Models\Customer', 'key');
  }

  public function getLatestMessagesAttribute(){

    return ChatMessage::whereDiscussionId($this->id)
      ->where('created_at', '>=', Carbon::now()->addHour(-6)->format('Y-m-d H:i:s'))
      ->orderBy('created_at', 'desc')
      ->limit(5)
      ->get()
      ->reverse();
  }

  public function getLatestMessageAttribute()
  {
    return ChatMessage::whereDiscussionId($this->id)->orderBy('created_at', 'desc')->first();
  }

  public function getLastMessageAttribute()
  {
    return ChatMessage::whereDiscussionId($this->id)->orderBy('id', 'desc')->first();
  }


  public function getPickyAssistPid()
  {
    if($this->pickyassist_pid > 0)
      return $this->pickyassist_pid;

    $pickyassist = ChatDiscussion::getPickyAssistByContext($this->context);

    return $pickyassist['project_id'] ?? null;
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
      ->where('is_system', '<>', 1)
      ->orderBy('created_at', 'desc')
      ->first();

    $this->unreplied_count = isset($last_replied->id) ?
      ChatMessage::whereDiscussionId($this->id)->where('is_system', '<>', 1)->where('id', '>', $last_replied->id)->where('direction', ChatMessage::DIRECTION_IN)->count() :
      ChatMessage::whereDiscussionId($this->id)->where('is_system', '<>', 1)->where('direction', ChatMessage::DIRECTION_IN)->count();

    $this->is_new_customer = count($this->customer->orders ?? []) <= 0 ? 1 : 0;

    parent::save();
  }

  public function sendEmailNotification(){

    if(preg_match('/\d+/', $this->key)){

      $customer = Customer::find($this->key);
      if(isset($customer->id) && filter_var($customer->email, FILTER_VALIDATE_EMAIL)){
        $email = $customer->email;
      }
    }
    else if(filter_var($this->key, FILTER_VALIDATE_EMAIL))
      $email = $this->key;

    if(filter_var($email, FILTER_VALIDATE_EMAIL)){

      Mail::to($email)
        ->queue(new ChatDiscussionCustomerNotification($this->id));
    }

  }

  public function sendGreeting(){

    if(!config('chat.greeting')) return;

    $text = config('chat.greeting');
    preg_match_all('/(\[\w+\])+/', $text, $matches);
    if(isset($matches[0][0])){
      foreach($matches[0] as $match){
        $key = substr($match, 1, strlen($match) - 2);
        $value = $this->{$key} ?? '';
        $text = str_replace($match, $value, $text);
      }
    }

    $message = new ChatMessage([
      'discussion_id'=>$this->id,
      'direction'=>ChatMessage::DIRECTION_OUT,
      'text'=>$text,
      'is_system'=>1,
      'extra'=>[ 'name'=>'Tara', 'avatar_url'=>'chat-figure.png' ],
    ]);
    $message->save();
  }

  public function sendOfflineMessage(){

    $offline_message_at = Carbon::createFromTimeString(date('Y-m-d H:i:s', strtotime($this->extra['offline_message_at'] ?? null)));

    if(config('chat.offline-message') && $offline_message_at->diffInHours() > 2){

      $text = config('chat.offline-message');
      $faqs = config('chat.offline-message-faqs', []);
      if(count($faqs) > 0){
        $text .= "<div class='vmar-1'><label>Mungkin artikel dibawah ini dapat membantu anda:</label><ol class='vmart-05'>";
        foreach($faqs as $faq_topic){

          $faq = FAQ::where('topic', $faq_topic)->first();
          if(isset($faq->id))
            $text .= "<li><a href=\"/faq/{$faq->seo_url}\" target=\"_blank\">{$faq->topic}</a></li>";
        }
        $text .= "</ol></div>";
      }

      $message = new ChatMessage([
        'discussion_id'=>$this->id,
        'direction'=>ChatMessage::DIRECTION_OUT,
        'text'=>$text,
        'is_system'=>1,
        'extra'=>[ 'name'=>'Tara', 'avatar_url'=>'chat-figure.png' ],
      ]);
      $message->save();

      $extra = $this->extra;
      $extra['offline_message_at'] = Carbon::now()->format('Y-m-d H:i:s');
      $this->extra = $extra;
      $this->save();
    }
  }



  public static function notifyUnsent($cmd = null){

    $discussions = ChatDiscussion::whereExists(function($query){
      $query->select(DB::raw(1))
        ->from('chat_message')
        ->whereRaw('chat_message.discussion_id = chat_discussion.id 
          AND direction = 2 
          AND unsent = 1 
          AND notified <> 1 
          AND TIMESTAMPDIFF(MINUTE, created_at, NOW()) BETWEEN 5 AND 10080');
    })
      ->get();

    if($cmd) $cmd->info("Discussions require notification: " . count($discussions));

    foreach($discussions as $discussion){

      $offline = count(Redis::pubsub('channels', Str::slug(env('APP_NAME')) . '-' . "customer-discussion-{$discussion->id}")) <= 0;

      if($offline){
        $discussion->sendEmailNotification();

        ChatMessage::where([
          'discussion_id'=>$discussion->id,
          'unsent'=>1
        ])
          ->update([ 'notified'=>1 ]);

        $discussion->update([ 'last_notified_at'=>Carbon::now()->toDateTimeString() ]);

        if($cmd) $cmd->info("Notification to {$discussion->key} sent.");
      }
    }
  }

  public static function detachHandled(Command $cmd = null){

    DB::table('chat_discussion')
      ->select(DB::raw("`id`, (SELECT updated_at FROM chat_message WHERE discussion_id = chat_discussion.id ORDER BY `id` DESC LIMIT 1) as last_updated_at"))
      ->where('handled_by', '>', 0)
      ->get()
      ->each(function($discussion) use($cmd){

        if(Carbon::create($discussion->last_updated_at)->diffInMinutes() > 5){
          $cmd->info("Close discussion {$discussion->id}");

          ChatDiscussion::findOrFail($discussion->id)->update([ 'handled_by'=>null ]);
        }

      });
  }

  public static function check(Command $command = null){

    self::detachHandled($command);
  }

  public static function getPickyAssistByContext($context)
  {
    $pickyassists = collect(Config::where('key', 'pickyassist')->pluck('value')->first() ?? []);

    foreach($pickyassists as $pickyassist){
      if(in_array($context, $pickyassist['context'] ?? []))
        return $pickyassist;
    }
    return null;
  }

}
