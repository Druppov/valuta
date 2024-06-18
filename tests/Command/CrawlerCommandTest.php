<?php

// tests/Command/CrawlerCommandTest.php
namespace Tests\Command;

use App\Command\CrawlerCommand;
use App\Entity\CurrencyRate;
use App\Repository\CurrencyRateRepository;
use App\Service\ExchangeRateService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class CrawlerCommandTest extends TestCase
{
    private $exchangeRateService;
    private $entityManager;
    private $currencyRateRepository;
    private $commandTester;

    protected function setUp(): void
    {
        $this->exchangeRateService = $this->createMock(ExchangeRateService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->currencyRateRepository = $this->createMock(CurrencyRateRepository::class);

        $command = new CrawlerCommand(
            $this->exchangeRateService,
            $this->entityManager,
            $this->currencyRateRepository
        );

        $application = new Application();
        $application->add($command);

        $this->commandTester = new CommandTester($command);
    }

    public function testExecute()
    {
        // Настраиваем поведение мок-объектов
        $this->exchangeRateService
            ->method('getBase')
            ->willReturn('CBR');

        $this->exchangeRateService
            ->method('fetchExchangeRates')
            ->willReturn([
                'EUR' => 0.84,
                'GBP' => 0.74,
            ]);

        $this->currencyRateRepository
            ->method('deleteBySomeFields')
            ->willReturn(null);

        $this->entityManager
            ->expects($this->exactly(2))
            ->method('persist');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Exchange Rates', $output);
        $this->assertStringContainsString('EUR: 0.84', $output);
        $this->assertStringContainsString('GBP: 0.74', $output);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }
}
