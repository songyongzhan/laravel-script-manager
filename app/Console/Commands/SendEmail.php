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


        $list = $this->getCrontabList();

        foreach ($list->results as $key => $val) {

            if ($val['crontanType'] == '1') { //脚本 artisan
                //exec($val['content'], $output, $status);
//                dump($val['content']);
                exec(" E:\phpStudy\php\php-5.2.17\php -r \"echo 11;\" ", $output, $status);
                Log::info($output);
                Log::info($status);

                //Log::info($output);
                //Log::info($status);

            } elseif ($val['crontanType'] == '2') { //脚本 http请求

                $service = new HttpClientService();
                //一般情况下，就是去访问远程 这里通过curl的形式去请求
                $data = $service->fetchGet($val['content']);


                //Log::info(file_get_contents($val['content']));
            }
        }

        $email = $this->option('email');

        if (empty($email)) {
            Log::error('邮箱地址为空，直接返回');
            return;
        }

        Log::info('发送邮件: ' . $email . ' 时间:' . microtime(true));
    }

    /**
     * 获取计划任务列表
     * getCrontabList
     * @return \Songyz\Library\DefaultPage
     *
     * @date 2020/5/21 15:31
     */
    private function getCrontabList()
    {
        $manager = new CrontabManager();

        return $manager->getList([], [], 100);
    }
}
