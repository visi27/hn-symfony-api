<?php

namespace AppBundle\Form;

use AppBundle\Entity\ImportRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class, array('label' => 'Invoices (PDF file)'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ImportRequest::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_import_request_form';
    }
}