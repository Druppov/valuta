<?php

// tests/Controller/CurrencyExchangeControllerTest.php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CurrencyExchangeControllerTest extends KernelTestCase
{
    private $entityManager;
    private $base;

    protected function setUp(): void
    {
        self::bootKernel();

        // Получаем контейнер Symfony для доступа к сервисам
        $container = self::$kernel->getContainer();

        // Получаем нужные сервисы из контейнера
        $this->entityManager = $container->get('doctrine')->getManager();
        $this->base = $container->getParameter('exchange_rate_service_provider');
    }

    public function testConvert()
    {
        $client = self::$kernel->getContainer()->get('test.client');

        $requestData = [
            'from' => 'EUR',
            'to' => 'USD',
            'amount' => 100,
        ];

        $client->request(Request::METHOD_POST, '/currency/convert', [], [], [], json_encode($requestData));

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('from', $responseData);
        $this->assertEquals('EUR', $responseData['from']);

        $this->assertArrayHasKey('to', $responseData);
        $this->assertEquals('USD', $responseData['to']);

        $this->assertArrayHasKey('amount', $responseData);
        $this->assertEquals(100, $responseData['amount']);

        $this->assertArrayHasKey('targetAmount', $responseData);
        $this->assertEquals(110, $responseData['targetAmount']);

        $this->assertArrayHasKey('responseMessage', $responseData);
        $this->assertEquals('Convert successfully.', $responseData['responseMessage']);
    }

}
