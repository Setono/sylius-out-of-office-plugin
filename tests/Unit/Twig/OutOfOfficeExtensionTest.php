<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\Unit\Twig;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusOutOfOfficePlugin\Dismissal\DismissalCookieKeyGeneratorInterface;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Setono\SyliusOutOfOfficePlugin\Provider\ActiveOutOfOfficePeriodProviderInterface;
use Setono\SyliusOutOfOfficePlugin\Twig\OutOfOfficeExtension;
use Symfony\Component\Clock\MockClock;

final class OutOfOfficeExtensionTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<ActiveOutOfOfficePeriodProviderInterface> */
    private ObjectProphecy $provider;

    /** @var ObjectProphecy<DismissalCookieKeyGeneratorInterface> */
    private ObjectProphecy $cookieKeyGenerator;

    private MockClock $clock;

    protected function setUp(): void
    {
        $this->provider = $this->prophesize(ActiveOutOfOfficePeriodProviderInterface::class);
        $this->cookieKeyGenerator = $this->prophesize(DismissalCookieKeyGeneratorInterface::class);
        $this->clock = new MockClock(new \DateTimeImmutable('2026-06-17 12:00:00'));
    }

    private function createExtension(): OutOfOfficeExtension
    {
        return new OutOfOfficeExtension(
            $this->provider->reveal(),
            $this->cookieKeyGenerator->reveal(),
            $this->clock,
            1234,
        );
    }

    /**
     * @test
     */
    public function it_exposes_only_prefixed_twig_functions(): void
    {
        foreach ($this->createExtension()->getFunctions() as $function) {
            self::assertStringStartsWith('setono_sylius_out_of_office_', $function->getName());
        }
    }

    /**
     * @test
     */
    public function it_returns_the_active_period(): void
    {
        $period = $this->prophesize(OutOfOfficePeriodInterface::class)->reveal();
        $this->provider->getActivePeriod(null)->willReturn($period);

        self::assertSame($period, $this->createExtension()->getActivePeriod());
    }

    /**
     * @test
     */
    public function it_reports_active_when_a_period_is_returned(): void
    {
        $this->provider->getActivePeriod(null)->willReturn($this->prophesize(OutOfOfficePeriodInterface::class)->reveal());

        self::assertTrue($this->createExtension()->isActive());
    }

    /**
     * @test
     */
    public function it_reports_inactive_when_no_period_is_returned(): void
    {
        $this->provider->getActivePeriod(null)->willReturn(null);

        self::assertFalse($this->createExtension()->isActive());
    }

    /**
     * @test
     */
    public function it_checks_whether_a_specific_period_is_active_using_the_clock(): void
    {
        $period = $this->prophesize(OutOfOfficePeriodInterface::class);
        $period->isActiveAt($this->clock->now())->willReturn(true);

        self::assertTrue($this->createExtension()->isPeriodActive($period->reveal()));
    }

    /**
     * @test
     */
    public function it_returns_false_when_checking_a_period_throws(): void
    {
        $period = $this->prophesize(OutOfOfficePeriodInterface::class);
        $period->isActiveAt($this->clock->now())->willThrow(new \RuntimeException('boom'));

        self::assertFalse($this->createExtension()->isPeriodActive($period->reveal()));
    }

    /**
     * @test
     */
    public function it_delegates_the_dismissal_cookie_key(): void
    {
        $period = $this->prophesize(OutOfOfficePeriodInterface::class);
        $this->cookieKeyGenerator->generate($period->reveal())->willReturn('the_key');

        self::assertSame('the_key', $this->createExtension()->getDismissalCookieKey($period->reveal()));
    }

    /**
     * @test
     */
    public function it_returns_the_dismissal_cookie_max_age(): void
    {
        self::assertSame(1234, $this->createExtension()->getDismissalCookieMaxAge());
    }
}
