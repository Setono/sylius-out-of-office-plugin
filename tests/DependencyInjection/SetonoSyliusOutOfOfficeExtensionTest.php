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
            \Setono\SyliusOutOfOfficePlugin\Provider\ActiveOutOfOfficePeriodProvider::class,
        );
    }
}
