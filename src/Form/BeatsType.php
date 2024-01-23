<?php

namespace App\Form;

use App\Entity\Beats;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BeatsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label'=>'Nom du Beat'])
            ->add('price', TextType::class, ['label'=>'Prix'])
            ->add('imageName', FileType::class, [
                'label'=>'Photo',
                'mapped'=> false,
                'required'=>false,
                'constraints' => [
                    new File([
                        'maxSize' => '10240k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez ajouter une image valide',
                    ])
                ],
            ])
            ->add('description', TextType::class, ['label'=>'Description du Beat'])
            ->add('beatName', FileType::class, [
                'label'=>'Beat',
                'mapped'=> false,
                'required'=>false,
                'constraints' => [
                    new File([
                        'maxSize' => '50240k',
                        'mimeTypes' => [
                            'audio/mpeg',
                        ],
                        'mimeTypesMessage' => 'Veuillez ajouter un fichier MP3',
                    ])
                ],
            ])
            ->add('user')
            ->add('category')
            ->add('isVIP', ChoiceType::class, ['label' => 'Mettre en avant',
            'choices' => [
                'Mettre en avant' => true,
                'Ne pas mettre en avant' => false
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Beats::class,
        ]);
    }
}
