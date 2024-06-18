<?php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CurrencyExchangeControllerTest extends WebTestCase
{
    public function testConvert()
    {
        $client = static::createClient();

        // Set content type to application/x-www-form-urlencoded
        $client->setServerParameter('CONTENT_TYPE', 'application/x-www-form-urlencoded');

        $client->request('POST', '/currency/convert', [
            'from' => 'USD',
            'to' => 'EUR',
            'amount' => 100
        ]);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);

        // Add debugging output to understand the structure of the response
        if (!isset($data['from'])) {
            echo "Response JSON: " . $client->getResponse()->getContent() . "\n";
        }

        $this->assertArrayHasKey('from', $data);
        $this->assertArrayHasKey('to', $data);
        $this->assertArrayHasKey('amount', $data);
        $this->assertArrayHasKey('targetAmount', $data);
        $this->assertArrayHasKey('responseMessage', $data);
    }
}
