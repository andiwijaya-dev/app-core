<?php

namespace Andiwijaya\AppCore\Http\Controllers;

use Andiwijaya\AppCore\Events\ChatEvent;
use Andiwijaya\AppCore\Models\ChatDiscussion;
use Andiwijaya\AppCore\Models\ChatMessage;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatAdminController extends BaseController
{
  public $extends = '';
  public $path = ''; // Required for message notification onclick target
  public $title = ''; // Required for message notification onclick target

  public $view = 'andiwijaya::chat-admin';
  public $view_discussion_item = 'andiwijaya::components.chat-admin-discussion-item';
  public $view_discussion_no_item = 'andiwijaya::components.chat-admin-discussion-no-item';
  public $view_message_item = 'andiwijaya::components.chat-admin-message-item';
  public $view_message_foot = 'andiwijaya::components.chat-admin-message-foot';


  public $channel_discussion = 'chat-admin-discussion';

  public $storage = 'images';


  public function index(Request $request){

    $action = isset(($actions = explode('|', $request->get('action', 'view')))[0]) ? $actions[0] : '';
    $method = action2method($action);

    if(method_exists($this, $method))
      return call_user_func_array([ $this, $method ], func_get_args());
  }

  public function view(Request $request){

    $action = isset(($actions = explode('|', $request->get('action', 'view')))[0]) ? $actions[0] : '';

    $filter = $request->get('filter');
    $after_id = $actions[1] ?? null;
    $item_per_page = 10;

    $model = ChatDiscussion::
    whereExists(function($query){
      $query->select(DB::raw(1))
        ->from('chat_message')
        ->whereRaw('chat_message.discussion_id = chat_discussion.id');
    })
      ->orderBy('updated_at', 'desc')
      ->orderBy('id', 'desc');

    if($filter != 'all')
      $model->where('unreplied_count', '>', 0);

    if(strlen($request->get('search')) > 0)
      $model->filter($request->all());

    if($after_id > 0){
      $discussions = collect([]);

      $append = false;
      $model->chunk(1000, function($rows) use($after_id, $item_per_page, &$discussions, &$append){

        foreach($rows as $row){

          if($row->id == $after_id) $append = true;

          if($append) $discussions->add($row);

          if(count($discussions) >= $item_per_page + 1) break;
        }

        if(count($discussions) >= $item_per_page + 1) return false;
      });
    }
    else{

      $discussions = $model
        ->limit($item_per_page + 1)
        ->get();
    }

    $after_id = count($discussions) >= $item_per_page + 1 ? $discussions[$item_per_page]->id : null;
    $discussions = $discussions->splice(0, $item_per_page);

    $params = [
      'extends'=>$this->extends,
      'discussions'=>$discussions,
      'view_discussion_item'=>$this->view_discussion_item,
      'view_discussion_no_item'=>$this->view_discussion_no_item,
      'filter'=>$filter,
      'after_id'=>$after_id,
      'channel_discussion'=>$this->channel_discussion,
      'title'=>$this->title
    ];

    if($request->ajax()){

      switch($action){

        case 'load-more':
          return [
            'pre-script'=>"$('.chat-list-body .load-more').remove()",
            '.chat-list-body'=>'>>' . view('andiwijaya::components.chat-admin-discussion-items', $params)->render()
          ];

        default:
          return [
            '.chat-list-body'=>view('andiwijaya::components.chat-admin-discussion-items', $params)->render()
          ];
      }
    }

    return view($this->view, $params);
  }

  public function loadMore(Request $request){

    $actions = explode('|', $request->get('action', 'view'));

    $filter = $request->get('filter');
    $after_id = $actions[1] ?? null;
    $item_per_page = 10;

    $model = ChatDiscussion::
    whereExists(function($query){
      $query->select(DB::raw(1))
        ->from('chat_message')
        ->whereRaw('chat_message.discussion_id = chat_discussion.id');
    })
      ->orderBy('updated_at', 'desc')
      ->orderBy('id', 'desc');

    if($filter != 'all')
      $model->where('unreplied_count', '>', 0);

    if(strlen($request->get('search')) > 0)
      $model->filter($request->all());

    if($after_id > 0){
      $discussions = collect([]);

      $append = false;
      $model->chunk(1000, function($rows) use($after_id, $item_per_page, &$discussions, &$append){

        foreach($rows as $row){

          if($row->id == $after_id) $append = true;

          if($append) $discussions->add($row);

          if(count($discussions) >= $item_per_page + 1) break;
        }

        if(count($discussions) >= $item_per_page + 1) return false;
      });
    }
    else{

      $discussions = $model
        ->limit($item_per_page + 1)
        ->get();
    }

    $after_id = count($discussions) >= $item_per_page + 1 ? $discussions[$item_per_page]->id : null;
    $discussions = $discussions->splice(0, $item_per_page);

    $params = [
      'extends'=>$this->extends,
      'discussions'=>$discussions,
      'view_discussion_item'=>$this->view_discussion_item,
      'view_discussion_no_item'=>$this->view_discussion_no_item,
      'filter'=>$filter,
      'after_id'=>$after_id,
      'channel_discussion'=>$this->channel_discussion,
      'title'=>$this->title
    ];

    if($request->ajax()){

      return [
        'pre-script'=>"$('.chat-list-body .load-more').remove()",
        '.chat-list-body'=>'>>' . view('andiwijaya::components.chat-admin-discussion-items', $params)->render()
      ];
    }
    abort(404);
  }

  public function show(Request $request, $id, array $extra = []){

    $item_per_page = 8;

    $action = isset(($actions = explode('|', $request->get('action')))[0]) ? $actions[0] : '';
    $prev_id = isset($actions[1]) ? $actions[1] : null;
    $last_id = isset($actions[1]) ? $actions[1] : null;

    $discussion = ChatDiscussion::find($id);

    $model = ChatMessage::whereDiscussionId($id)
      ->orderBy('created_at', 'desc')
      ->orderBy('id', 'desc');

    if($action == 'load-prev'){

      $messages = collect([]);

      $append = false;
      $model->chunk(1000, function($rows) use($prev_id, $item_per_page, &$messages, &$append){

        foreach($rows as $row){
          if($row->id == $prev_id) $append = true;
          if($append) $messages->add($row);
          if(count($messages) >= $item_per_page + 1) break;
        }
        if(count($messages) >= $item_per_page + 1) return false;
      });

    }
    else if($action == 'load-next'){

      $messages = collect([]);

      $append = true;
      $model->chunk(1000, function($rows) use($last_id, $item_per_page, &$messages, &$append){

        foreach($rows as $row){
          if($row->id == $last_id) $append = false;
          if($append) $messages->add($row);
        }
      });

    }
    else{

      $messages = $model->limit($item_per_page + 1)->get();
    }

    $prev_id = count($messages) >= $item_per_page + 1 ? $messages[$item_per_page]->id : null;
    $messages = $messages->splice(0, $item_per_page)->reverse();
    $last_id = $messages->pluck('id')->last();

    $params = [
      'discussion'=>$discussion,
      'messages'=>$messages,
      'prev_id'=>$prev_id,
      'last_id'=>$last_id,
      'view_message_item'=>$this->view_message_item,
      'view_message_foot'=>$this->view_message_foot,
      'storage'=>$this->storage
    ];

    switch($action){

      case 'load-prev':
        return [
          'pre-script'=>"$('.chat-message-body .load-prev').remove()",
          '.chat-message-body'=>'<<' . view('andiwijaya::components.chat-admin-message-items', $params)->render()
        ];

      case 'load-next':
        $returns = [];
        foreach($messages as $idx=>$message)
          $returns[] = [
            'type'=>'element',
            'html'=>view($this->view_message_item, compact('idx', 'message', 'storage'))->render(),
            'parent'=>'.chat-admin .chat-message-body'
          ];

        $returns[] = [ 'type'=>'script', 'script'=>"$('.chat-admin .chat-message-body').scrollToBottom()" ];

        return $returns;

      default:
        return [
          '.chat-message-head'=>view('andiwijaya::components.chat-admin-message-head', $params)->render(),
          '.chat-message-body'=>view('andiwijaya::components.chat-admin-message-body', $params)->render(),
          '.chat-message-foot'=>view('andiwijaya::components.chat-admin-message-foot', $params)->render(),
          'script'=>implode(';', [
            "$('.chat-message form[method=get]').attr('action', '/chat-admin/{$discussion->id}');",
            "$('.chat-message').attr('data-id', {$discussion->id})",
            "$('.chat-admin').chatadmin_resize().chatadmin_open()",
            "$('.chat-admin .chat-message-body').scrollToBottom()"
          ])
        ];

    }
  }

  public function store(Request $request){

    $method = action2method($request->get('action', 'send-message'));
    if(method_exists($this, $method))
      return call_user_func_array([ $this, $method ], func_get_args());
  }

  public function export(Request $request){

    return new StreamedResponse(
      function(){

        $handle = fopen('php://output', 'w');

        fputcsv($handle, [
          'Email',
          'Topic',
          'Date',
          'Type',
          'Message',
        ]);

        ChatMessage::orderBy('discussion_id', 'asc')
          ->chunk(1000, function($messages) use($handle){

            foreach($messages as $message){

              $obj = [
                $message->discussion->key,
                $message->discussion->title,
                $message->created_at,
                $message->direction == ChatMessage::DIRECTION_IN ? 'In' : 'Out',
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

  public function beginChat(Request $request){

    $discussion = ChatDiscussion::find($request->get('id'));

    if($discussion->handled_by > 0 && $discussion->handled_by != $this->user->id)
      exc(__('text.chat-admin-discussion-unable-to-begin-chat', [
        'name'=>ucwords(strtolower($discussion->handled_by_user->name ?? ''))
      ]));

    $discussion->handled_by = $this->user->id;
    $discussion->save();

    return [
      '.chat-admin .chat-message-foot'=>view($this->view_message_foot, compact('discussion'))->render()
    ];
  }

  public function handle(ChatEvent $event)
  {

    $updates = [];

    switch ($event->type) {

      case ChatEvent::TYPE_NEW_CHAT_MESSAGE:

        $online = count(Redis::pubsub('channels', $this->channel_discussion)) > 0;

        if ($online) {

          $updates[] = [
            'type' => 'element',
            'html' => view($this->view_discussion_item, ['discussion' => $event->discussion])->render(),
            'parent' => '.chat-admin .chat-list-body',
            'mode' => 'prepend'
          ];

          $updates[] = [
            'type' => 'element',
            'html' => view($this->view_message_item, ['message' => $event->message, 'storage' => $this->storage])->render(),
            'parent' => ".chat-admin .chat-message[data-id={$event->discussion->id}] .chat-message-body"
          ];
        }
        break;

    }

    $updates[] = [
      'type' => 'script',
      'script' => implode(';', [
        "$.lazy_load()",
        "$('.chat-admin .chat-message[data-id={$event->discussion->id}] .chat-message-body').scrollToBottom()"
      ])
    ];

    Redis::publish(
      $this->channel_discussion,
      json_encode($updates)
    );

    if(isset($event->message) && $event->message->direction == ChatMessage::DIRECTION_IN){

      $title = "Pesan baru dari {$event->discussion->key}";
      $description = substr($event->message->text, 0, 30) . '...';
      $target = "/chat-admin/{$event->discussion->id}";
      Redis::publish(
        'global-notification',
        json_encode([
          ['type' => 'script', 'script' => "$.notify({ title:\"{$title}\", description:\"{$description}\", target:\"{$target}\" })"]
        ])
      );
    }

  }

  public function sendMessage(Request $request){

    $discussion_id = $request->get('id');
    $discussion = ChatDiscussion::where([
      'id'=>$discussion_id
    ])
      ->first();

    if(!$discussion) exc(__('models.chat-message-unable-to-send-message'));

    $request->merge([ 'image_disk'=>$this->storage ]);

    $message = new ChatMessage([
      'discussion_id'=>$request->get('id'),
      'direction'=>ChatMessage::DIRECTION_OUT
    ]);
    $message->fill($request->all());
    $message->save();

    $request->merge([ 'action'=>'load-next|' . $request->get('last_id') ]);

    return [
      [ 'type'=>'script', 'script'=>"$('.chat-admin').chatadmin_clear()" ]
    ];
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