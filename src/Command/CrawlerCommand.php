<?php

namespace App\Command;

use App\Entity\CurrencyRate;
use App\Repository\CurrencyRateRepository;
use App\Service\ExchangeRateService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:crawler',
    description: 'Import exchange rates',
)]
class CrawlerCommand extends Command
{

    private readonly ExchangeRateService $exchangeRateService;
    private EntityManagerInterface $entityManager;
    private CurrencyRateRepository $currencyRateRepository;
    private $base;

    public function __construct(
        ExchangeRateService $exchangeRateService,
        EntityManagerInterface $entityManager,
        CurrencyRateRepository $currencyRateRepository
    )
    {
        $this->exchangeRateService = $exchangeRateService;
        $this->entityManager = $entityManager;
        $this->currencyRateRepository = $currencyRateRepository;
        $this->base = $this->exchangeRateService->getBase();

        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $exchangeRates = $this->exchangeRateService->fetchExchangeRates();

        $io = new SymfonyStyle($input, $output);
        $io->title('Exchange Rates');



        $ddd = new DateTime();
        foreach ($exchangeRates as $currency => $rate) {
            $io->text(sprintf('%s: %s', $currency, $rate));

            $currencyRate = $this->currencyRateRepository->deleteBySomeFields(base: $this->base, currentDate: $ddd->format("Y-m-d"), currencyCode: $currency);
            if ($currencyRate) {
                $currencyRate->setRate((float)$rate);
            } else {
                $currencyRate = new CurrencyRate();
                $currencyRate->setBase($this->base);
                $currencyRate->setCurrencyCodeId($currency);
                $currencyRate->setDatetime($ddd);
                $currencyRate->setRate((float)$rate);
                $this->entityManager->persist($currencyRate);
            }
        }
        $this->entityManager->flush();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
