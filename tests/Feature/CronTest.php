<?php

namespace Tests\Feature;

use Cron\CronExpression;
use Tests\TestCase;

class CronTest extends TestCase
{

    public function testA(){

        for ($i=1;$i<10;$i++){
            $cron = CronExpression::factory('1 2-4 * 4,5,6 */3');

            //isValidExpression //判断是不是表达式
            dump($cron->getExpression('foo'));
            dump($cron->getPreviousRunDate());
            $this->assertEquals('1', $cron->getExpression(CronExpression::MINUTE));
            $this->assertEquals('2-4', $cron->getExpression(CronExpression::HOUR));
            $this->assertEquals('*', $cron->getExpression(CronExpression::DAY));
            $this->assertEquals('4,5,6', $cron->getExpression(CronExpression::MONTH));
            $this->assertEquals('*/3', $cron->getExpression(CronExpression::WEEKDAY));
            $this->assertEquals('1 2-4 * 4,5,6 */3', $cron->getExpression());
            $this->assertEquals('1 2-4 * 4,5,6 */3', (string) $cron);
            $this->assertNull($cron->getExpression('foo'));
            sleep(1);
        }


    }


}
