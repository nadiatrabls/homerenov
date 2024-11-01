<?php

namespace App\Form;

use App\Entity\DemandeFacture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureDemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('referenceChantier', TextType::class, [
                'label' => 'Référence du chantier',
                'attr' => ['placeholder' => 'Référence du chantier'],
            ])
            ->add('factureAuNomDe', TextType::class, [
                'label' => 'Facture au nom de',
                'attr' => ['placeholder' => 'Nom de la personne ou société'],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message (facultatif)',
                'required' => false,
                'attr' => ['placeholder' => 'Ajoutez un message si nécessaire'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Configurer les options par défaut du formulaire
        $resolver->setDefaults([
            'data_class' => DemandeFacture::class, // Associe ce formulaire à l'entité DemandeFacture
        ]);
    }
}
