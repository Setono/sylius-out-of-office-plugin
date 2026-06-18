<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\Unit\Twig;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Setono\SyliusOutOfOfficePlugin\Provider\ActiveOutOfOfficePeriodProviderInterface;
use Setono\SyliusOutOfOfficePlugin\Twig\OutOfOfficeRuntime;
use Symfony\Component\Clock\MockClock;

final class OutOfOfficeRuntimeTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<ActiveOutOfOfficePeriodProviderInterface> */
    private ObjectProphecy $provider;

    private MockClock $clock;

    protected function setUp(): void
    {
        $this->provider = $this->prophesize(ActiveOutOfOfficePeriodProviderInterface::class);
        $this->clock = new MockClock(new \DateTimeImmutable('2026-06-17 12:00:00'));
    }

    private function createRuntime(): OutOfOfficeRuntime
    {
        return new OutOfOfficeRuntime($this->provider->reveal(), $this->clock);
    }

    /**
     * @test
     */
    public function it_returns_the_active_period(): void
    {
        $period = $this->prophesize(OutOfOfficePeriodInterface::class)->reveal();
        $this->provider->getActivePeriod(null)->willReturn($period);

        self::assertSame($period, $this->createRuntime()->getActivePeriod());
    }

    /**
     * @test
     */
    public function it_reports_active_when_a_period_is_returned(): void
    {
        $this->provider->getActivePeriod(null)->willReturn($this->prophesize(OutOfOfficePeriodInterface::class)->reveal());

        self::assertTrue($this->createRuntime()->isActive());
    }

    /**
     * @test
     */
    public function it_reports_inactive_when_no_period_is_returned(): void
    {
        $this->provider->getActivePeriod(null)->willReturn(null);

        self::assertFalse($this->createRuntime()->isActive());
    }

    /**
     * @test
     */
    public function it_checks_whether_a_specific_period_is_active_using_the_clock(): void
    {
        $period = $this->prophesize(OutOfOfficePeriodInterface::class);
        $period->isActiveAt($this->clock->now())->willReturn(true);

        self::assertTrue($this->createRuntime()->isPeriodActive($period->reveal()));
    }

    /**
     * @test
     */
    public function it_returns_false_when_checking_a_period_throws(): void
    {
        $period = $this->prophesize(OutOfOfficePeriodInterface::class);
        $period->isActiveAt($this->clock->now())->willThrow(new \RuntimeException('boom'));

        self::assertFalse($this->createRuntime()->isPeriodActive($period->reveal()));
    }
}
