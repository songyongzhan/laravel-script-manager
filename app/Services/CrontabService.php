<?php

namespace App\Services;

use App\Models\Crontab;
use Songyz\Simple\Orm\Core\DatabaseService;
use Songyz\Simple\Orm\Core\Model;

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
