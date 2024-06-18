<?php
/**
 * @author Dmitriy Druppov
 * @company UnitedThinkers
 * @since 2024/06/17
 */

namespace App\Service;

interface ExchangeRateService
{

    public function fetchExchangeRates(): array;

    public function getBase() : string;

}
