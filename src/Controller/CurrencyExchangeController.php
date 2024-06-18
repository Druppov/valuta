<?php

namespace App\Controller;

use App\Entity\CurrencyRate;
use App\Service\ConvertHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CurrencyExchangeController extends AbstractController
{
    private $params;
    private $base;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
        $this->base = $this->params->get('exchange_rate_service_provider');
    }

    #[Route('/currency', name: 'app_currency_exchange')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $rates = $entityManager->getRepository(CurrencyRate::class)->findAll();

        $data = [];
        foreach ($rates as $rate) {
            $data[] = [
                'base' => $rate->getBase(),
                'currencyCode' => $rate->getCurrencyCodeId(),
                'rate' => $rate->getRate(),
                'date' => $rate->getDatetime()
            ];
        }

        return $this->json($data);
    }

    #[Route('/currency/convert', name: 'convert', methods:['post'])]
    public function convert(EntityManagerInterface $entityManager, Request $request)
    {
        $responseMessage = "";


        $currencyFrom = $request->request->get('from');
        if (empty($currencyFrom) || $currencyFrom == 'undefined') {
            $responseMessage = ConvertHelper::formatResponseMessage($responseMessage, "Parameter `from` not found.");
        }

        $currencyTo = $request->request->get('to');
        if (empty($currencyTo) || $currencyTo == 'undefined') {
            $responseMessage = ConvertHelper::formatResponseMessage($responseMessage, "Parameter `to` not found.");
        }

        $amount = (float)$request->request->get('amount');
        if ($amount < 0) {
            $responseMessage = ConvertHelper::formatResponseMessage($responseMessage, "Parameter `amount` must be positive.");
        }

        if (!empty($responseMessage)) {
            return $this->json([
                'responseMessage' => $responseMessage
            ]);
        }

        $toDay = date("Y-m-d");

        $fromCurrency = $entityManager->getRepository(CurrencyRate::class)->findOneBySomeFields(base: $this->base, currentDate: $toDay, currencyCode: $currencyFrom);
        $toCurrency = $entityManager->getRepository(CurrencyRate::class)->findOneBySomeFields(base: $this->base, currentDate: $toDay, currencyCode: $currencyTo);

        if (!empty($currencyFrom) && !isset($fromCurrency)) {
            $responseMessage = ConvertHelper::formatResponseMessage($responseMessage, sprintf("%s not supported.", $currencyFrom));
        }
        if (!empty($currencyTo) && !isset($toCurrency)) {
            $responseMessage = ConvertHelper::formatResponseMessage($responseMessage, sprintf("%s not supported.", $currencyTo));
        }

        if (!empty($responseMessage)) {
            return $this->json([
                'responseMessage' => $responseMessage
            ]);
        }

        $targetAmount = ConvertHelper::convert($fromCurrency->getRate(), $toCurrency->getRate(), $amount);

        $data = [
            'from' => $currencyFrom,
            'to' => $currencyTo,
            'amount' => $amount,
            'targetAmount' => $targetAmount,
            'responseMessage' => "Convert successfully."
        ];

        return $this->json($data);
    }
}
