<?php

namespace App\Admin\Controllers;

use App\Models\CrontabRunLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Show;

class CrontabRunLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '脚本执行日志';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CrontabRunLog());

        $grid->column('id', __('crontab.id'));
        $grid->column('crontab_id', __('crontab.crontab_id'));
        $grid->column('title', __('crontab.title'));
        $grid->column('run_time', __('crontab.run_time'));
        $grid->column('status', __('crontab.status'));
        $grid->column('update_time', __('crontab.update_time'));
        $grid->column('create_time', __('crontab.create_time'));

        $grid->enableHotKeys();

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
        $show = new Show(CrontabRunLog::findOrFail($id));
        $show->field('id', __('crontab.id'));
        $show->field('crontab_id', __('crontab.crontab_id'));
        $show->field('title', __('crontab.title'));
        $show->field('result_content', __('crontab.result_content'));
        $show->field('run_time', __('crontab.run_time'));
        $show->field('status', __('crontab.status'));
        $show->field('update_time', __('crontab.update_time'));
        $show->field('create_time', __('crontab.create_time'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CrontabRunLog());

        $form->number('crontab_id', __('Crontab id'));
        $form->text('title', __('Title'));
        $form->textarea('result_content', __('Result content'));
        $form->datetime('run_time', __('Run time'))->default(date('Y-m-d H:i:s'));
        $form->switch('status', __('Status'))->default(1);
        $form->number('sort_id', __('Sort id'));

        return $form;
    }
}
