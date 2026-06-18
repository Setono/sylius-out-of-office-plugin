<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\EventListener;

use Setono\SyliusOutOfOfficePlugin\Provider\ActiveOutOfOfficePeriodProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Authoritative, server-side guard: when the active out of office period for the order's channel
 * has the "disable" checkout behavior, order completion is blocked.
 */
final class CheckoutGuardListener
{
    public function __construct(
        private readonly ActiveOutOfOfficePeriodProviderInterface $activePeriodProvider,
        private readonly RouterInterface $router,
    ) {
    }

    public function check(GenericEvent $event): void
    {
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface) {
            return;
        }

        $period = $this->activePeriodProvider->getActivePeriod($order->getChannel());
        if (null === $period || !$period->isCheckoutDisabled()) {
            return;
        }

        $event->stop('setono_sylius_out_of_office.checkout.disabled', GenericEvent::TYPE_ERROR);
        $event->setResponse(new RedirectResponse($this->router->generate('sylius_shop_checkout_complete')));
    }
}
