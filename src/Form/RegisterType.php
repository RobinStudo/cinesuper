<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
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
            ->add('FirstName', TextType::class, [
                "label" => "Firstname",
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            ->add('LastName', TextType::class, [
                "label" => "LastName",
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            ->add('Email', EmailType::class, [
                "label" => "Email",
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            ->add('Birthdate', BirthdayType::class, [
                'label' => "Date de naissance",
                'years' => range(date('Y') - 6, date('Y') - 100),
                'months' => range(date('m'), 12),
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            ->add('Password', PasswordType::class, [
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
