<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Setono\SyliusOutOfOfficePlugin\DependencyInjection\SetonoSyliusOutOfOfficeExtension;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriod;

final class SetonoSyliusOutOfOfficeExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new SetonoSyliusOutOfOfficeExtension(),
        ];
    }

    /**
     * @test
     */
    public function it_sets_the_dismissal_parameters(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('setono_sylius_out_of_office.dismissal.cookie_prefix', 'setono_out_of_office_dismissed');
        $this->assertContainerBuilderHasParameter('setono_sylius_out_of_office.dismissal.cookie_max_age', 2592000);
    }

    /**
     * @test
     */
    public function it_allows_overriding_the_dismissal_parameters(): void
    {
        $this->load([
            'dismissal' => [
                'cookie_prefix' => 'custom_prefix',
                'cookie_max_age' => 60,
            ],
        ]);

        $this->assertContainerBuilderHasParameter('setono_sylius_out_of_office.dismissal.cookie_prefix', 'custom_prefix');
        $this->assertContainerBuilderHasParameter('setono_sylius_out_of_office.dismissal.cookie_max_age', 60);
    }

    /**
     * @test
     */
    public function it_registers_the_out_of_office_period_resource(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('setono_sylius_out_of_office.model.out_of_office_period.class', OutOfOfficePeriod::class);
    }

    /**
     * @test
     */
    public function it_registers_the_provider(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService(
            'setono_sylius_out_of_office.provider.active_out_of_office_period',
        );
    }
}
