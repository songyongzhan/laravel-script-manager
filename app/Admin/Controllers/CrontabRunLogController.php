<?php

namespace App\Admin\Controllers;

use App\Models\CrontabRunLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CrontabRunLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\CrontabRunLog';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CrontabRunLog());

        $grid->column('id', __('Id'));
        $grid->column('crontab_id', __('Crontab id'));
        $grid->column('title', __('Title'));
        $grid->column('result_content', __('Result content'));
        $grid->column('run_time', __('Run time'));
        $grid->column('status', __('Status'));
        $grid->column('sort_id', __('Sort id'));
        $grid->column('update_time', __('Update time'));
        $grid->column('create_time', __('Create time'));

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

        $show->field('id', __('Id'));
        $show->field('crontab_id', __('Crontab id'));
        $show->field('title', __('Title'));
        $show->field('result_content', __('Result content'));
        $show->field('run_time', __('Run time'));
        $show->field('status', __('Status'));
        $show->field('sort_id', __('Sort id'));
        $show->field('update_time', __('Update time'));
        $show->field('create_time', __('Create time'));

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
