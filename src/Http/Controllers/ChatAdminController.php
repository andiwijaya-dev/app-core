<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Models\ChatDiscussion;
use Andiwijaya\AppCore\Models\ChatMessage;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatAdminController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  public $module_id = 33;

  public $name = 'chat';

  public $path = '/chat-admin';

  public $view = 'andiwijaya::chat';

  public $extends = 'website.minimal';

  public $height = '90vh';

  public function index(Request $request, array $extra = []){

    $request->has('display') ? Session::put('chat.display', $request->get('display')) : '';

    $model = ChatDiscussion::orderBy('updated_at', 'desc');

    switch(Session::get('chat.display')){

      case 'unreplied':
        $model->where('unreplied_count', '>', 0);
        break;

    }

    $action = isset(($actions = explode('|', $request->get('action')))[0]) ? $actions[0] : '';

    $params = $this->getParams($request, $extra);

    $params['chats'] = $model->get();

    if($request->has('chat')) $params['chat'] = $request->get('chat');

    if($request->ajax()){

      return [
        '.chat .chat-list-body'=>view($this->view, $params)->renderSections()['chat-list']
      ];

    }

    else{

      switch($action){

        case 'download':
          return $this->download($request);

        default:
          return view($this->view, $params);

      }

    }

  }

  public function show(Request $request, $id, array $extra = []){

    $params = $this->getParams($request, $extra);

    $params['chat'] = ChatDiscussion::whereId($id)->first();

    if($request->ajax()){

      $sections = view($this->view, $params)->renderSections();

      return [
        '.message-list'=>$sections['message-list'],
        '.info-card'=>$sections['info'],
        'rewrite'=>[
          'title'=>'',
          'url'=>$this->path . '/' . $id
        ],
        'script'=>implode(';', [
          "$('.chat').chat_resize()",
          "if(typeof channels[1]) socket.emit('leave', channels[1])",
          "socket.emit('join', (channels[1] = 'discussion-{$params['chat']->id}'));"
        ])
      ];

    }
    else{

      $request->merge($params);

      return $this->index($request, $extra);

    }

  }

  public function store(Request $request){

    $discussion_id = $request->get('id');
    $discussion = ChatDiscussion::where([
      'id'=>$discussion_id,
      'status'=>ChatDiscussion::STATUS_OPEN
    ])
      ->first();

    if(!$discussion) exc(__('models.chat-message-unable-to-send-message'));

    $message = new ChatMessage([
      'discussion_id'=>$request->get('id'),
      'direction'=>ChatMessage::DIRECTION_OUT
    ]);
    $message->fill($request->all());
    $message->save();

    return [
      //".chat .message-list-body"=>'>>' . view('andiwijaya::components.chat-message-item', [ 'item'=>$message, 'highlight'=>1 ])->render(),
      'script'=>implode(';', [
        "$('.chat .message-list-body').scrollToBottom()",
        "$('.chat .message-list-foot input[name=text]').val('')",
        "$('.chat .message-list-foot .images-cont').html('')"
      ])
    ];

  }

  public function download(Request $request){

    return new StreamedResponse(
      function(){

        $handle = fopen('php://output', 'w');

        fputcsv($handle, [
          'Email',
          'Topic',
          'Tanggal',
          'Tipe',
          'Pesan',
        ]);

        ChatMessage::orderBy('discussion_id', 'asc')
          ->chunk(1000, function($messages) use($handle){

            foreach($messages as $message){

              $obj = [
                $message->discussion->key,
                $message->discussion->title,
                $message->created_at,
                $message->direction == ChatMessage::DIRECTION_IN ? 'Masuk' : 'Keluar',
                $message->text
              ];

              fputcsv($handle, $obj);

            }

          });

        fclose($handle);

      },
      200,
      [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="chat-' . Carbon::now()->format('Y-m-d-H-i-s') . '.csv"',
      ]);

  }


  protected function getParams(Request $request, array $params = []){

    $obj = [
      'module_id'=>$this->module_id,
      'name'=>$this->name,
      'path'=>$this->path,
      'extends'=>$this->extends,
      'height'=>$this->height
    ];

    if(env('APP_DEBUG')) $obj['faker'] = Factory::create();

    if(Session::has('user')) $obj['user'] = Session::get('user');


    $obj = array_merge($obj, $params);

    return $obj;

  }

}