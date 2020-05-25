<?php

namespace App\Tests\Service;

use App\Service\CbrService;
use PHPUnit\Framework\TestCase;

class CbrServiceTest extends TestCase
{
    public function testValutes()
    {
        $cbr = new CbrService('/home/alleksey/phpProject/converter/var/cache/daily.xml');

        $result = $cbr->getValutes();

        // проверяем есть ли данные в массиве
        $this->assertGreaterThan(0, count($result),"должен вернуться массив с количеством элементов > 0");

        // Проверяем есть ли валюта USD
        $this->assertTrue(array_key_exists('USD',$result));

    }
}