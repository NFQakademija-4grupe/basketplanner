<?php

namespace BasketPlanner\MatchBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search_text', 'text', [
                'required' => false,
                'constraints' => new Length(['min' => 5, 'minMessage' => 'Paieškos tekstas per trumpas'])
            ])
            ->add('type', 'entity', [
                'class' => 'BasketPlanner\MatchBundle\Entity\Type',
                'choice_label' => 'name',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c');
                }
            ])
            ->add('min_date', 'datetime', [
                'required' => false,
                'widget' => 'single_text',
                'constraints' => new Range(['min' => '+1', 'minMessage' => 'Blogai nuordyta pradžios data'])
            ])
            ->add('max_date', 'datetime', [
                'required' => false,
                'widget' => 'single_text',
                'constraints' => new Range(['max' => '+30 days', 'maxMessage' => 'Blogai nurodyta pabaigos data'])
            ])
            ->add('search', 'submit', ['label' => 'Ieškoti'])
            ->setMethod('GET')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false
        ]);
    }

    public function getName()
    {
        return 'basket_planner_filter_match';
    }
}