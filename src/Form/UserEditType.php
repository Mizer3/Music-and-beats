<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ["label"=>"Email"])
            ->add('roles', ChoiceType::class, [
                'label'=>'Type d\'utilisateur',
                'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices'  => [
                        'Artiste' => 'ROLE_USER',
                        'Beatmaker' => 'ROLE_BEATMAKER'
                        // https://symfony.com/doc/current/forms.html#passing-options-to-forms
                    ],
            ])
            ->add('pseudo', TextType::class, ['label'=>'Pseudo'])
            ->add('nom', TextType::class, ['label'=>'Nom'])
            ->add('prenom', TextType::class, ['label'=>'Prenom'])
            ->add('adresse', TextType::class, ['label'=>'Adresse'])
            ->add('codePostal', IntegerType::class, ['label'=>'Code Postal',
            'constraints' => [new Length([
                'min' => 5,
                'minMessage' => 'Le code postal doit contenir 5 chiffres',
                // max length allowed by Symfony for security reasons
                'max' => 5,
            ]),],])
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
            ->add('isVerified', CheckboxType::class, ['label'=>'Validation du compte',
            'constraints' => [
                new IsTrue([
                    'message' => 'Vous devez valider le compte',
                ]),
            ],
        ])
        ->add('isVIP', CheckboxType::class, ['label' => 'Mettre en avant'])
        ;
        $builder->get('roles')
                    ->addModelTransformer(new CallbackTransformer(
                        function ($rolesArray) {
                            // transform the array to a string
                            return count($rolesArray)? $rolesArray[0]: null;
                        },
                        function ($rolesString) {
                            // transform the string back to an array
                            return [$rolesString];
                        }
                    ));
        // $builder->set(IsVerified(true));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}