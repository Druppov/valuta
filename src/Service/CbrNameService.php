<?php
/**
 * @author Dmitriy Druppov
 * @company UnitedThinkers
 * @since 2024/06/17
 */

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CbrNameService
{

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
            $name = $node->filter('Name')->text();

            if ($currency && $name) {
                $exchangeRates[$currency] = $name;
            }
        });

        return $exchangeRates;
    }

}