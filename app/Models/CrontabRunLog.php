<?php

namespace App\Models;

use Songyz\Simple\Orm\Core\Model;

class CrontabRunLog extends Model
{
    protected $table = 'crontab_run_log';
    protected $underlineToHump = false;

    const UPDATED_AT = 'update_time';
    const CREATED_AT = 'create_time';

}
