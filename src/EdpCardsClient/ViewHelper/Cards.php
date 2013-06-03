<?php
namespace EdpCardsClient\ViewHelper;

use Zend\View\Helper\AbstractHelper;

class Cards extends AbstractHelper
{
    protected $gameService;

    public function __construct($gameService)
    {
        $this->gameService = $gameService;
    }

    public function getPlayer()
    {
        return $this->gameService->getSessionPlayer();
    }

    public function isInGame($game)
    {
        $activePlayer = $this->getPlayer();
        if (!$activePlayer) return false;
        foreach ($game->getPlayers() as $player) {
            if ($player->id == $activePlayer->id) return true;
        }

        return false;
    }
}
