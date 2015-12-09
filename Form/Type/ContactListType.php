<?php

namespace Flower\MarketingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactListType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('assignee', 'genemu_jqueryselect2_entity', array(
                'class' => 'Flower\ModelBundle\Entity\User\User',
                'property' => 'getHappyName',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Marketing\ContactList',
            'translation_domain' => 'ContactList',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'contactlist';
    }
}
