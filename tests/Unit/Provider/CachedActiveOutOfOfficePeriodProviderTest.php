<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Setono\SyliusOutOfOfficePlugin\Provider\ActiveOutOfOfficePeriodProviderInterface;
use Setono\SyliusOutOfOfficePlugin\Provider\CachedActiveOutOfOfficePeriodProvider;
use Sylius\Component\Channel\Model\ChannelInterface;

final class CachedActiveOutOfOfficePeriodProviderTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<ActiveOutOfOfficePeriodProviderInterface> */
    private ObjectProphecy $inner;

    protected function setUp(): void
    {
        $this->inner = $this->prophesize(ActiveOutOfOfficePeriodProviderInterface::class);
    }

    private function channel(string $code = 'US_WEB'): ChannelInterface
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getCode()->willReturn($code);

        return $channel->reveal();
    }

    /**
     * @test
     */
    public function it_calls_the_decorated_provider_only_once_per_channel(): void
    {
        $channel = $this->channel();
        $period = $this->prophesize(OutOfOfficePeriodInterface::class)->reveal();

        $this->inner->getActivePeriod($channel)->willReturn($period)->shouldBeCalledOnce();

        $provider = new CachedActiveOutOfOfficePeriodProvider($this->inner->reveal());

        self::assertSame($period, $provider->getActivePeriod($channel));
        self::assertSame($period, $provider->getActivePeriod($channel));
    }

    /**
     * @test
     */
    public function it_caches_the_null_channel_resolution(): void
    {
        $this->inner->getActivePeriod(null)->willReturn(null)->shouldBeCalledOnce();

        $provider = new CachedActiveOutOfOfficePeriodProvider($this->inner->reveal());

        self::assertNull($provider->getActivePeriod());
        self::assertNull($provider->getActivePeriod());
    }

    /**
     * @test
     */
    public function it_caches_separately_per_channel(): void
    {
        $us = $this->channel('US_WEB');
        $eu = $this->channel('EU_WEB');
        $usPeriod = $this->prophesize(OutOfOfficePeriodInterface::class)->reveal();

        $this->inner->getActivePeriod($us)->willReturn($usPeriod)->shouldBeCalledOnce();
        $this->inner->getActivePeriod($eu)->willReturn(null)->shouldBeCalledOnce();

        $provider = new CachedActiveOutOfOfficePeriodProvider($this->inner->reveal());

        self::assertSame($usPeriod, $provider->getActivePeriod($us));
        self::assertNull($provider->getActivePeriod($eu));
    }
}
