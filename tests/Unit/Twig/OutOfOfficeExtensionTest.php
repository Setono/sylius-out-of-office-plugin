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
    public function it_exposes_three_prefixed_twig_functions(): void
    {
        $functions = (new OutOfOfficeExtension())->getFunctions();

        self::assertCount(3, $functions);

        foreach ($functions as $function) {
            self::assertStringStartsWith('setono_sylius_out_of_office_', $function->getName());
        }
    }
}
