<?php
namespace EdpCardsClient\Service;

use Zend\ServiceManager as SM;
use Zend\EventManager as EM;

use EdpCards\Entity;

class Game implements SM\ServiceLocatorAwareInterface
{
    protected $gameMapper;

    /**
     * @var SM\ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    public function getList()
    {
        return $this->getGameMapper()->getList();
    }

    public function get($id)
    {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

        return $this->getGameMapper()->get($id);
    }

    public function create(Entity\Game $game, Entity\Player $player)
    {
        return $this->getGameMapper()->create($game, $player);
    }

    public function update(Entity\Game $game)
    {
        return $this->getGameMapper()->update($game);
    }

    public function delete($id)
    {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

        return $this->getGameMapper()->delete($id);
    }

    public function joinGame($gameId, $playerId)
    {
        return $this->getGameMapper()->joinGame($gameId, $playerId);
    }

    public function getRoundInfo($gameId, $roundId = null)
    {
        $round = $this->getGameMapper()->getRoundInfo($gameId, $roundId);
        return $round;
    }

    public function getPlayerCards($gameId, $playerId)
    {
        return $this->getGameMapper()->getPlayerCards($gameId, $playerId);
    }

    public function getDecks()
    {
        $decks = $this->getGameMapper()->getDecks();
        if (!$decks) {
            return false;
        }

        foreach ($decks as &$deck) {
            if (empty($deck['description'])) {
                $deck['description'] = $deck['id'];
            }
        }

        return $decks;
    }

    public function getGameMapper()
    {
        if (!$this->gameMapper) {
            $this->gameMapper = $this->getServiceLocator()->get('edpcardsclient_gamemapper');
        }

        return $this->gameMapper;
    }

    public function setGameMapper($gameMapper)
    {
        $this->gameMapper = $gameMapper;
    }

    /**
     * Set service locator
     *
     * @param SM\ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function setServiceLocator(SM\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return SM\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
