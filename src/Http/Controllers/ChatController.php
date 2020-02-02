<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Models\Chat;
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

class ChatController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  public $module_id = 33;

  public $name = 'chat';

  public $path = '/chat';
  
  public $view = 'andiwijaya::chat';

  public $extends = 'website.minimal';

  public $height = '100vh';

  public function index(Request $request){

    $model = Chat::orderBy('updated_at', 'desc');

    switch($request->get('filter')){

      case 'unread':
        $model->where('unread_count', '>', 0);
        break;

    }

    $action = isset(($actions = explode('|', $request->get('action')))[0]) ? $actions[0] : '';

    $params = $this->getParams($request);

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

    $params['chat'] = Chat::whereId($id)->first();

    if($request->ajax()){

      return [
        '.message-list'=>view($this->view, $params)->renderSections()['message-list'],
        'rewrite'=>[
          'title'=>'',
          'url'=>'/chat/' . $id
        ],
        'script'=>"$('.chat').chat_resize()"
      ];

    }
    else{

      $request->merge([ 'chat'=>$params['chat'] ]);

      return $this->index($request);

    }

  }

  public function store(Request $request){

    $message = new ChatMessage([
      'chat_id'=>$request->get('id'),
      'direction'=>ChatMessage::DIRECTION_OUT
    ]);
    $message->fill($request->all());
    $message->save();

    return [
      ".chat .message-list-body"=>'>>' . view('andiwijaya::components.chat-message-item', [ 'item'=>$message, 'highlight'=>1 ])->render(),
      'script'=>"$('.chat .message-list-body').scrollToBottom()"
    ];

  }

  public function download(Request $request){

    return new StreamedResponse(
      function(){

        $handle = fopen('php://output', 'w');

        fputcsv($handle, [
          'Nama',
          'Tanggal',
          'Tipe',
          'Pesan',
        ]);

        ChatMessage::orderBy('chat_id', 'asc')
          ->chunk(1000, function($messages) use($handle){

            foreach($messages as $message){

              $obj = [
                $message->chat->title,
                $message->created_at,
                $message->direction == ChatMessage::DIRECTION_IN ? 'Masuk' : 'Keluar',
                $message->message
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


  public function getParams(Request $request, array $params = []){

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