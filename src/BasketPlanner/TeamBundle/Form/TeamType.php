<?php

namespace BasketPlanner\TeamBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TeamType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', 'textarea')
            ->add('save', 'submit', [
                'label' => is_null($options['data']->getId()) ? 'Sukurti' : 'Redaguoti'
            ])
            ->add('type', 'entity', [
                'label' => 'Komandos tipas:',
                'class' => 'BasketPlanner\MatchBundle\Entity\Type',
                'data_class' => 'BasketPlanner\MatchBundle\Entity\Type',
                'choice_label' => 'name',
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                              ->select('t')
                              ->where('t.players <> 2');
                }
            ]);
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BasketPlanner\TeamBundle\Entity\Team',
            'for_editing' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'basketplanner_teambundle_team';
    }
}
