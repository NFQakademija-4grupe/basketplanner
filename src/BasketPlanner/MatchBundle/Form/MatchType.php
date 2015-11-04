<?php

namespace BasketPlanner\MatchBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', null, array('widget' => 'single_text'))
            ->add('date', 'date')
            ->add('time', 'time')
            ->add('district', 'text')
            ->add('latitude', 'text')
            ->add('longitude', 'text')
            ->add('type', 'text')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BasketPlanner\MatchBundle\Entity\Match',
        ));
    }

    public function getName()
    {
        return 'match';
    }
}