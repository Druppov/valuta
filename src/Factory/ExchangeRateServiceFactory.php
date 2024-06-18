<?php
/**
 * @author Dmitriy Druppov
 * @company UnitedThinkers
 * @since 2024/06/18
 */

namespace App\Factory;

use App\Service\CbrExchangeRateService;
use App\Service\EcbExchangeRateService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRateServiceFactory
{
    private $params;
    private $httpClient;

    public function __construct(ParameterBagInterface $params, HttpClientInterface $httpClient)
    {
        $this->params = $params;
        $this->httpClient = $httpClient;
    }

    public function create()
    {
        $provider = $this->params->get('exchange_rate_service_provider');
        switch ($provider) {
            case 'CBR':
                return new CbrExchangeRateService($this->httpClient);
            case 'ECB':
                return new EcbExchangeRateService($this->httpClient);
            default:
                throw new \InvalidArgumentException("Unsupported exchange rate service provider: $provider");
        }
    }
}
