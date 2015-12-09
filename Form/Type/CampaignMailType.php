<?php

namespace Flower\MarketingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CampaignMailType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name')
                ->add('assignee')
                ->add('assignee', 'genemu_jqueryselect2_entity', array(
                    'class' => 'Flower\ModelBundle\Entity\User\User',
                    'property' => 'getHappyName',
                ))
                ->add('mailFrom')
                ->add('mailSubject')
                ->add('mailFromName')
                ->add('template')
                ->add('contactLists', 'genemu_jqueryselect2_entity', array(
                    'class' => 'Flower\ModelBundle\Entity\Marketing\ContactList',
                    'property' => 'name',
                    'multiple' => true,
                ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Marketing\CampaignMail',
            'translation_domain' => 'CampaignMail',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'campaignmail';
    }

}
