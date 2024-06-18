<?php

namespace Tests\Service;

use App\Service\CbrExchangeRateService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CbrExchangeRateServiceTest extends TestCase
{
    public function testFetchExchangeRates()
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);

        $responseMock->method('getContent')->willReturn($this->getFakeXmlContent());
        $httpClientMock->method('request')->willReturn($responseMock);

        $cbrService = new CbrExchangeRateService($httpClientMock);
        $exchangeRates = $cbrService->fetchExchangeRates();

        $this->assertIsArray($exchangeRates);
        $this->assertArrayHasKey('USD', $exchangeRates);
        $this->assertArrayHasKey('EUR', $exchangeRates);
        $this->assertEquals(73.12, $exchangeRates['USD']);
        $this->assertEquals(86.45, $exchangeRates['EUR']);
    }

    private function getFakeXmlContent(): string
    {
        return <<<XML
        <ValCurs Date="18.06.2024" name="Foreign Currency Market">
            <Valute ID="R01235">
                <NumCode>840</NumCode>
                <CharCode>USD</CharCode>
                <Nominal>1</Nominal>
                <Name>Доллар США</Name>
                <Value>73.12</Value>
            </Valute>
            <Valute ID="R01239">
                <NumCode>978</NumCode>
                <CharCode>EUR</CharCode>
                <Nominal>1</Nominal>
                <Name>Евро</Name>
                <Value>86.45</Value>
            </Valute>
        </ValCurs>
        XML;
    }
}
