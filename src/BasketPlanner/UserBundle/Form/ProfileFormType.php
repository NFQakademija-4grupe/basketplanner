<?php

namespace BasketPlanner\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileFormType extends AbstractType {

    private $class;

    public function __construct($class) {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('firstName', 'text', array(
            'label' => 'form.first_name',
            'translation_domain' => 'FOSUserBundle')
        );
        $builder->add('lastName', 'text', array(
            'label' => 'form.last_name',
            'translation_domain' => 'FOSUserBundle')
        );
        $builder->add('gender', 'choice', array(
            'choices'  => array('male' => 'form.male', 'female' => 'form.female'),
            'required' => true,
            'label' => 'form.gender',
            'translation_domain' => 'FOSUserBundle')
        );

        $builder->add('age', 'integer', array(
            'label' => 'form.age',
            'translation_domain' => 'FOSUserBundle')
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention' => 'profile',
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'basketplanner_user_form_profile';
    }
}