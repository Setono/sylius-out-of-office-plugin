<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficeCheckoutBehavior;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriod;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

final class OutOfOfficePeriodTest extends TestCase
{
    use ProphecyTrait;

    private function createPeriod(): OutOfOfficePeriodInterface
    {
        $period = new OutOfOfficePeriod();
        $period->setCurrentLocale('en');
        $period->setFallbackLocale('en');

        return $period;
    }

    /**
     * @test
     */
    public function it_is_active_when_enabled_and_has_no_bounds(): void
    {
        $period = $this->createPeriod();

        self::assertTrue($period->isActiveAt(new \DateTimeImmutable('2026-06-17 12:00:00')));
    }

    /**
     * @test
     */
    public function it_is_not_active_when_disabled(): void
    {
        $period = $this->createPeriod();
        $period->setEnabled(false);

        self::assertFalse($period->isActiveAt(new \DateTimeImmutable('2026-06-17 12:00:00')));
    }

    /**
     * @test
     */
    public function it_is_not_active_before_starts_at(): void
    {
        $period = $this->createPeriod();
        $period->setStartsAt(new \DateTimeImmutable('2026-06-20 00:00:00'));

        self::assertFalse($period->isActiveAt(new \DateTimeImmutable('2026-06-19 23:59:59')));
    }

    /**
     * @test
     */
    public function it_is_active_at_and_after_starts_at_when_no_upper_bound(): void
    {
        $period = $this->createPeriod();
        $period->setStartsAt(new \DateTimeImmutable('2026-06-20 00:00:00'));

        self::assertTrue($period->isActiveAt(new \DateTimeImmutable('2026-06-20 00:00:00')));
        self::assertTrue($period->isActiveAt(new \DateTimeImmutable('2026-07-01 00:00:00')));
    }

    /**
     * @test
     */
    public function it_is_not_active_after_ends_at(): void
    {
        $period = $this->createPeriod();
        $period->setEndsAt(new \DateTimeImmutable('2026-07-05 23:59:00'));

        self::assertFalse($period->isActiveAt(new \DateTimeImmutable('2026-07-06 00:00:00')));
    }

    /**
     * @test
     */
    public function it_is_active_at_and_before_ends_at_when_no_lower_bound(): void
    {
        $period = $this->createPeriod();
        $period->setEndsAt(new \DateTimeImmutable('2026-07-05 23:59:00'));

        self::assertTrue($period->isActiveAt(new \DateTimeImmutable('2026-07-05 23:59:00')));
        self::assertTrue($period->isActiveAt(new \DateTimeImmutable('2026-01-01 00:00:00')));
    }

    /**
     * @test
     */
    public function it_is_active_within_both_bounds_inclusively(): void
    {
        $period = $this->createPeriod();
        $period->setStartsAt(new \DateTimeImmutable('2026-06-20 00:00:00'));
        $period->setEndsAt(new \DateTimeImmutable('2026-07-05 23:59:00'));

        self::assertTrue($period->isActiveAt(new \DateTimeImmutable('2026-06-20 00:00:00')));
        self::assertTrue($period->isActiveAt(new \DateTimeImmutable('2026-06-28 10:00:00')));
        self::assertTrue($period->isActiveAt(new \DateTimeImmutable('2026-07-05 23:59:00')));
    }

    /**
     * @test
     */
    public function it_falls_back_to_the_top_bar_message_for_product_and_checkout(): void
    {
        $period = $this->createPeriod();
        $period->setTopBarMessage('We are away');

        self::assertSame('We are away', $period->getTopBarMessage());
        self::assertSame('We are away', $period->getProductMessage());
        self::assertSame('We are away', $period->getCheckoutMessage());
    }

    /**
     * @test
     */
    public function it_uses_dedicated_messages_when_set(): void
    {
        $period = $this->createPeriod();
        $period->setTopBarMessage('top');
        $period->setProductMessage('product');
        $period->setCheckoutMessage('checkout');

        self::assertSame('top', $period->getTopBarMessage());
        self::assertSame('product', $period->getProductMessage());
        self::assertSame('checkout', $period->getCheckoutMessage());
    }

    /**
     * @test
     */
    public function it_reports_whether_checkout_is_disabled(): void
    {
        $period = $this->createPeriod();
        self::assertFalse($period->isCheckoutDisabled());

        $period->setCheckoutBehavior(OutOfOfficeCheckoutBehavior::Disable->value);
        self::assertTrue($period->isCheckoutDisabled());
    }

    /**
     * @test
     */
    public function it_manages_channels(): void
    {
        $period = $this->createPeriod();
        $channel = $this->prophesize(ChannelInterface::class)->reveal();

        self::assertCount(0, $period->getChannels());

        $period->addChannel($channel);
        $period->addChannel($channel);

        self::assertTrue($period->hasChannel($channel));
        self::assertCount(1, $period->getChannels());

        $period->removeChannel($channel);

        self::assertFalse($period->hasChannel($channel));
        self::assertCount(0, $period->getChannels());
    }
}
