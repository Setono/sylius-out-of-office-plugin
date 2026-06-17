<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Model;

enum OutOfOfficeCheckoutBehavior: string
{
    /** Orders still complete; checkout only shows the notice (default). */
    case Allow = 'allow';

    /** Order completion is blocked while the period is active. */
    case Disable = 'disable';
}
