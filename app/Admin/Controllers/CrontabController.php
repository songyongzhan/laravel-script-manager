<?php

namespace App\Admin\Controllers;

use App\Models\Crontab;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Form\Row;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Artisan;

class CrontabController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '脚本管理';


    protected $crontanType = [
        '1' => '脚本',
        '2' => 'Http'
    ];

    protected $status = [
        '1' => '启用',
        '0' => '禁用'
    ];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Crontab());

        $grid->column('id', __('crontab.id'));
        $grid->column('title', __('crontab.title'));
        $grid->column('schedule', __('crontab.schedule'));
        $grid->column('status', __('crontab.status'))->using($this->status);
        $grid->column('content', __('crontab.content'));
        $grid->column('crontan_type', __('crontab.crontan_type'))->using($this->crontanType);
        $grid->column('max_num', __('crontab.max_num'));
        $grid->column('execute_num', __('crontab.execute_num'));
        $grid->column('start_time', __('crontab.start_time'));
        $grid->column('end_time', __('crontab.end_time'));

        $grid->column('update_time', __('crontab.update_time'));
        $grid->column('create_time', __('crontab.create_time'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Crontab::findOrFail($id));

        $show->field('id', __('crontab.id'));
        $show->field('title', __('crontab.title'));
        $show->field('schedule', __('crontab.schedule'));
        $show->field('status', __('crontab.status'));
        $show->field('content', __('Content'));
        $show->field('crontan_type', __('crontab.crontan_type'));
        $show->field('max_num', __('crontab.max_num'));
        $show->field('execute_num', __('crontab.execute_num'));
        $show->field('start_time', __('crontab.start_time'));
        $show->field('end_time', __('crontab.end_time'));
        $show->field('update_time', __('crontab.update_time'));
        $show->field('create_time', __('crontab.create_time'));

        return $show;
    }

    public function platformInfo()
    {
        //潘窜程序是否运行， 内存使用情况
        $shellCommand = 'netstat -ntlp | grep ' . config('script.port') . ' | wc -l';
	$memory = $this->getMemoryUsage();

        exec($shellCommand, $result, $status);

        if ($status != 0 || (isset($result[0]) && $result[0] != 1)) {
            return response()->json(['code' => 1, 'message' => '失败', 'data' => ['wsRun' => 0, 'memory' => $memory]]);
        }

        //证明程序已经启动
        return response()->json(['code' => 1, 'message' => '成功', 'data' => ['wsRun' => 1, 'memory' => $memory]]);
    }

    public function reStart()
    {
        $result = Artisan::call('ws:run --command=restart');
        return response()->json(['code' => 0, 'message' => '成功', 'data' => $result]);
    }

    public function start()
    {
	exec('/usr/local/php/php7.2.12/bin/php /data/www/laravel-script-manager/artisan ws:run --command=start', $result, $status);
        print_r($result);
	var_dump($status);
	exit;
	$result = Artisan::call('ws:run --command=start');
        return response()->json(['code' => 0, 'message' => '成功', 'data' => $result]);
    }

    public function stop()
    {
        //$result = Artisan::call('ws:run --command=stop');
       
        exec('/usr/local/php/php7.2.12/bin/php /data/www/laravel-script-manager/artisan ws:run --command=stop', $result, $status);
	var_dump($result,$status);
	return response()->json(['code' => 0, 'message' => '成功', 'data' => $result]);
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Crontab());

        $form->text('title', __('crontab.title'));
        $form->text('schedule', __('crontab.schedule'));
        $form->switch('status', __('crontab.status'))->default(1);
        $form->text('content', __('crontab.content'));
        $form->text('crontan_type', __('crontab.crontan_type'));
        $form->text('max_num', __('crontab.max_num'));
        $form->text('execute_num', __('crontab.execute_num'));
        $form->datetime('start_time', __('crontab.start_time'))->default(date('Y-m-d H:i:s'));
        $form->datetime('end_time', __('crontab.end_time'))->default(date('Y-m-d H:i:s'));

        return $form;
    }

    public function platform(Content $content)
    {
        // 选填
        $content->header('脚本管理平台');

        // 选填
        $content->description('管理常用的脚本');

        // 添加面包屑导航 since v1.5.7
        $content->breadcrumb(
            ['text' => '首页', 'url' => '/admin'],
            ['text' => '平台管理']
        );
        $content->view('crontab.platform');

        return $content;
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
