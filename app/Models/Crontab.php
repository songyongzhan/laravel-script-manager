<?php

namespace App\Models;

use Songyz\Core\Model;

class Crontab extends Model
{
    protected $table = 'crontab';

    const UPDATED_AT = 'update_time';
    const CREATED_AT = 'create_time';

}
