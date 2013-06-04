<?php
namespace EdpCardsClient\Mapper;

use Zend\Stdlib\Hydrator\Filter;
use EdpCards\Entity;

class Game extends AbstractRestMapper
{
    public function getList()
    {
        $games = $this->request('/games');

        if(!$games) {
            return array();
        }

        $list = array();
        foreach($games as $game) {
            $gameEntity = $this->getHydrator()->hydrate($game, new Entity\Game);
            $players = array();
            foreach ($game['players'] as $player) {
                $players[] = $this->getHydrator()->hydrate($player, new Entity\Player);
            }
            $gameEntity->setPlayers($players);
            $list[] = $gameEntity;
        }

        return $list;
    }

    public function get($id)
    {
        $game = $this->request(sprintf('/games/%d', $id));

        $players = array();
        foreach ($game['players'] as $player) {
            $players[] = $this->getHydrator()->hydrate($player, new Entity\Player);
        }

        $game = $this->getHydrator()->hydrate($game, new Entity\Game);
        $game->setPlayers($players);

        return $game;
    }

    public function create(Entity\Game $game, Entity\Player $player)
    {
        $hydrator = $this->getHydrator();

        $data = array_merge(
            $hydrator->extract($game),
            array(
                'player_id' => $player->id
            )
        );

        if (!$game = $this->request('/games', 'POST', $data)) {
            return false;
        }

        $game = $hydrator->hydrate($game, new Entity\Game);

        return $game;
    }

    public function submitAnswers($gameId, $roundId, $playerId, $cardIds)
    {
        $data = array(
            'player_id' => $playerId,
            'card_ids'  => $cardIds,
        );
        $result = $this->request(sprintf('/games/%d/rounds/%d', $gameId, $roundId), 'PUT', $data);
        return $result;
    }

    public function getRoundInfo($gameId, $roundId = null)
    {
        $roundId = $roundId ?: 'latest';
        $round = $this->request(sprintf('/games/%d/rounds/%s', $gameId, $roundId));
        $round['black_card'] = $this->getHydrator()->hydrate($round['black_card'], new Entity\Card);

        return $round;
    }

    public function getPlayerCards($gameId, $playerId)
    {
        $playerCards = $this->request(sprintf('/games/%d/players/%d', $gameId, $playerId));
        foreach ($playerCards['cards'] as $i => $card) {
            $playerCards['cards'][$i] = $this->getHydrator()->hydrate($card, new Entity\Card);
        }

        return $playerCards['cards'];
    }

    public function joinGame($gameId, $playerId)
    {
        $data = array('player_id' => $playerId);
        $player = $this->request(sprintf('/games/%d/players', $gameId), 'POST', $data);
        return $player;
    }

    public function getDecks()
    {
        return $this->request('/decks');
    }

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->hydrator = parent::getHydrator();
            foreach (array('getId', 'getPlayers', 'getPlayerCount') as $filter) {
                $this->hydrator->addFilter($filter, new Filter\MethodMatchFilter($filter), Filter\FilterComposite::CONDITION_AND);
            }
        }

        return $this->hydrator;
    }
}
