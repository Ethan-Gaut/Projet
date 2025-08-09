<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('text1', CKEditorType::class, [
                'config' => [
                    'toolbar' => 'standard', // tu peux customiser la barre d'outils
                    'uiColor' => '#ffffff',
                    'height' => 300,         // hauteur en pixels (tu peux augmenter)
                ],
                'label' => 'texte',  // change le label affiché dans le formulaire
                'required' => false,
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', // à adapter si tu préfères afficher le nom
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                'class' => 'category-checkboxes', // classe CSS optionnelle pour customiser
                ],  
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (JPG ou PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Merci d’uploader une image JPG ou PNG valide',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
