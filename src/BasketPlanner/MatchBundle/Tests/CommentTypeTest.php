<?php

namespace BasketPlanner\MatchBundle\Tests;

use BasketPlanner\MatchBundle\Entity\Court;
use BasketPlanner\MatchBundle\Entity\Match;
use BasketPlanner\MatchBundle\Entity\Type;
use BasketPlanner\MatchBundle\Form\CommentType;
use BasketPlanner\UserBundle\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;

class CommentTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'message' => 'Cia yra maco pranesimo testas.',
            'createdAt' => new \DateTime(),
            'match' => $match,
            'user' => $user
        );

        $type = new CommentType();

        $form = $this->factory->create($type, $formData. ['for_editing' => false]);

        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}