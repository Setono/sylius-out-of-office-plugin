<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Model\TimestampableTrait;
use Sylius\Resource\Model\ToggleableTrait;
use Sylius\Resource\Model\TranslatableTrait;
use Sylius\Resource\Model\TranslationInterface;

class OutOfOfficePeriod implements OutOfOfficePeriodInterface
{
    use ToggleableTrait;
    use TimestampableTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    protected ?int $id = null;

    protected ?string $name = null;

    protected ?\DateTimeImmutable $startsAt = null;

    protected ?\DateTimeImmutable $endsAt = null;

    protected bool $showOnTopBar = true;

    protected bool $showOnProductPage = true;

    protected bool $showAtCheckout = true;

    protected string $checkoutBehavior;

    /** @var Collection<array-key, ChannelInterface> */
    protected Collection $channels;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->channels = new ArrayCollection();
        $this->checkoutBehavior = OutOfOfficeCheckoutBehavior::Allow->value;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getStartsAt(): ?\DateTimeInterface
    {
        return $this->startsAt;
    }

    public function setStartsAt(?\DateTimeImmutable $startsAt): void
    {
        $this->startsAt = $startsAt;
    }

    public function getEndsAt(): ?\DateTimeInterface
    {
        return $this->endsAt;
    }

    public function setEndsAt(?\DateTimeImmutable $endsAt): void
    {
        $this->endsAt = $endsAt;
    }

    public function isShowOnTopBar(): bool
    {
        return $this->showOnTopBar;
    }

    public function setShowOnTopBar(bool $showOnTopBar): void
    {
        $this->showOnTopBar = $showOnTopBar;
    }

    public function isShowOnProductPage(): bool
    {
        return $this->showOnProductPage;
    }

    public function setShowOnProductPage(bool $showOnProductPage): void
    {
        $this->showOnProductPage = $showOnProductPage;
    }

    public function isShowAtCheckout(): bool
    {
        return $this->showAtCheckout;
    }

    public function setShowAtCheckout(bool $showAtCheckout): void
    {
        $this->showAtCheckout = $showAtCheckout;
    }

    public function getCheckoutBehavior(): string
    {
        return $this->checkoutBehavior;
    }

    public function setCheckoutBehavior(string $checkoutBehavior): void
    {
        $this->checkoutBehavior = $checkoutBehavior;
    }

    public function isCheckoutDisabled(): bool
    {
        return OutOfOfficeCheckoutBehavior::Disable->value === $this->checkoutBehavior;
    }

    public function isActiveAt(\DateTimeInterface $now): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        if (null !== $this->startsAt && $now < $this->startsAt) {
            return false;
        }

        if (null !== $this->endsAt && $now > $this->endsAt) {
            return false;
        }

        return true;
    }

    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function hasChannel(ChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    public function addChannel(ChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    public function removeChannel(ChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    public function getTopBarMessage(): ?string
    {
        return $this->getTranslation()->getTopBarMessage();
    }

    public function setTopBarMessage(?string $topBarMessage): void
    {
        $this->getTranslation()->setTopBarMessage($topBarMessage);
    }

    public function getProductMessage(): ?string
    {
        $translation = $this->getTranslation();

        return $translation->getProductMessage() ?? $translation->getTopBarMessage();
    }

    public function setProductMessage(?string $productMessage): void
    {
        $this->getTranslation()->setProductMessage($productMessage);
    }

    public function getCheckoutMessage(): ?string
    {
        $translation = $this->getTranslation();

        return $translation->getCheckoutMessage() ?? $translation->getTopBarMessage();
    }

    public function setCheckoutMessage(?string $checkoutMessage): void
    {
        $this->getTranslation()->setCheckoutMessage($checkoutMessage);
    }

    /**
     * @return OutOfOfficePeriodTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var OutOfOfficePeriodTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): OutOfOfficePeriodTranslationInterface
    {
        return new OutOfOfficePeriodTranslation();
    }
}
