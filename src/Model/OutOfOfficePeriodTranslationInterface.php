<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Model;

use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TranslationInterface;

interface OutOfOfficePeriodTranslationInterface extends ResourceInterface, TranslationInterface
{
    public function getTopBarMessage(): ?string;

    public function setTopBarMessage(?string $topBarMessage): void;

    public function getProductMessage(): ?string;

    public function setProductMessage(?string $productMessage): void;

    public function getCheckoutMessage(): ?string;

    public function setCheckoutMessage(?string $checkoutMessage): void;
}
