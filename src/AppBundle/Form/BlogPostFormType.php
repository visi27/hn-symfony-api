<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogPostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add(
                'category',
                EntityType::class,
                [
                    'query_builder' => function (CategoryRepository $repository) {
                        return $repository->createAlphabeticalQueryBuilder();
                    },
                    'class' => Category::class,
                    'placeholder' => 'Choose a Category',
                ]
            )
            ->add('summary')
            ->add(
                'content'
            )
            ->add(
                'isPublished',
                ChoiceType::class,
                [
                    'choices' => [
                        'Yes' => true,
                        'No' => false,
                    ],
                ]
            )
            ->add(
                'publishedAt',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'attr' => ['class' => 'js-datepicker'],
                    'html5' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\BlogPost',
                'csrf_protection' => false,
                'is_edit' => false,
                'allow_extra_fields' => true,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
