<?php
/**
 * @author Dmitriy Druppov
 * @company UnitedThinkers
 * @since 2024/06/17
 */

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CbrExchangeRateService implements ExchangeRateService
{

    const BASE = "CBR";

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchExchangeRates(): array
    {
        $url = 'https://www.cbr.ru/scripts/XML_daily.asp';

        // Fetch the XML content
        $response = $this->client->request('GET', $url);
        $content = $response->getContent();

        // Parse the XML content
        $crawler = new Crawler($content);
        $exchangeRates = [];

        // Iterate over the exchange rates and extract the data
        $crawler->filter('ValCurs > Valute')->each(function (Crawler $node) use (&$exchangeRates) {
            $currency = $node->filter('CharCode')->text();
            $rate = $node->filter('Value')->text();
            $rate = str_replace(',', '.', $rate); // Convert to float-compatible format

            if ($currency && $rate) {
                $exchangeRates[$currency] = (float) $rate;
            }
        });

        return $exchangeRates;
    }

    public function getBase() : string
    {
        return self::BASE;
    }

}
