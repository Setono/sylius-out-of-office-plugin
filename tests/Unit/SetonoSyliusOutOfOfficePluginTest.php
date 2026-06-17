<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Setono\SyliusOutOfOfficePlugin\SetonoSyliusOutOfOfficePlugin;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

final class SetonoSyliusOutOfOfficePluginTest extends TestCase
{
    /**
     * @test
     */
    public function it_supports_the_doctrine_orm_driver(): void
    {
        $bundle = new SetonoSyliusOutOfOfficePlugin();

        self::assertContains(SyliusResourceBundle::DRIVER_DOCTRINE_ORM, $bundle->getSupportedDrivers());
    }
}
