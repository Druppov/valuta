<?php
/**
 * @author Dmitriy Druppov
 * @company UnitedThinkers
 * @since 2024/06/17
 */

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EcbExchangeRateService implements ExchangeRateService
{
    const BASE = 'ECB';

    private $client;

    /**
     * @param $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchExchangeRates(): array
    {
        $url = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

        // Fetch the XML content
        $response = $this->client->request('GET', $url);
        $content = $response->getContent();

        // Parse the XML content
        $crawler = new Crawler($content);
        $exchangeRates = [];

        // Iterate over the exchange rates and extract the data
        $crawler->filter("")->each(function (Crawler $node) use (&$exchangeRates) {
            $currency = $node->attr('currency');
            $rate = $node->attr('rate');

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
