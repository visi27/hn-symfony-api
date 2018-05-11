<?php
/**
 * Created by Evis Bregu <evis.bregu@gmail.com>.
 * Date: 5/11/18
 * Time: 12:34 PM
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FavoriteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author')
            ->add('createdAt')
            ->add('createdAtI')
            ->add('numComments')
            ->add('objectID')
            ->add('points')
            ->add('storyText')
            ->add('title')
            ->add('url');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\Favorite',
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
