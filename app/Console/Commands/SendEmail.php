<?php

namespace App\Console\Commands;

use App\Managers\CrontabManager;
use App\Services\HttpClientService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email {--email= : 邮箱地址}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每隔100毫秒执行一次';

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
        Log::info('success... email');

        $this->info('执行成功');
    }


}
