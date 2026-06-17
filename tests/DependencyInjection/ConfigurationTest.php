<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Setono\SyliusOutOfOfficePlugin\DependencyInjection\Configuration;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    /**
     * @test
     */
    public function it_is_valid_without_any_configuration(): void
    {
        $this->assertConfigurationIsValid([[]]);
    }

    /**
     * @test
     */
    public function it_does_not_allow_a_blank_cookie_prefix(): void
    {
        $this->assertPartialConfigurationIsInvalid(
            [['dismissal' => ['cookie_prefix' => '']]],
            'dismissal.cookie_prefix',
        );
    }
}
