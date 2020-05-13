<?php

namespace Andiwijaya\AppCore\Console\Commands;

use Andiwijaya\AppCore\Models\ChatDiscussion;
use Illuminate\Console\Command;

class ChatDiscussionNotifyUnsent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat-discussion:notify-unsent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification for unsent chat message';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      ChatDiscussion::notifyUnsent(function($message){

        $this->info($message);
      });
    }
}
