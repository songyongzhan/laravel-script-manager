<?php

namespace App\Services;

use Songyz\Core\DatabaseService;
use Songyz\Core\Model;
use App\Models\Crontab;

/**
 * crontab service
 * Class CrontabService
 * @date 2020-05-21 07:26:53
 */
class CrontabService extends DatabaseService {

    //主键
    protected $primaryId = 'id';

    /**
     * @inheritDoc
     */
    public function getModel(): Model
    {
        return new Crontab();
    }
}
