<?php

namespace BasketPlanner\MatchBundle\Form;

use BasketPlanner\MatchBundle\Entity\Court;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', 'textarea')
            ->add('beginsAt', 'datetime', [
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'data' => new \DateTime()
            ])
            ->add('court', new CourtType())
            ->add('type', 'entity', [
                'class' => 'BasketPlanner\MatchBundle\Entity\Type',
                'data_class' => 'BasketPlanner\MatchBundle\Entity\Type',
                'choice_label' => 'name',
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('c');
                }
            ])
            ->add('save', 'submit', [
                'label' =>  'Sukurti mačą'
            ]);

    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'BasketPlanner\MatchBundle\Entity\Match'
        ]);
    }
    public function getName()
    {
        return 'basket_planner_create_match';
    }
}