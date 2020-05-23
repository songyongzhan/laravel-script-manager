<?php

namespace App\Managers;

use App\Services\CrontabService;
use Songyz\Simple\Orm\Core\DatabaseManager;
use Songyz\Simple\Orm\Core\DatabaseService;

/**
 * crontab
 * Class CrontabManager
 * @date 2020-05-21 07:26:18
 */
class CrontabManager extends DatabaseManager
{

    public function getService(): DatabaseService
    {
        return new CrontabService();
    }
}
