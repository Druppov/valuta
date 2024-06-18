<?php
/**
 * @author Dmitriy Druppov
 * @company UnitedThinkers
 * @since 2024/06/17
 */

namespace App\Service;

class ConvertHelper
{
    public static function formatResponseMessage(string $responseMessage, string $message) : string {
        if (empty($responseMessage)) {
            $responseMessage = $message;
        } else {
            $responseMessage .= sprintf(PHP_EOL . "%s", $message);
        }

        return $responseMessage;
    }

    public static function convert(float $rateFrom, float $rateTo, float $amount) : float {

        // Convert from the source currency to EUR/RUB
        $amountIn = $amount / $rateFrom;

        // Convert from EUR/RUB to the target currency
        $amountInTargetCurrency = $amountIn * $rateTo;

        return $amountInTargetCurrency;
    }

}
