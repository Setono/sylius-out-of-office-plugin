<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())
    ->addPathToExclude(__DIR__ . '/tests')
    ->addPathToExclude(__DIR__ . '/src/Migrations')
    ->addPathToExclude(__DIR__ . '/src/Fixture')
    // sylius/core-bundle is the Sylius umbrella this plugin integrates with; its classes are only
    // referenced directly from the (excluded) fixtures, so silence the unused-dependency report.
    ->ignoreErrorsOnPackage('sylius/core-bundle', [ErrorType::UNUSED_DEPENDENCY])
    // The Sylius components/bundles are pulled in via sylius/core-bundle. Depending on whether the
    // monorepo (sylius/sylius) or the read-only splits are installed, those classes are attributed to
    // different package names, so ignore the shadow report for all of them.
    ->ignoreErrorsOnPackages([
        'sylius/sylius',
        'sylius/channel',
        'sylius/core',
        'sylius/channel-bundle',
        'sylius/ui-bundle',
    ], [ErrorType::SHADOW_DEPENDENCY])
    ->disableReportingUnmatchedIgnores()
;
