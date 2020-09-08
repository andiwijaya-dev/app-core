<?php

namespace Andiwijaya\AppCore\Console\Commands;

use Andiwijaya\AppCore\Models\ChatDiscussion;
use Illuminate\Console\Command;

class ChatDiscussionSendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat-discussion:send-notification {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
      ChatDiscussion::findOrFail($this->argument('id'))->sendEmailNotification();
    }
}
