<?php

namespace BasketPlanner\MatchBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourtType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'integer', array('required' => false))
            ->add('address', 'text')
            ->add('latitude', 'number', array(
                'precision' => 18,
                'required' => false))
            ->add('longitude', 'number', array(
                'precision' => 18,
                'required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'BasketPlanner\MatchBundle\Entity\Court'
        ]);
    }

    public function getName()
    {
        return 'basket_planner_create_court';
    }
}