<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\Unit\EventSubscriber;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\TestCase;
use Setono\SyliusOutOfOfficePlugin\EventSubscriber\AdminMenuSubscriber;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuSubscriberTest extends TestCase
{
    /**
     * @test
     */
    public function it_subscribes_to_the_admin_main_menu_event(): void
    {
        self::assertArrayHasKey('sylius.menu.admin.main', AdminMenuSubscriber::getSubscribedEvents());
    }

    /**
     * @test
     */
    public function it_adds_an_item_under_the_configuration_section(): void
    {
        $factory = new MenuFactory();
        $menu = $factory->createItem('root');
        $menu->addChild('configuration');

        (new AdminMenuSubscriber())->addAdminMenuItem(new MenuBuilderEvent($factory, $menu));

        $configuration = $menu->getChild('configuration');
        self::assertNotNull($configuration);

        $item = $configuration->getChild('setono_sylius_out_of_office_period');
        self::assertNotNull($item);
        self::assertSame('setono_sylius_out_of_office.ui.out_of_office', $item->getLabel());
        self::assertSame('tabler:beach', $item->getLabelAttribute('icon'));
    }

    /**
     * @test
     */
    public function it_does_nothing_when_there_is_no_configuration_section(): void
    {
        $factory = new MenuFactory();
        $menu = $factory->createItem('root');

        (new AdminMenuSubscriber())->addAdminMenuItem(new MenuBuilderEvent($factory, $menu));

        self::assertCount(0, $menu->getChildren());
    }
}
