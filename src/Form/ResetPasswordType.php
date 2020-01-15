<?php

namespace App\Form;

use App\Entity\ResetPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /* Nouveau mot de passe */
            ->add('newPassword', RepeatedType::class, [
                'label' => false,
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => "Nouveau mot de passe",
                    'required' => true,
                    'constraints' => [
                        new NotNull([
                            'message' => "Saisir votre nouveau mot de passe",
                        ]),
                        new NotBlank([
                            'message' => "Saisir votre nouveau mot de passe",
                        ]),
                        new Length([
                            'min' => "8",
                            'minMessage' => "Votre mot de passe doit contenir au moins 8 caractères.",
                        ]),
                        new Regex([
                            "pattern" => "/^\S+$/",
                            "message" => "N'utilisez pas d'espace dans votre mot de passe."
                        ]),
                    ],
                    'attr' => [
                        'placeholder' => "Tapez votre nouveau mot de passe ...",
                        'class' => "form-control mb-3",
                    ],
                ],
                'second_options' => [
                    'label' => "Confirmer votre nouveau mot de passe",
                    'constraints' => [
                        new NotBlank([
                            'message' => "Repéter le mot de passe",
                        ]),
                    ],
                    'attr' => [
                        'placeholder' => "Confirmer votre nouveau mot de passe ...",
                        'class' => "form-control mb-3",
                    ],
                ],
                'invalid_message' => "Les mots de passe doivent etre identiques.",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data-class' => ResetPassword::class,
            'csrf_protection' => true,
            'label' => false,
            'attr' => [
                "novalidate" => "novalidate",
            ]
        ]);
    }
}
