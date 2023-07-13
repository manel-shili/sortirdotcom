<?php

namespace App\Form;

use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RechercheSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'multiple' => false,
                'expanded' => false
            ])
            ->add('mot_cle', TextType::class, [
                'label' => 'Recherché dans le titre',
                'required' => false
            ])
            ->add('date_debut', DateType::class, [
                'label' => 'Entre',
                'required' => false,
                'widget' => 'single_text'
            ])
            ->add('date_fin', DateType::class, [
                'label' => 'Et',
                'required' => false,
                'widget' => 'single_text'
            ])
            ->add('organisateur', CheckboxType::class, [
                'required' => false
            ])
            ->add('inscrit', CheckboxType::class, [
                'required' => false
            ])
            ->add('passe', CheckboxType::class, [
                'required' => false
            ])
            ->add('Rechercher', SubmitType::class)
        ;
    }
}
