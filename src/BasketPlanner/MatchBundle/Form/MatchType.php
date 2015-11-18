<?php

namespace BasketPlanner\MatchBundle\Form;

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
            ->add('save', 'submit', [
                'label' => is_null($options['data']->getId()) ? 'Sukurti mačą' : 'Redaguoti'
            ])
        ;

        if (!$options['for_editing'])
        {
            $builder->add('beginsAt', 'datetime')
                ->add('court', 'entity', [
                    'class' => 'BasketPlanner\MatchBundle\Entity\Court',
                    'choice_label' => 'address',
                    'query_builder' => function(EntityRepository $er) {
                        return $er
                            ->createQueryBuilder('c')
                            ->where('c.approved = :approved')
                            ->setParameter('approved', 1);
                    }
                ])
                ->add('type', 'entity', [
                    'class' => 'BasketPlanner\MatchBundle\Entity\Type',
                    'data_class' => 'BasketPlanner\MatchBundle\Entity\Type',
                    'choice_label' => 'name',
                    'expanded' => true,
                    'multiple' => false
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'BasketPlanner\MatchBundle\Entity\Match',
            'for_editing' => false
        ]);
    }

    public function getName()
    {
        return 'basket_planner_create_match';
    }
}