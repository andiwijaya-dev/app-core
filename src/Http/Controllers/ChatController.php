<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Models\ChatDiscussion;
use Andiwijaya\AppCore\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ChatController{

  public function index(Request $request){

    if($request->ajax()){

      $action = $request->get('action');

      switch($action){

        case 'open-chat':

          return [
            '#chatpopup>*:nth-child(2)'=>'',
            'script'=>""
          ];

      }

    }

    return view('website.chat', []);

  }

  public function store(Request $request){

    //sleep(1);

    if(Session::has('chat.id') && !ChatDiscussion::whereId(Session::get('chat.id'))->first())
      Session::forget('chat');

    $action = $request->input('action');

    switch($action){

      case 'open-chat':
        return $this->openChat($request);

      case 'send-message-offline':
        return $this->sendMessageOffline($request);

      case 'send-message':
        return $this->sendMessage($request);

      case 'auth':
        return $this->auth($request);

      case 'end-chat':
        return $this->endChat($request);

      case 'check-message':
        return  $this->checkForNewMessages($request);

      case 'set-as-sent':
        return  $this->setAsSent($request);

      default:
        exc($action);
        break;

    }

  }

  public function auth($request){

    // Validation
    $validator = Validator::make($request->all(), [
      'key'=>'required|email',
      'topic'=>'required|min:3'
    ], [
      'key.required'=>'Email harus diisi',
      'key.email'=>'Masukkan email yang benar',
      'topic.required'=>'Topik belum diisi',
      'topic.min'=>'Topik harus diisi'
    ]);
    if($validator->fails()) exc($validator->errors()->first());

    $discussion = ChatDiscussion::updateOrCreate([
      'key'=>$request->get('key'),
      'title'=>$request->get('topic')
    ], [
      'status'=>ChatDiscussion::STATUS_OPEN,
      'avatar_image_url'=>$request->get('avatar_image_url')
    ]);

    Session::put('chat.id', $discussion->id);
    Session::put('chat.key', $request->get('key'));
    Session::put('chat.topic', $request->get('topic'));

    $sections = view($this->view,
      array_merge([
        'extends'=>$this->extends,
        'item'=>$discussion,
        'offline'=>0
      ])
    )
      ->renderSections();

    return [
      '.chat-popup-head'=>$sections['chat-head'],
      '.chat-popup-body'=>$sections['chat-body'],
      '.chat-popup-foot'=>$sections['chat-foot'],
      'script'=>implode(';', [
        "$.chat_resize()"
      ])
    ];

  }

  public function offline($request){

    $sections = view($this->view,
      array_merge([
        'extends'=>$this->extends,
        'offline'=>1
      ])
    )
      ->renderSections();

    return [
      '.chat-popup-head'=>$sections['offline-head'],
      '.chat-popup-body'=>$sections['offline-body'],
      '.chat-popup-foot'=>$sections['offline-foot'],
      '!.chat-popup'=>$sections['chat-popup'],
      'script'=>implode(';', [
        "$.chat_popup_open()",
        "$('.chat-popup-body').scrollToBottom()",
      ])
    ];

  }

  private function sendMessage($request){

    $discussion_id = Session::get('chat.id');
    $discussion = ChatDiscussion::whereId($discussion_id)
      ->whereStatus(ChatDiscussion::STATUS_OPEN)
      ->first();

    if(!$discussion){

      $sections = view($this->view,
        array_merge([
          'extends'=>$this->extends,
          'item'=>$discussion,
          'offline'=>0
        ])
      )
        ->renderSections();

      return [
        '.chat-popup-head'=>$sections['intro-head'],
        '.chat-popup-body'=>$sections['intro'],
        '.chat-popup-foot'=>'',
        'script'=>implode(';', [
          "$.chat_resize()",
          "$.alert('" . __('models.chat-message-unable-to-send-message') . "')"
        ])
      ];

    }

    $message = new ChatMessage([
      'discussion_id'=>$discussion->id,
      'direction'=>ChatMessage::DIRECTION_IN
    ]);
    $message->fill($request->all());
    $message->save();

    $offline = count(Redis::pubsub('channels', 'chat-list')) == 0;

    if($offline){

      return [
        'script'=>implode(';', [
          "$.alert('Pesan anda telah terkirim tetapi pada saat ini staff kami sedang offline, kami akan menghubungi anda kembali.')",
          "$.chat_popup_close()",
        ])
      ];
    }
    else{

      return [
        //'.chat-popup-body'=>'>>' . view('andiwijaya::components.customer-chat-message', [ 'item'=>$message ])->render(),
        'script'=>implode(';', [
          "$.chat_resize()",
          "$.chat_popup_clear()",
          "$('.chat-popup-body').scrollToBottom()"
        ])
      ];
    }

  }

  public function sendMessageOffline($request){

    $validator = Validator::make($request->all(),
      [
        'message'=>'required|min:5',
        'email'=>'required|email',
        'phone_number'=>'nullable|regex:/^\d{7,}$/'
      ],
      [
        'message.required'=>'Harap masukkan pesan anda',
        'message.min'=>'Harap masukkan pesan anda',
        'email.required'=>'Harap masukkan email anda',
        'email.email'=>'Harap masukkan email dengan benar',
        'phone_number.regex'=>'Harap masukkan nomor telpon dengan benar'
      ]);
    if($validator->fails()) exc($validator->errors()->first());

    $text = $request->get('message');
    $email = $request->get('email');
    $phone_number = $request->get('phone_number');

    $discussion = ChatDiscussion::updateOrCreate([
      'key'=>$email,
      'title'=>'Pertanyaan'
    ]);
    if($phone_number){
      $extra['phone_number'] = $phone_number;
      $discussion->extra = $extra;
      $discussion->save();
    }

    $message = new ChatMessage([
      'discussion_id'=>$discussion->id,
      'direction'=>ChatMessage::DIRECTION_IN
    ]);
    $message->text = $text;
    $extra = $message->extra;
    $extra['offline-message'] = 1;
    $message->extra = $extra;
    $message->save();

    Session::put('chat.id', $discussion->id);

    $sections = view($this->view,
      array_merge([
        'extends'=>$this->extends,
        'offline'=>1
      ])
    )
      ->renderSections();

    return [
      '.chat-popup-head'=>$sections['offline-head'],
      '.chat-popup-body'=>$sections['offline-thankyou'],
      '.chat-popup-foot'=>'',
    ];

  }

  public function openChat($request){

    $request->has('key') ? Session::put('chat.key', $request->get('key')) : '';

    $offline = count(Redis::pubsub('channels', 'chat-list')) == 0;

    if($offline)
      return $this->offline($request);
    if($request->has('key') && $request->has('topic'))
      return $this->auth($request);

    $discussion_id = Session::get('chat.id');
    $discussion = ChatDiscussion::whereId($discussion_id)
      ->whereStatus(ChatDiscussion::STATUS_OPEN)
      ->first();

    if(!$discussion) Session::forget('chat.id');

    $key = Session::get('chat.key');
    $available_topics = ChatDiscussion::where('key', $key)
      ->where('title', '<>', '')
      ->pluck('title')->unique();

    $latest_messages = $discussion->latest_messages ?? [];

    DB::statement("UPDATE chat_message SET unsent = 0 WHERE discussion_id = ? and direction = ? and unsent = 1", [
      $discussion_id, ChatMessage::DIRECTION_OUT
    ]);

    $sections = view($this->view,
      array_merge([
        'extends'=>$this->extends,
        'item'=>$discussion,
        'latest_messages'=>$latest_messages,
        'key'=>$key,
        'available_topics'=>$available_topics,
        'offline'=>$offline
      ])
    )
      ->renderSections();

    return [
      '.chat-popup-head'=>($offline ? $sections['offline-head'] : (isset($discussion->id) ? $sections['chat-head'] : $sections['intro-head'])),
      '.chat-popup-body'=>($offline ? $sections['offline-body'] : (isset($discussion->id) ? $sections['chat-body'] : $sections['intro'])),
      '.chat-popup-foot'=>($offline ? $sections['offline-foot'] : (isset($discussion->id) ? $sections['chat-foot'] : '')),
      '!.chat-popup'=>$sections['chat-popup'],
      'script'=>implode(';', [
        "$.chat_popup_open()",
        "$('.chat-popup-body').scrollToBottom()",
      ])
    ];

  }

  public function endChat($request){

    $discussion_id = Session::get('chat.id');
    $discussion = ChatDiscussion::whereId($discussion_id)->first();

    if($discussion){
      Session::forget('chat.id');
      $discussion->end();
    }

    $key = Session::get('chat.key');
    $available_topics = ChatDiscussion::where('key', $key)
      ->where('title', '<>', '')
      ->pluck('title')->unique();

    $sections = view($this->view,
      array_merge([
        'extends'=>$this->extends,
        'item'=>$discussion,
        'key'=>$key,
        'available_topics'=>$available_topics,
        'offline'=>0
      ])
    )
      ->renderSections();

    return [
      '.chat-popup-head'=>$sections['intro-head'],
      '.chat-popup-body'=>$sections['intro'],
      '.chat-popup-foot'=>'',
      'script'=>implode(';', [
        "$.chat_popup_close()"
      ])
    ];

  }

  public function checkForNewMessages(Request $request){

    $last_message_id = $request->get('last_message_id');

    $discussion_id = Session::get('chat.id');

    $messages = ChatMessage::where([
      'discussion_id'=>$discussion_id
    ])
      ->where('id', '>', $last_message_id)
      ->orderBy('id')
      ->get();

    if(count($messages) > 0){

      $html = [];
      foreach($messages as $message)
        $html[] = view('andiwijaya::components.customer-chat-message', [ 'item'=>$message ])->render();

      return [
        '.chat-body-messages'=>'>>' . implode('', $html),
        '.chat-badge .unread'=>"<span>" . count($messages) . "</span>",
        'script'=>implode(';', [
          "$.chat_resize()",
          "$.chat_popup_clear()",
          "$('.chat-popup-body').scrollToBottom()",
          "!$('.chat-popup').hasClass('active') ? $('.chat-badge .unread').removeClass('hidden') : $('chat-unread').addClass('hidden')"
        ])
      ];
    }
  }

  public function setAsSent(Request $request){

    $message_id = $request->get('sent_id');
    ChatMessage::whereId($message_id)->update([ 'unsent'=>0 ]);

  }

  public function update(Request $request){

    return [
      '_'=>view('website.chat-popup')->render()
    ];

  }

}