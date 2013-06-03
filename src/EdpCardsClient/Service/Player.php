<?php
namespace EdpCardsClient\Service;

use Zend\ServiceManager as SM;
use Zend\EventManager as EM;

use EdpCards\Entity;

class Player implements SM\ServiceLocatorAwareInterface
{
    use SM\ServiceLocatorAwareTrait;

    protected $playerMapper;
    protected $sessionPlayer;

    public function getSessionPlayer()
    {
        if (!$this->sessionPlayer) {
            $session = $this->getServiceLocator()->get('session');
            if (!$session || !$session->player) {
                return false;
            }

            $this->sessionPlayer = $this->getPlayerMapper()->getHydrator(false)->hydrate($session->player, new Entity\Player);
        }

        return $this->sessionPlayer;
    }

    public function getList()
    {
        return $this->getPlayerMapper()->getList();
    }

    public function get($id)
    {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        return $this->getPlayerMapper()->get($id);
    }

    public function create(Entity\Player $player)
    {
        $player = $this->getPlayerMapper()->create($player);
        $session = $this->getServiceLocator()->get('session');
        $session->player = $this->getPlayerMapper()->getHydrator(false)->extract($player);
        $this->sessionPlayer = $player;
        return $player;
    }

    public function update(Entity\Player $player)
    {
        return $this->getPlayerMapper()->update($player);
    }

    public function delete($id) {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        
        return $this->getPlayerMapper()->delete($id);
    }
    
    public function getPlayerMapper() {
        if(!$this->playerMapper) {
            $this->playerMapper = $this->getServiceLocator()->get('edpcardsclient_playermapper');
        }
        
        return $this->playerMapper;
    }
    
    public function setPlayerMapper($playerMapper) {
        $this->playerMapper = $playerMapper;
    }
}
