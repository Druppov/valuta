<?php

namespace App\Entity;

use App\Repository\CurrencyRateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRateRepository::class)]
class CurrencyRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(length: 3)]
    private ?int $id = null;

    #[ORM\Column(length: 3)]
    private ?string $base = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datetime = null;

//    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
//    #[ORM\JoinColumn(nullable: false)]
//    private ?CurrencyCode $CurrencyCode = null;

    #[ORM\Column(length: 3)]
    private ?string $currencyCodeId = null;

    #[ORM\Column]
    private ?float $rate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $Id): static
    {
        $this->Id = $Id;

        return $this;
    }

    public function getBase(): ?string
    {
        return $this->base;
    }

    public function setBase(string $base): static
    {
        $this->base = $base;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): static
    {
        $this->datetime = $datetime;

        return $this;
    }

//    public function getCurrencyCode(): ?CurrencyCode
//    {
//        return $this->CurrencyCode;
//    }
//
//    public function setCurrencyCode(CurrencyCode $CurrencyCode): static
//    {
//        $this->CurrencyCode = $CurrencyCode;
//
//        return $this;
//    }

    public function getCurrencyCodeId(): ?string
    {
        return $this->currencyCodeId;
    }

    public function setCurrencyCodeId(string $currencyCodeId): static
    {
        $this->currencyCodeId = $currencyCodeId;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }
}
