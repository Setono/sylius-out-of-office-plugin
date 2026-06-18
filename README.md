# Setono Sylius Out Of Office Plugin

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Mutation testing][ico-infection]][link-infection]

Announce an out-of-office / away period from the Sylius admin — with a start/end date, per-channel
scoping and per-placement messaging — and surface it on the storefront **top bar**, **product pages**
and **checkout**. Think of it as an email auto-responder for your store: set a date range, set a
message, and customers are informed that orders placed now will ship when you are back.

## Features

- Multiple, channel-aware, **scheduled** periods — stage next summer's holiday in advance, or run
  different messaging per channel.
- A master `enabled` switch independent of the dates, so you can flip a period on/off instantly.
- Per-placement messages (top bar / product page / checkout) with sensible fallbacks, all translatable.
- Optional **checkout blocking** for periods where you genuinely cannot ship (off by default).
- Storefront integration through `sylius_ui` template events, so it works across all Sylius 1.x minors
  with **zero configuration**.

## Installation

This plugin targets **Sylius 1.x**, **Symfony `^6.4`** and **PHP ≥ 8.1**.

### 1. Require the plugin

```bash
composer require setono/sylius-out-of-office-plugin
```

> The plugin is currently in **alpha**. If your project's `minimum-stability` is `stable`, require it
> explicitly as a pre-release: `composer require setono/sylius-out-of-office-plugin:^1.0@alpha`.

### 2. Register the bundle

```php
# config/bundles.php

return [
    // ...
    Setono\SyliusOutOfOfficePlugin\SetonoSyliusOutOfOfficePlugin::class => ['all' => true],
];
```

### 3. Import the routing

```yaml
# config/routes/setono_sylius_out_of_office.yaml

setono_sylius_out_of_office:
    resource: "@SetonoSyliusOutOfOfficePlugin/Resources/config/routes.yaml"
```

### 4. Update the database schema

Generate a migration for your application and run it:

```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```

The plugin deliberately does not ship its own migration — migrations belong to the application.

That's it — an **Out of office** entry appears in the admin under *Configuration*.

## Storefront integration

The blocks are registered automatically (the plugin prepends the following `sylius_ui` configuration),
so there is nothing to do. The event ids below are the ones that exist in Sylius 1.x; copy this into
your app's configuration only if you want to re-prioritise, move or disable a block:

```yaml
# config/packages/setono_sylius_out_of_office.yaml (optional override)

sylius_ui:
    events:
        # Top bar — full-width announcement bar just after the header, above the page content
        sylius.shop.layout.before_content:
            blocks:
                setono_sylius_out_of_office_top_bar:
                    template: '@SetonoSyliusOutOfOfficePlugin/shop/top_bar.html.twig'
                    priority: 100

        # Product show page
        sylius.shop.product.show.content:
            blocks:
                setono_sylius_out_of_office_product_notice:
                    template: '@SetonoSyliusOutOfOfficePlugin/shop/product_notice.html.twig'
                    priority: 100

        # Checkout — complete/summary step
        sylius.shop.checkout.complete.summary:
            blocks:
                setono_sylius_out_of_office_checkout_notice:
                    template: '@SetonoSyliusOutOfOfficePlugin/shop/checkout_notice.html.twig'
                    priority: 100
```

> **Note:** the notices depend on wall-clock time, so if you put a full-page cache (Varnish / Symfony
> HttpCache) in front of the shop, render these blocks via ESI / edge fragments or keep a short TTL —
> otherwise a bar may stick around (or fail to appear) until the page cache expires.

## Configuration

The resource model classes are overridable through the standard Sylius `resources` configuration, e.g.:

```yaml
# config/packages/setono_sylius_out_of_office.yaml

setono_sylius_out_of_office:
    resources:
        out_of_office_period:
            classes:
                model: App\Entity\OutOfOffice\OutOfOfficePeriod
```

## Customization

- **Templates, grid and form** follow standard Sylius override rules — drop a file in
  `templates/bundles/SetonoSyliusOutOfOfficePlugin/` to restyle the bar/notices. The markup uses stable
  hook classes (`setono-out-of-office-bar`, `setono-out-of-office-notice`) and minimal inline styling.
- **Checkout behavior**: each period has a `checkoutBehavior` of `allow` (default — informational only)
  or `disable` (blocks order completion via a `sylius.order.pre_complete` guard and shows a notice).

## Single source of truth

The active period is resolved by `ActiveOutOfOfficePeriodProviderInterface`, which the templates, the
checkout guard and your own code all go through. In Twig:

```twig
{% if setono_sylius_out_of_office_is_active() %}
    {{ setono_sylius_out_of_office_active_period().topBarMessage }}
{% endif %}
```

## Development

This plugin was scaffolded from the [Setono Sylius plugin skeleton](https://github.com/Setono/SyliusPluginSkeleton)
(`1.14.x`) with `php init` answered as org **Setono** and plugin name **SyliusOutOfOffice**.

```bash
composer install
(cd tests/Application && yarn install && yarn build)
(cd tests/Application && bin/console assets:install)
(cd tests/Application && bin/console doctrine:database:create)
(cd tests/Application && bin/console doctrine:schema:create)
(cd tests/Application && bin/console sylius:fixtures:load -n)
(cd tests/Application && symfony serve)
```

Admin credentials in the test application: `sylius` / `sylius`.

Quality tooling:

```bash
composer analyse       # PHPStan (level max)
composer check-style   # ECS
composer fix-style     # ECS autofix
composer phpunit       # PHPUnit (functional tests run when a database is available)
vendor/bin/infection   # mutation testing
```

## License

This plugin is released under the [MIT License](LICENSE).

[ico-version]: https://poser.pugx.org/setono/sylius-out-of-office-plugin/v/stable
[ico-license]: https://poser.pugx.org/setono/sylius-out-of-office-plugin/license
[ico-github-actions]: https://github.com/Setono/SyliusOutOfOfficePlugin/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/SyliusOutOfOfficePlugin/branch/master/graph/badge.svg
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2FSyliusOutOfOfficePlugin%2Fmaster

[link-packagist]: https://packagist.org/packages/setono/sylius-out-of-office-plugin
[link-github-actions]: https://github.com/Setono/SyliusOutOfOfficePlugin/actions
[link-code-coverage]: https://codecov.io/gh/Setono/SyliusOutOfOfficePlugin
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/Setono/SyliusOutOfOfficePlugin/master
