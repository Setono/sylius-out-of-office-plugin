<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\Unit\Twig;

use PHPUnit\Framework\TestCase;
use Setono\SyliusOutOfOfficePlugin\Twig\OutOfOfficeExtension;

final class OutOfOfficeExtensionTest extends TestCase
{
    /**
     * @test
     */
    public function it_exposes_the_prefixed_twig_functions(): void
    {
        $names = [];
        foreach ((new OutOfOfficeExtension())->getFunctions() as $function) {
            $names[] = $function->getName();
        }

        self::assertSame([
            'setono_sylius_out_of_office_active_period',
            'setono_sylius_out_of_office_is_active',
            'setono_sylius_out_of_office_period_is_active',
        ], $names);
    }
}
