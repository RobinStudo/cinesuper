<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                "label" => "Firstname",
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            ->add('lastName', TextType::class, [
                "label" => "LastName",
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            ->add('email', EmailType::class, [
                "label" => "Email",
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            ->add('birthdate', DateType::class, [
                'label' => "Date de naissance",
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            ->add('password', PasswordType::class, [
                "label" => "Password",
                "constraints" => [
                    new Regex([
                        "pattern" => "/^\S+$/",
                        "message" => "Don't use spaces in your password."
                    ])
                ],
                "attr" => [
                    'class' => "form-control",
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            "csrf_protection" => true,
        ]);
    }
}
