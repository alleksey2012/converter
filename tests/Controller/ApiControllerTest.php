<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{

    public function testConverter()
    {
        $client = static::createClient();
        // делаем тестовый запрос к API
        $client->request('GET', '/api/converter', ['base'=>'USD', 'summa'=>100, 'quoted'=>'eur']);
        // проверяем статус страницы
        $this->assertTrue($client->getResponse()->isSuccessful(), 'response status is 2xx');
        // проверяем в заголовках контент тип
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            '"Content-Type" header должен быть "application/json"'
        );
        // проверяем есть ли правльный ответ от API
        $this->assertStringContainsString('"success":true', $client->getResponse()->getContent(),'отсутствует выходной параметр "success":true');
    }


}