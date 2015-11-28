<?php

namespace BasketPlanner\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName');
        $builder->add('lastName');
        $builder->add('gender', 'choice', array(
            'choices'  => array('male' => 'form.male', 'female' => 'form.female'),
            'required' => true,
            'label' => 'form.gender',
            'translation_domain' => 'FOSUserBundle'
        ));
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'basketplanner_user_form_registration';
    }
}