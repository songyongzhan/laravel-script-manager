<?php

namespace App\Console\Commands;

use App\Managers\CrontabManager;
use App\Models\Crontab;
use App\Models\CrontabRunLog;
use App\Services\HttpClientService;
use Carbon\Carbon;
use Cron\CronExpression;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Songyz\Common\Library\Tools;

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
                '-',
                'start',
                'restart',
                'stop',
            ];
            $commandNames = [
                ['-', ''],
                ['启动', 'start'],
                ['重新启动', 'restart'],
                ['停止', 'stop'],
            ];
            $this->table(['命令', '描述'], $commandNames);
            $command = $this->choice('请输入命令', $commandMaps, 1);
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

    //脚本启动
    private function start()
    {
        $this->ws = new \swoole_server('127.0.0.1', config('script.port'));
        $this->ws->on('start', function ($ws) {
            swoole_set_process_name(config('script.server_name'));
        });
        $this->ws->on('workerStart', function ($ws, $workerId) {
            $ws->tick(1000, function ($timerId) {
                $this->info(Tools::getCurrentDate() . ' 执行中... 内存使用率:' . $this->getMemoryUsage());
            });

            $this->info('ws workerStart 启动...');
            $this->setTimer($ws, $workerId);
        });

        $this->ws->on('receive', function ($server, $fd, $reactor_id, $data) {

        });

        $this->ws->on('workerstop', function (swoole_server $ws, $workerId) {
            Log::channel('shell')->info(Tools::getCurrentDate() . 'workerstop停止 workerId' . $workerId);
        });

        $this->ws->on('managerstart', function (swoole_server $ws) {

            Log::channel('shell')->info(Tools::getCurrentDate() . 'managerstart启动');
        });

        $this->ws->on('managerstop', function (swoole_server $ws) {
            Log::channel('shell')->info(Tools::getCurrentDate() . 'managerstop退出');
        });

        $this->ws->on('shutdown', function (swoole_server $ws) {
            Log::channel('shell')->info(Tools::getCurrentDate() . '脚本服务程序退出');
        });

        $this->ws->start();
    }

    private function restart()
    {
        $this->alert('执行restart命令：');
        $shellCommand = 'kill -USR1 `pidof ' . config('script.server_name') . '`';
        exec($shellCommand, $out, $status);
        $this->info('restart执行结果:' . json_encode($out, JSON_UNESCAPED_UNICODE) . ' return_var:' . $status);
    }

    private function stop()
    {
        $this->alert('执行stop命令：');
        $shellCommand = 'kill -9 `pidof ' . config('script.server_name') . '`';
        exec($shellCommand, $out, $status);
        if ($status == 0) {
            $this->info('服务已关闭');
        } else {
            $this->info('服务关闭失败' . json_encode($out, JSON_UNESCAPED_UNICODE) . ' return_var:' . $status);
        }
    }

    private function setTimer($ws, $workerId)
    {
        $list = $this->getCrontabList();

        $phpPath = $this->findPhpPath();
        $outPath = config('script.output_path');
        $today = date('Ymd');
        $service = new HttpClientService();

        foreach ($list->results as $key => $val) {
            if ($val['maxNum'] != 0 && $val['executeNum'] > $val['maxNum']) {
                continue;
            }

            $whichVal = 'val' . $val['id'];
            $$whichVal = $val;

            if ($val['scriptNav'] == 2) { //每隔多长时间执行一次
                $ws->tick($val['everyTimeRun'],
                    function ($timerId, $val) use ($phpPath, $outPath, $today, $service) {
                        if (!isset($this->ws->{$timerId})) {
                            $this->ws->{$timerId} = $val['executeNum'];
                        }
                        $this->runScript($val, $timerId, $phpPath, $outPath, $today, $service);
                    }, $$whichVal);

            } else {
                //分时日月周
                $ws->tick(60000, function ($timerId, $val) use ($phpPath, $outPath, $today, $service) {
                    if (!isset($this->ws->{$timerId})) {
                        $this->ws->{$timerId} = $val['executeNum'];
                    }
                    $cron = CronExpression::factory($val['schedule']);
                    if ($cron->isDue()) {
                        //判断是不是到了执行时间
                        $this->runScript($val, $timerId, $phpPath, $outPath, $today, $service);
                    }
                }, $$whichVal);
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


            /* if ($val['crontanType'] == '1') { //脚本 artisan
                 $command = $phpPath . ' ' . $val['content'] . ' 2>&1 >>' . $outPath . DIRECTORY_SEPARATOR . $today . '.log';
                 exec($command, $output, $status);
                 $result = ['command' => $command, 'out' => $output, 'status' => $status];
                 Crontab::getQuery()->find($val['id'])->update(['execute_num' => DB::raw('execute_num+1')]);
                 $this->saveRunLog($val['id'], $val['title'], $result);
             } elseif ($val['crontanType'] == '2') { //脚本 http请求
                 $service = new HttpClientService();
                 //一般情况下，就是去访问远程 这里通过curl的形式去请求
                 $data = $service->fetchGet($val['content']);
                 $result = array_merge([$data], ['httpUrl' => $val['content']]);
                 Crontab::getQuery()->find($val['id'])->update(['execute_num' => DB::raw('execute_num+1')]);
                 $this->saveRunLog($val['id'], $val['title'], $result);
             }*/

        }

        return true;
    }

    // 执行脚本
    private function runScript($val, $timerId, $phpPath, $outPath, $today, $service)
    {
        if ($val['maxNum'] != 0 && $this->ws->{$timerId} >= $val['maxNum']) {
            //脚本执行次数到了，需要退出
            swoole_timer_clear($timerId);
        }

        $submitTime = Carbon::createFromTimeString($val['startTime']);
        $endTime = Carbon::createFromTimeString($val['endTime']);
        $currentTime = Carbon::parse();
        if ($currentTime->gt($endTime)) {
            swoole_timer_clear($timerId);
        } elseif (!$currentTime->between($submitTime, $endTime)) {
            return; //如果不在时间范围内，则证明脚本未到执行的时间
        }

        if ($val['crontanType'] == '1') { //脚本 artisan
            $command = $phpPath . ' ' . $val['content'] . ' 2>&1 >>' . $outPath . DIRECTORY_SEPARATOR . $today . '.log';
            exec($command, $output, $status);
            $result = ['command' => $command, 'out' => $output, 'status' => $status];
            Crontab::getQuery()->find($val['id'])->update(['execute_num' => DB::raw('execute_num+1')]);
            $this->saveRunLog($val['id'], $val['title'], $result);
            unset($command);
        } elseif ($val['crontanType'] == '2') { //脚本 http请求
            //一般情况下，就是去访问远程 这里通过curl的形式去请求
            $data = $service->fetchGet($val['content']);
            $result = array_merge([$data], ['httpUrl' => $val['content']]);
            Crontab::getQuery()->find($val['id'])->update(['execute_num' => DB::raw('execute_num+1')]);
            $this->saveRunLog($val['id'], $val['title'], $result);
            unset($data);
        }
        $this->ws->{$timerId} = $this->ws->{$timerId} + 1;
        $val['executeNum'] = $val['executeNum'] + 1;
        unset($result, $val);
    }

    /**
     * 获取计划任务列表
     * getCrontabList
     * @return \Songyz\Simple\Orm\Library\DefaultPage
     *
     * @date 2020/5/21 15:31
     */
    private function getCrontabList()
    {
        $manager = new CrontabManager();
        $where = [get_where_condition('status', 1)];

        return $manager->getList($where, [], 100);
    }

    //将数据保存到数据库
    private function saveRunLog(int $id, string $title, array $result)
    {
        $data = [
            'crontab_id' => $id,
            'title' => $title,
            'result_content' => json_encode($result, JSON_UNESCAPED_UNICODE),
            'run_time' => Tools::getCurrentDate()
        ];
        $res = CrontabRunLog::getQuery()->create($data);
        Log::channel('shell')->info($id . ' ' . $title . ' 插入插入数据库:' . $res->id, $data);
    }

    /**
     * 获取php所在位置
     * findPhpPath
     * @return mixed
     *
     * @date 2020/5/23 17:01
     */
    private function findPhpPath()
    {
        $shellCommand = 'which php';
        exec($shellCommand, $out, $status);
        if ($status != 0) {
            $this->info('没有找到php所在位置');
            exit;
        }

        return $out[0];
    }


    private function getMemoryUsage()
    {
        // MEMORY
        if (false === ($str = @file("/proc/meminfo"))) {
            return false;
        }
        $str = implode("", $str);
        preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s",
            $str, $buf);

        $memTotal = round($buf[1][0] / 1024, 2);
        $memFree = round($buf[2][0] / 1024, 2);
        $memUsed = $memTotal - $memFree;
        return (floatval($memTotal) != 0) ? round($memUsed / $memTotal * 100, 2) : 0;
    }


}
