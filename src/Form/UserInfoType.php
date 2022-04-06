<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class UserInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ["label"=>"Email"])
            // ->add('roles')
            // ->add('password')
            ->add('pseudo', TextType::class, ['label'=>'Pseudo'])
            ->add('nom', TextType::class, ['label'=>'Nom'])
            ->add('prenom', TextType::class, ['label'=>'Prenom'])
            ->add('adresse', TextType::class, ['label'=>'Adresse'])
            ->add('codePostal', IntegerType::class, ['label'=>'Code Postal'])
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
            // ->add('isVerified')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
