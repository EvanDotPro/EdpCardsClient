<?php
namespace EdpCardsClient\Form;

use Zend\Form;

class CreateGame extends Form\Form
{
    public function __construct()
    {
        parent::__construct();

        $this->add($name = new Form\Element\Text('name'));
        $name->setAttribute('id', 'name');
        $name->setLabel('Name');

        $this->add($decks = new Form\Element\Select('decks'));
        $decks->setAttribute('id', 'decks');
        $decks->setLabel('Decks');
        $decks->setAttribute('multiple', true);
        $decks->setEmptyOption(null);

        $this->add($submit = new Form\Element\Button('submit'));
        $submit->setLabel('Create Game');
        $submit->setAttribute('type', 'submit');
    }
}
