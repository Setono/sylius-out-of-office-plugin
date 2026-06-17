<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\Unit\EventListener;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusOutOfOfficePlugin\EventListener\CheckoutGuardListener;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Setono\SyliusOutOfOfficePlugin\Provider\ActiveOutOfOfficePeriodProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

final class CheckoutGuardListenerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<ActiveOutOfOfficePeriodProviderInterface> */
    private ObjectProphecy $provider;

    /** @var ObjectProphecy<RouterInterface> */
    private ObjectProphecy $router;

    protected function setUp(): void
    {
        $this->provider = $this->prophesize(ActiveOutOfOfficePeriodProviderInterface::class);
        $this->router = $this->prophesize(RouterInterface::class);
    }

    private function createListener(): CheckoutGuardListener
    {
        return new CheckoutGuardListener($this->provider->reveal(), $this->router->reveal());
    }

    /**
     * @test
     */
    public function it_does_nothing_when_the_subject_is_not_an_order(): void
    {
        $event = $this->prophesize(GenericEvent::class);
        $event->getSubject()->willReturn(new \stdClass());
        $event->stop(Argument::cetera())->shouldNotBeCalled();

        $this->createListener()->check($event->reveal());
    }

    /**
     * @test
     */
    public function it_does_nothing_when_no_period_is_active(): void
    {
        $channel = $this->prophesize(ChannelInterface::class)->reveal();
        $order = $this->prophesize(OrderInterface::class);
        $order->getChannel()->willReturn($channel);

        $this->provider->getActivePeriod($channel)->willReturn(null);

        $event = $this->prophesize(GenericEvent::class);
        $event->getSubject()->willReturn($order->reveal());
        $event->stop(Argument::cetera())->shouldNotBeCalled();

        $this->createListener()->check($event->reveal());
    }

    /**
     * @test
     */
    public function it_does_nothing_when_the_active_period_allows_checkout(): void
    {
        $channel = $this->prophesize(ChannelInterface::class)->reveal();
        $order = $this->prophesize(OrderInterface::class);
        $order->getChannel()->willReturn($channel);

        $period = $this->prophesize(OutOfOfficePeriodInterface::class);
        $period->isCheckoutDisabled()->willReturn(false);
        $this->provider->getActivePeriod($channel)->willReturn($period->reveal());

        $event = $this->prophesize(GenericEvent::class);
        $event->getSubject()->willReturn($order->reveal());
        $event->stop(Argument::cetera())->shouldNotBeCalled();

        $this->createListener()->check($event->reveal());
    }

    /**
     * @test
     */
    public function it_blocks_checkout_when_the_active_period_disables_it(): void
    {
        $channel = $this->prophesize(ChannelInterface::class)->reveal();
        $order = $this->prophesize(OrderInterface::class);
        $order->getChannel()->willReturn($channel);

        $period = $this->prophesize(OutOfOfficePeriodInterface::class);
        $period->isCheckoutDisabled()->willReturn(true);
        $this->provider->getActivePeriod($channel)->willReturn($period->reveal());

        $this->router->generate('sylius_shop_checkout_complete')->willReturn('/checkout/complete');

        $event = $this->prophesize(GenericEvent::class);
        $event->getSubject()->willReturn($order->reveal());
        $event->stop('setono_sylius_out_of_office.checkout.disabled', GenericEvent::TYPE_ERROR)->shouldBeCalled();
        $event->setResponse(Argument::type(RedirectResponse::class))->shouldBeCalled();

        $this->createListener()->check($event->reveal());
    }
}
