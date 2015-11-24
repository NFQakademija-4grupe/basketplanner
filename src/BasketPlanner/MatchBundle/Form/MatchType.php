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
            ->add('save', 'submit', [
                'label' => is_null($options['data']->getId()) ? 'Sukurti mačą' : 'Redaguoti'
            ])
        ;

        if (!$options['for_editing'])
        {
            $builder->add('beginsAt', 'datetime')
                ->add('court', new CourtType())
                ->add('type', 'entity', [
                    'class' => 'BasketPlanner\MatchBundle\Entity\Type',
                    'choice_label' => 'name',
                    'required' => true,
                    'expanded' => true,
                    'multiple' => false,
                    'query_builder' => function(EntityRepository $er) {
                        return $er
                            ->createQueryBuilder('c');
                    }
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