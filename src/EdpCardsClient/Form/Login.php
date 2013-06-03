<?php
namespace EdpCardsClient\Form;

use Zend\Form;

class Login extends Form\Form
{
    public function __construct()
    {
        parent::__construct();

        $this->setAttribute('method', 'post');

        $this->add($email = new Form\Element\Email('email'));
        $email->setLabel('Email Address');
        $email->setAttribute('placeholder', 'Email Address');

        $this->add($displayName = new Form\Element\Text('displayName'));
        $displayName->setLabel('Display Name');
        $displayName->setAttribute('placeholder', 'Display Name');

        $this->add($submit = new Form\Element\Button('submit'));
        $submit->setLabel('Sign In');
        $submit->setAttribute('type', 'submit');
    }
}
