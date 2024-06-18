<?php

namespace Tests\Controller;

use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CurrencyExchangeControllerTest extends WebTestCase
{
    public function testConvert()
    {
        $client = static::createClient();

        $client->request('POST', '/currency/convert', [
            'from' => 'USD',
            'to' => 'EUR',
            'amount' => 100
        ]);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('from', $data);
        $this->assertArrayHasKey('to', $data);
        $this->assertArrayHasKey('amount', $data);
        $this->assertArrayHasKey('targetAmount', $data);
        $this->assertArrayHasKey('responseMessage', $data);
    }
}
