<?php
namespace EdpCardsClient\Mapper;

use Zend\Stdlib\Hydrator\Filter;
use EdpCards\Entity;

class Player extends AbstractRestMapper
{
    protected $filteredHydrator;

    public function getList()
    {
        $players = $this->request('/players');

        if(!$players)
            return false;

        $list = array();

        $hydrator = $this->getHydrator();
        new \Zend\Stdlib\Hydrator\ClassMethods();
        foreach($players as $player) {
            $list[] = $hydrator->hydrate($player, new Entity\Player);
        }

        return $list;
    }

    public function get($id)
    {
        $player = $this->request(sprintf('/players/%d', $id));
        return $this->getHydrator()->hydrate($player, new Entity\Player);
    }

    public function create(Entity\Player $player)
    {
        $player = $this->request('/players', 'POST', $this->getHydrator()->extract($player));
        return $this->getHydrator(false)->hydrate($player, new Entity\Player);
    }

    public function getHydrator($filter = true)
    {
        if ($filter) {
            if (!$this->filteredHydrator) {
                $hydratorClass = get_class(parent::getHydrator());
                $this->filteredHydrator = new $hydratorClass;
                $this->filteredHydrator->addFilter('getId', new Filter\MethodMatchFilter('getId'), Filter\FilterComposite::CONDITION_AND);
                $this->filteredHydrator->addFilter('getPoints', new Filter\MethodMatchFilter('getPoints'), Filter\FilterComposite::CONDITION_AND);
            }

            return $this->filteredHydrator;
        }

        return parent::getHydrator();
    }
}
