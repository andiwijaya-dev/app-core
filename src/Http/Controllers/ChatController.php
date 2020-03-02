<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Models\ChatDiscussion;
use Andiwijaya\AppCore\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ChatController{

  protected $extends = 'website.no-header';

  protected $view = 'andiwijaya::chat';


  public function index(Request $request){

    return view($this->view,
      array_merge([
        'extends'=>$this->extends
      ])
    );

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

      default:
        break;

    }

  }

  private function auth($request){

    // Validation

    $discussion = ChatDiscussion::updateOrCreate([
      'key'=>$request->get('key'),
      'title'=>$request->get('topic')
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

    $text = $request->get('text');
    $discussion_id = Session::get('chat.id');
    $discussion = ChatDiscussion::whereId($discussion_id)->first();

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
          "$.chat_resize()"
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

  private function openChat($request){

    $discussion_id = Session::get('chat.id');
    $discussion = ChatDiscussion::whereId($discussion_id)->first();

    $sections = view($this->view,
      array_merge([
        'extends'=>$this->extends,
        'item'=>$discussion
      ])
    )
      ->renderSections();

    return [
      '!.chat-popup'=>$sections['chat-popup'],
      'script'=>implode(';', [
        "$.chat_popup_open()"
      ])
    ];

  }

}