<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Models\ChatDiscussion;
use Andiwijaya\AppCore\Models\ChatMessage;
use Illuminate\Http\Request;
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

      case 'send-message':
        return $this->sendMessage($request);

      case 'auth':
        return $this->auth($request);

      case 'end-chat':
        return $this->endChat($request);
        break;

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
        'item'=>$discussion
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

  private function sendMessage($request){

    $discussion_id = Session::get('chat.id');
    $discussion = ChatDiscussion::whereId($discussion_id)
      ->whereStatus(ChatDiscussion::STATUS_OPEN)
      ->first();

    if(!$discussion){

      $sections = view($this->view,
        array_merge([
          'extends'=>$this->extends,
          'item'=>$discussion
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

    return [
      //'.chat-popup-body'=>'>>' . view('andiwijaya::components.customer-chat-message', [ 'item'=>$message ])->render(),
      'script'=>implode(';', [
        "$.chat_resize()",
        "$.chat_popup_clear()",
        "$('.chat-popup-body').scrollToBottom()"
      ])
    ];

  }

  public function openChat($request){

    $request->has('key') ? Session::put('chat.key', $request->get('key')) : '';

    if($request->has('key') && $request->has('topic'))
      $this->auth($request);

    $discussion_id = Session::get('chat.id');
    $discussion = ChatDiscussion::whereId($discussion_id)
      ->whereStatus(ChatDiscussion::STATUS_OPEN)
      ->first();

    if(!$discussion) Session::forget('chat.id');

    $key = Session::get('chat.key');
    $available_topics = ChatDiscussion::where('key', $key)
      ->where('title', '<>', '')
      ->pluck('title')->unique();

    $sections = view($this->view,
      array_merge([
        'extends'=>$this->extends,
        'item'=>$discussion,
        'key'=>$key,
        'available_topics'=>$available_topics
      ])
    )
      ->renderSections();

    return [
      '.chat-popup-head'=>$sections['chat-head'],
      '.chat-popup-body'=>$sections['chat-body'],
      '.chat-popup-foot'=>$sections['chat-foot'],
      '!.chat-popup'=>$sections['chat-popup'],
      'script'=>implode(';', [
        "$.chat_popup_open()",
        "$('.chat-popup-body').scrollToBottom()"
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
        'available_topics'=>$available_topics
      ])
    )
      ->renderSections();

    return [
      '.chat-popup-head'=>$sections['intro-head'],
      '.chat-popup-body'=>$sections['intro'],
      '.chat-popup-foot'=>'',
      'script'=>implode(';', [
        "$.chat_resize()",
        "$.chat_popup_close()"
      ])
    ];

  }

  public function update(Request $request){

    return [
      '_'=>view('website.chat-popup')->render()
    ];

  }

}