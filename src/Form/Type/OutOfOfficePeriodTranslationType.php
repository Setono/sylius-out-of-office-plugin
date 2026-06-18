<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

final class OutOfOfficePeriodTranslationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('topBarMessage', TextareaType::class, [
                'required' => false,
                'label' => 'setono_sylius_out_of_office.form.out_of_office_period.top_bar_message',
                'help' => 'setono_sylius_out_of_office.form.out_of_office_period.top_bar_message_help',
            ])
            ->add('productMessage', TextareaType::class, [
                'required' => false,
                'label' => 'setono_sylius_out_of_office.form.out_of_office_period.product_message',
                'help' => 'setono_sylius_out_of_office.form.out_of_office_period.product_message_help',
            ])
            ->add('checkoutMessage', TextareaType::class, [
                'required' => false,
                'label' => 'setono_sylius_out_of_office.form.out_of_office_period.checkout_message',
                'help' => 'setono_sylius_out_of_office.form.out_of_office_period.checkout_message_help',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_out_of_office_period_translation';
    }
}
