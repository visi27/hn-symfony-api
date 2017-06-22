<?php

namespace Tests\AppBundle\Form;

use AppBundle\Form\LoginForm;
use Symfony\Component\Form\Test\TypeTestCase;

class LoginFormTest extends TypeTestCase
{
    public function testSubmitValidData(){
        $formData = [
            "_username" => "filan.fisteku@gmail.com",
            "_password" => "I<3Pizza"
        ];

        $form = $this->factory->create(LoginForm::class);

        //$object = Login::fromArray($formData);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        //$this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
