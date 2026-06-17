<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\DependencyInjection;

use Setono\SyliusOutOfOfficePlugin\DependencyInjection\SetonoSyliusOutOfOfficeExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * See examples of tests and configuration options here: https://github.com/SymfonyTest/SymfonyDependencyInjectionTest
 */
final class SetonoSyliusOutOfOfficeExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new SetonoSyliusOutOfOfficeExtension(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getMinimalConfiguration(): array
    {
        return [
            'option' => 'option_value',
        ];
    }

    /**
     * @test
     */
    public function after_loading_the_correct_parameter_has_been_set(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('setono_sylius_out_of_office.option', 'option_value');
    }
}
