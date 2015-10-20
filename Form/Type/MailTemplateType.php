<?php

namespace Flower\MarketingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MailTemplateType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name')
                ->add('type', 'choice', array(
                    'choices' => array('html' => "html", 'plain' => "plain text")
                ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Marketing\MailTemplate',
            'translation_domain' => 'MailTemplate',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mailtemplate';
    }

}
