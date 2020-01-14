<?php

namespace App\Form;
use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("file", VichFileType::class, [
                "label" => "Add a new profil picture",
                "attr" => [
                    "class" => "addPictureTitle",
                ],
                "required" => true,
                "allow_delete" => true,
                "by_reference" => false,
                "constraints" => [
                    new File([
                        "maxSize" => "1M",
                        "maxSizeMessage" => "Your file is too big (1M max).",
                        "mimeTypes" => [
                            "image/jpeg",
                            "image/png",
                        ],
                        "mimeTypesMessage" => "You can only upload jpeg/png files.",
                    ]),
                ],
            ])
            ->add("save", SubmitType::class, [
                "label" => "Envoyer",
                "attr" => [
                    "class" => "btn btn-primary",
                ]
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Picture::class,
            "csrf_protection" => true,
            "attr" => [
                "novalidate" => "novalidate",
            ]
        ]);
    }
}
