<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Form\Type;

use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficeCheckoutBehavior;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class OutOfOfficePeriodType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'setono_sylius_out_of_office.form.out_of_office_period.name',
                'help' => 'setono_sylius_out_of_office.form.out_of_office_period.name_help',
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'label' => 'setono_sylius_out_of_office.form.out_of_office_period.enabled',
            ])
            ->add('startsAt', DateTimeType::class, [
                'required' => false,
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
                'label' => 'setono_sylius_out_of_office.form.out_of_office_period.starts_at',
                'help' => 'setono_sylius_out_of_office.form.out_of_office_period.starts_at_help',
            ])
            ->add('endsAt', DateTimeType::class, [
                'required' => false,
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
                'label' => 'setono_sylius_out_of_office.form.out_of_office_period.ends_at',
                'help' => 'setono_sylius_out_of_office.form.out_of_office_period.ends_at_help',
            ])
            ->add('channels', ChannelChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'label' => 'setono_sylius_out_of_office.form.out_of_office_period.channels',
                'help' => 'setono_sylius_out_of_office.form.out_of_office_period.channels_help',
            ])
            ->add('showOnTopBar', CheckboxType::class, [
                'required' => false,
                'label' => 'setono_sylius_out_of_office.form.out_of_office_period.show_on_top_bar',
            ])
            ->add('showOnProductPage', CheckboxType::class, [
                'required' => false,
                'label' => 'setono_sylius_out_of_office.form.out_of_office_period.show_on_product_page',
            ])
            ->add('showAtCheckout', CheckboxType::class, [
                'required' => false,
                'label' => 'setono_sylius_out_of_office.form.out_of_office_period.show_at_checkout',
            ])
            ->add('checkoutBehavior', ChoiceType::class, [
                'label' => 'setono_sylius_out_of_office.form.out_of_office_period.checkout_behavior',
                'help' => 'setono_sylius_out_of_office.form.out_of_office_period.checkout_behavior_help',
                'choices' => [
                    'setono_sylius_out_of_office.form.out_of_office_period.checkout_behavior_choices.allow' => OutOfOfficeCheckoutBehavior::Allow->value,
                    'setono_sylius_out_of_office.form.out_of_office_period.checkout_behavior_choices.disable' => OutOfOfficeCheckoutBehavior::Disable->value,
                ],
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => OutOfOfficePeriodTranslationType::class,
                'label' => 'setono_sylius_out_of_office.form.out_of_office_period.translations',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_out_of_office_period';
    }
}
