<?php

namespace BasketPlanner\MatchBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', 'text')
            ->add('save', 'submit', ['label' => 'Rašyti'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BasketPlanner\MatchBundle\Entity\Comment',
        ));
    }

    public function getName()
    {
        return 'basket_planner_create_comment';
    }
}