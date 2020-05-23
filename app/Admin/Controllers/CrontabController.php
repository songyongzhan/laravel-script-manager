<?php

namespace App\Admin\Controllers;

use App\Models\Crontab;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CrontabController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Crontab';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Crontab());

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('schedule', __('Schedule'));
        $grid->column('status', __('Status'));
        $grid->column('sort_id', __('Sort id'));
        $grid->column('update_time', __('Update time'));
        $grid->column('create_time', __('Create time'));
        $grid->column('sleep', __('Sleep'));
        $grid->column('content', __('Content'));
        $grid->column('crontan_type', __('Crontan type'));
        $grid->column('max_num', __('Max num'));
        $grid->column('execute_num', __('Execute num'));
        $grid->column('start_time', __('Start time'));
        $grid->column('end_time', __('End time'));

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

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('schedule', __('Schedule'));
        $show->field('status', __('Status'));
        $show->field('sort_id', __('Sort id'));
        $show->field('update_time', __('Update time'));
        $show->field('create_time', __('Create time'));
        $show->field('sleep', __('Sleep'));
        $show->field('content', __('Content'));
        $show->field('crontan_type', __('Crontan type'));
        $show->field('max_num', __('Max num'));
        $show->field('execute_num', __('Execute num'));
        $show->field('start_time', __('Start time'));
        $show->field('end_time', __('End time'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Crontab());

        $form->text('title', __('Title'));
        $form->text('schedule', __('Schedule'));
        $form->switch('status', __('Status'))->default(1);
        $form->number('sort_id', __('Sort id'));
        $form->number('sleep', __('Sleep'));
        $form->text('content', __('Content'));
        $form->number('crontan_type', __('Crontan type'));
        $form->number('max_num', __('Max num'));
        $form->number('execute_num', __('Execute num'));
        $form->datetime('start_time', __('Start time'))->default(date('Y-m-d H:i:s'));
        $form->datetime('end_time', __('End time'))->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
