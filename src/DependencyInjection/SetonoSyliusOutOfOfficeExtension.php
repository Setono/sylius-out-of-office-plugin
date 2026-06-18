<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusOutOfOfficeExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /** @var array{driver: string, resources: array<string, mixed>} $config */
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('setono_sylius_out_of_office', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('sylius_grid')) {
            $container->prependExtensionConfig('sylius_grid', [
                'grids' => [
                    'setono_sylius_out_of_office_period' => [
                        'driver' => [
                            'name' => 'doctrine/orm',
                            'options' => [
                                'class' => '%setono_sylius_out_of_office.model.out_of_office_period.class%',
                            ],
                        ],
                        'sorting' => ['startsAt' => 'desc'],
                        'fields' => [
                            'name' => [
                                'type' => 'string',
                                'label' => 'setono_sylius_out_of_office.ui.name',
                                'sortable' => null,
                            ],
                            'channels' => [
                                'type' => 'twig',
                                'label' => 'setono_sylius_out_of_office.ui.channels',
                                'options' => [
                                    'template' => '@SetonoSyliusOutOfOfficePlugin/admin/grid/field/channels.html.twig',
                                ],
                            ],
                            'active' => [
                                'type' => 'twig',
                                'label' => 'setono_sylius_out_of_office.ui.active_now',
                                'path' => '.',
                                'options' => [
                                    'template' => '@SetonoSyliusOutOfOfficePlugin/admin/grid/field/active.html.twig',
                                ],
                            ],
                            'startsAt' => [
                                'type' => 'datetime',
                                'label' => 'setono_sylius_out_of_office.ui.starts_at',
                                'sortable' => null,
                            ],
                            'endsAt' => [
                                'type' => 'datetime',
                                'label' => 'setono_sylius_out_of_office.ui.ends_at',
                                'sortable' => null,
                            ],
                            'enabled' => [
                                'type' => 'twig',
                                'label' => 'setono_sylius_out_of_office.ui.enabled',
                                'sortable' => null,
                                'options' => [
                                    'template' => '@SyliusUi/Grid/Field/enabled.html.twig',
                                ],
                            ],
                        ],
                        'filters' => [
                            'name' => [
                                'type' => 'string',
                                'label' => 'setono_sylius_out_of_office.ui.name',
                                'options' => ['fields' => ['name']],
                            ],
                            'enabled' => [
                                'type' => 'boolean',
                                'label' => 'setono_sylius_out_of_office.ui.enabled',
                            ],
                            'channel' => [
                                'type' => 'entity',
                                'label' => 'setono_sylius_out_of_office.ui.channel',
                                'options' => ['fields' => ['channels']],
                                'form_options' => [
                                    'class' => '%sylius.model.channel.class%',
                                ],
                            ],
                        ],
                        'actions' => [
                            'main' => ['create' => ['type' => 'create']],
                            'item' => [
                                'update' => ['type' => 'update'],
                                'delete' => ['type' => 'delete'],
                            ],
                            'bulk' => ['delete' => ['type' => 'delete']],
                        ],
                    ],
                ],
            ]);
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
