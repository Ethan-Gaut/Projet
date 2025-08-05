<?php

namespace App\Form;

use App\Entity\Pdf;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PdfType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('name', null, [
            'label' => 'Nom du document',
            'attr' => [
                'placeholder' => 'Ex: Rapport annuel',
                'class' => 'form-control',
            ],
        ])
        ->add('file', FileType::class, [
            'label' => 'Téléverser un fichier PDF',
            'mapped' => false,
            'required' => true,
            'constraints' => [
                new File([
                    'maxSize' => '10M',
                    'mimeTypes' => ['application/pdf'],
                    'mimeTypesMessage' => 'Merci d’envoyer un fichier PDF valide.',
                ])
            ],
            'attr' => [
                'class' => 'form-control-file', // ou 'form-control' selon ton CSS
            ],
        ])
    ;
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pdf::class,
        ]);
    }
}
