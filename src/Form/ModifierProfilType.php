<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class ModifierProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('username', TextType::class, [
                'label' => 'Pseudo'
            ])
            ->add('courriel', EmailType::class)
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone (facultatif)'
            ])
            ->add('site',EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'multiple'=>false,
                'expanded'=>false
            ])
            ->add('nomPhoto', FileType::class, [
                'label' => 'Ma photo',
                'mapped' => false,
                'required' => false,
                 'constraints' => [
                    new File([
                             'maxSize' => '1024k',
                             'mimeTypes' => [
                                 'image/*',
                             ],
                             'mimeTypesMessage' => 'Please upload a valid image',
                         ])
                ]
            ])
            ->add('Enregistrer' , SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
