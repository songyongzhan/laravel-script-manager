<?php

namespace App\Console\Commands;

use App\Managers\CrontabManager;
use App\Services\HttpClientService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WsServer extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ws:run
    {--command= : start/restart/stop}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'script shell server';

    /**
     * 启动的swoole服务
     */
    private $ws;

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
        $command = $this->option('command');
        if (empty($command)) {
            //输出选择项
            $commandMaps = [
                'start',
                'restart',
                'stop',
            ];
            $commandNames = [
                ['启动', 'start'],
                ['重新启动', 'restart'],
                ['停止', 'stop'],
            ];
            $this->table(['命令', '描述'], $commandNames);
            $command = $this->choice('请输入命令', $commandMaps, 0);
        }


        switch ($command) {
            case 'start':
                $this->start();
                break;
            case 'restart':
                $this->restart();
                break;
            case 'stop':
                $this->stop();
                break;
            default :
                $this->alert('你要干啥');
        }
    }


    private function start()
    {
        $this->ws = new swoole_server('127.0.0.1', config('script.port'));

        $this->ws->on('start', function ($ws) {
            swoole_set_process_name(config('script.server_name'));
        });

        $this->ws->on('workerStart', function ($ws, $workerId) {
            $this->setTimer($ws, $workerId);
        });

        $this->ws->start();
    }

    private function restart()
    {

        exec('kill -USR1 `pidof ' . config('script.server_name') . '`', $out, $status);

        dump($out);
        dump($status);

    }

    private function stop()
    {
        $this->ws->stop();

        $this->info('服务已关闭');
    }


    private function setTimer()
    {
        $list = $this->getCrontabList();


        foreach ($list->results as $key => $val) {

            if ($val['execute_num'] > $val['max_num']) {
                continue;
            }


//            swoole_timer_tick(1000,function($timerId){
//                $monitor = new Monitor();
//                $monitor -> serverName('nginx');
//                echo "每隔1000毫秒执行一次".$timerId."\n";
//                unset($monitor);
//            });
//
//            swoole_timer_tick(3000,function($timerId){
//                $monitor = new Monitor();
//                $monitor->serverName('mysql');
//                echo "每隔3秒执行一次".$timerId."\n";
//                unset($monitor);
//            });


            if ($val['crontanType'] == '1') { //脚本 artisan
                exec(" E:\phpStudy\php\php-5.2.17\php -r \"echo 11;\" ", $output, $status);
                Log::info($output);
                Log::info($status);
            } elseif ($val['crontanType'] == '2') { //脚本 http请求
                $service = new HttpClientService();
                //一般情况下，就是去访问远程 这里通过curl的形式去请求
                $data = $service->fetchGet($val['content']);
            }

        }
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
