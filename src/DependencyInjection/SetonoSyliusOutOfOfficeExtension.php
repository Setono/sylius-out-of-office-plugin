<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Yaml\Yaml;

final class SetonoSyliusOutOfOfficeExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /** @var array{driver: string, dismissal: array{cookie_prefix: string, cookie_max_age: int}, resources: array<string, mixed>} $config */
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('setono_sylius_out_of_office', $config['driver'], $config['resources'], $container);

        $container->setParameter('setono_sylius_out_of_office.dismissal.cookie_prefix', $config['dismissal']['cookie_prefix']);
        $container->setParameter('setono_sylius_out_of_office.dismissal.cookie_max_age', $config['dismissal']['cookie_max_age']);

        $loader->load('services.xml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('sylius_grid')) {
            /** @var array{sylius_grid?: array<string, mixed>} $gridConfig */
            $gridConfig = Yaml::parseFile(__DIR__ . '/../Resources/config/grids/setono_sylius_out_of_office_period.yaml');
            $container->prependExtensionConfig('sylius_grid', $gridConfig['sylius_grid'] ?? []);
        }

        if ($container->hasExtension('sylius_ui')) {
            $container->prependExtensionConfig('sylius_ui', [
                'events' => [
                    // Top bar — full-width announcement bar just after the header, above page content
                    'sylius.shop.layout.before_content' => [
                        'blocks' => [
                            'setono_sylius_out_of_office_top_bar' => [
                                'template' => '@SetonoSyliusOutOfOfficePlugin/shop/top_bar.html.twig',
                                'priority' => 100,
                            ],
                        ],
                    ],
                    // Product show page
                    'sylius.shop.product.show.content' => [
                        'blocks' => [
                            'setono_sylius_out_of_office_product_notice' => [
                                'template' => '@SetonoSyliusOutOfOfficePlugin/shop/product_notice.html.twig',
                                'priority' => 100,
                            ],
                        ],
                    ],
                    // Checkout — complete/summary step
                    'sylius.shop.checkout.complete.summary' => [
                        'blocks' => [
                            'setono_sylius_out_of_office_checkout_notice' => [
                                'template' => '@SetonoSyliusOutOfOfficePlugin/shop/checkout_notice.html.twig',
                                'priority' => 100,
                            ],
                        ],
                    ],
                    // Checkout — just before the navigation/place-order buttons (Phase 6: disable)
                    'sylius.shop.checkout.complete.before_navigation' => [
                        'blocks' => [
                            'setono_sylius_out_of_office_checkout_blocked' => [
                                'template' => '@SetonoSyliusOutOfOfficePlugin/shop/checkout_blocked.html.twig',
                                'priority' => 100,
                            ],
                        ],
                    ],
                ],
            ]);
        }
    }
}
