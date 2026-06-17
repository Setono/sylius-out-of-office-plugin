<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
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
            ->setLabelAttribute('icon', 'tabler:beach')
        ;
    }
}
