<?php

namespace App\Form;

use App\Entity\Devis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', TextType::class, [
                'label' => 'Numéro de devis',
                'required' => true,

            ])
            ->add('dateCreation', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de création',
                'required' => true,
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant',
                'required' => true,
            ])
            ->add('adressChantier', TextType::class, [
                'label' => 'Adresse du chantier',
                'required' => true,
            ])
            ->add('fichier', FileType::class, [
                'label' => 'Fichier PDF du devis',
                'mapped' => false,  // Non mappé directement à l'entité
                'required' => true, 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
  
  
}
