<?php

namespace Flower\MarketingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactListFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('name', 'filter_text')
            ->add('enabled', 'filter_boolean')
            ->add('created', 'filter_date')
            ->add('updated', 'filter_date')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Flower\ModelBundle\Entity\Marketing\ContactList',
            'csrf_protection'   => false,
            'validation_groups' => array('filter'),
            'method'            => 'GET',
            'translation_domain' =>  'ContactList',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'contactlist_filter';
    }
}
