<?php

namespace BasketPlanner\TeamBundle\Form;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InviteType extends AbstractType
{
    protected $inviterId;

    public function __construct ($inviterId)
    {
        $this->inviterId = $inviterId;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user','entity', [
                'class' => 'BasketPlanner\UserBundle\Entity\User',
                'property' => 'fullName',
                'placeholder' => 'Pasirinkite vartotoją',
                'query_builder' => function (EntityRepository $er) {
                    $query = $er->createQueryBuilder('user')
                        ->select('user')
                        ->where('user.id = :userId')
                        ->setParameter('userId', null);

                    return $query;
                }
            ])
            ->add('team', 'entity', [
                'label' => 'Komanda',
                'placeholder' => 'Pasirinkite komandą',
                'class' => 'BasketPlanner\TeamBundle\Entity\Team',
                'data_class' => 'BasketPlanner\TeamBundle\Entity\Team',
                'choice_label' => 'name',
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    $query = $er->createQueryBuilder('team')
                        ->select('team')
                        ->leftJoin('team.type', 't', Join::WITH, 'team.type = t.id')
                        ->innerJoin('BasketPlanner\TeamBundle\Entity\TeamUser', 'tu', 'WITH', 'team.id=tu.team')
                        ->where('tu.user = :userId')
                        ->andWhere('tu.role = :role')
                        ->groupBy('tu.team')
                        ->setParameter('userId', $this->inviterId)
                        ->setParameter('role', 'Owner');

                    return $query;
                }
            ])
            ->add('save', 'submit', [
                'label' => 'Pakviesti'
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BasketPlanner\TeamBundle\Entity\Invite',
            'for_editing' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'basketplanner_teambundle_invite';
    }
}
