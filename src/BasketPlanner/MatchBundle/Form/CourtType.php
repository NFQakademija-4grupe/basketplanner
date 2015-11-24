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
            ->add('id', 'integer')
            ->add('address', 'text')
            ->add('latitude', 'number', array(
                'precision' => 18))
            ->add('longitude', 'number', array(
                'precision' => 18))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'BasketPlanner\MatchBundle\Entity\Court',
            'for_editing' => false
        ]);
    }

    public function getName()
    {
        return 'basket_planner_create_court';
    }
}