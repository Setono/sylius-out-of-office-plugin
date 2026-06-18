<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Model;

use Sylius\Resource\Model\AbstractTranslation;

class OutOfOfficePeriodTranslation extends AbstractTranslation implements OutOfOfficePeriodTranslationInterface
{
    protected ?int $id = null;

    protected ?string $topBarMessage = null;

    protected ?string $productMessage = null;

    protected ?string $checkoutMessage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTopBarMessage(): ?string
    {
        return $this->topBarMessage;
    }

    public function setTopBarMessage(?string $topBarMessage): void
    {
        $this->topBarMessage = $topBarMessage;
    }

    public function getProductMessage(): ?string
    {
        return $this->productMessage;
    }

    public function setProductMessage(?string $productMessage): void
    {
        $this->productMessage = $productMessage;
    }

    public function getCheckoutMessage(): ?string
    {
        return $this->checkoutMessage;
    }

    public function setCheckoutMessage(?string $checkoutMessage): void
    {
        $this->checkoutMessage = $checkoutMessage;
    }
}
