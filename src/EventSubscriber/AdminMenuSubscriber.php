<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\EventSubscriber;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AdminMenuSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.menu.admin.main' => 'addAdminMenuItem',
        ];
    }

    public function addAdminMenuItem(MenuBuilderEvent $event): void
    {
        $configuration = $event->getMenu()->getChild('configuration');
        if (null === $configuration) {
            return;
        }

        $configuration
            ->addChild('setono_sylius_out_of_office_period', [
                'route' => 'setono_sylius_out_of_office_admin_out_of_office_period_index',
            ])
            ->setLabel('setono_sylius_out_of_office.ui.out_of_office')
            ->setLabelAttribute('icon', 'plane')
        ;
    }
}
