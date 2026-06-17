<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Model;

use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TimestampableInterface;
use Sylius\Resource\Model\ToggleableInterface;
use Sylius\Resource\Model\TranslatableInterface;

interface OutOfOfficePeriodInterface extends ResourceInterface, ChannelsAwareInterface, TranslatableInterface, ToggleableInterface, TimestampableInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getStartsAt(): ?\DateTimeInterface;

    public function setStartsAt(?\DateTimeImmutable $startsAt): void;

    public function getEndsAt(): ?\DateTimeInterface;

    public function setEndsAt(?\DateTimeImmutable $endsAt): void;

    public function isShowOnTopBar(): bool;

    public function setShowOnTopBar(bool $showOnTopBar): void;

    public function isShowOnProductPage(): bool;

    public function setShowOnProductPage(bool $showOnProductPage): void;

    public function isShowAtCheckout(): bool;

    public function setShowAtCheckout(bool $showAtCheckout): void;

    public function isDismissible(): bool;

    public function setDismissible(bool $dismissible): void;

    public function getCheckoutBehavior(): string;

    public function setCheckoutBehavior(string $checkoutBehavior): void;

    /**
     * Whether order completion should be blocked while this period is active.
     */
    public function isCheckoutDisabled(): bool;

    /**
     * Whether this period should be considered active at the given instant.
     * Combines the enabled flag with the optional start/end bounds (both inclusive).
     */
    public function isActiveAt(\DateTimeInterface $now): bool;

    public function getTopBarMessage(): ?string;

    public function setTopBarMessage(?string $topBarMessage): void;

    /**
     * Falls back to the top bar message when no dedicated product message is set.
     */
    public function getProductMessage(): ?string;

    public function setProductMessage(?string $productMessage): void;

    /**
     * Falls back to the top bar message when no dedicated checkout message is set.
     */
    public function getCheckoutMessage(): ?string;

    public function setCheckoutMessage(?string $checkoutMessage): void;
}
