<?php

namespace Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\EcbExchangeRateService;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class EcbExchangeRateServiceTest extends TestCase
{
    public function testFetchExchangeRates()
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);

        $responseMock->method('getContent')->willReturn($this->getFakeXmlContent());
        $httpClientMock->method('request')->willReturn($responseMock);

        $ecbService = new EcbExchangeRateService($httpClientMock);
        $exchangeRates = $ecbService->fetchExchangeRates();

        $this->assertIsArray($exchangeRates);
        $this->assertArrayHasKey('USD', $exchangeRates);
        $this->assertArrayHasKey('GBP', $exchangeRates);
        $this->assertEquals(1.18, $exchangeRates['USD']);
        $this->assertEquals(0.85, $exchangeRates['GBP']);
    }

    private function getFakeXmlContent(): string
    {
        return <<<XML
        <gesmes:Envelope xmlns:gesmes="http://www.gesmes.org/xml/2002-08-01" xmlns="http://www.ecb.int/vocabulary/2002-08-01/eurofxref">
            <Cube>
                <Cube time='2024-06-18'>
                    <Cube currency='USD' rate='1.18'/>
                    <Cube currency='GBP' rate='0.85'/>
                </Cube>
            </Cube>
        </gesmes:Envelope>
        XML;
    }
}
