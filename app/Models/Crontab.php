<?php

namespace App\Models;

use Songyz\Simple\Orm\Core\Model;

class Crontab extends Model
{
    protected $table = 'crontab';

    const UPDATED_AT = 'update_time';
    const CREATED_AT = 'create_time';

}
