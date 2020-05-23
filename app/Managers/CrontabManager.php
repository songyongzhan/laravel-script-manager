<?php

namespace App\Managers;

use Songyz\Core\DatabaseManager;
use Songyz\Core\DatabaseService;
use App\Services\CrontabService;

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
