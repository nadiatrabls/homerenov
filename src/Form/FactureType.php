<?php

namespace App\Form;

use App\Entity\Facture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', TextType::class, [
                'label' => 'Numéro de facture',
                'required' => true,
            ])
            ->add('dateEmission', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\'émission',
                'required' => true,
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant',
                'required' => true,  // Le montant est obligatoire
            ])
            ->add('refChantier', TextType::class, [
                'label' => 'Référence du chantier',
                'required' => true,
            ])
            ->add('statusPaiement', TextType::class, [
                'label' => 'Statut de paiement',
                'required' => true,
            ])
            ->add('fichier', FileType::class, [
                'label' => 'Fichier PDF de la facture',
                'mapped' => false,
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
