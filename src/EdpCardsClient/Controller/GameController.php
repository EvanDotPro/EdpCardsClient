<?php
namespace EdpCardsClient\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\Hydrator;

use Application\Form;
use EdpCards\Entity;

class GameController extends AbstractActionController
{
    protected $loginForm;
    protected $createGameForm;
    protected $playerService;
    protected $gameService;
    protected $hydrator;

    public function indexAction()
    {
        $view = new ViewModel;
        $view->games = $this->getGameService()->getList()?:array();
        $view->players = $this->getPlayerService()->getList()?:array();

        return $view;
    }

    public function gameAction()
    {
        $game = $this->getGameservice()->get($this->params('game_id'));

        if (!$game) {
            return $this->getResponse()->setStatusCode(404);
        }

        $view = new ViewModel;
        $view->game = $game;

        $round = $this->getGameService()->getRoundInfo($game->id);
        $view->blackCard = $round['black_card'];
        $view->roundId   = $round['round_id'];

        $player = $this->getPlayerService()->getSessionPlayer();
        $inGame = false;
        if ($player) {
            foreach ($game->players as $thisPlayer) {
                if ($player->id == $thisPlayer->id) $inGame = true;
            }
        }
        if (!$inGame || !$player) {
            return $this->redirect()->toRoute('games/game/round', array('game_id' => $game->id, 'round_id' => $round['round_id']));
        }

        $view->cards = $this->getGameService()->getPlayerCards($game->id, $player->id);

        foreach ($round['players'] as $donePlayer) {
            if ($player->id == $donePlayer->id) {
                return $this->redirect()->toRoute('games/game/round', array('game_id' => $game->id, 'round_id' => $round['round_id']));
            }
        }

        return $view;
    }

    protected function getRoundInfoViewModel($json = false)
    {
        $game  = $this->getGameservice()->get($this->params('game_id'));
        $round = $this->getGameService()->getRoundInfo($this->params('game_id'), $this->params('round_id'));
        $latestRound = $this->getGameService()->getRoundInfo($this->params('game_id'));
        $view = array();
        $view['blackCard'] = $round['black_card'];
        $view['players'] = $round['players'];
        $view['game'] = $game;
        if ($round['round_id'] < $latestRound['round_id']) {
            $view['newRound'] = true;
        } else {
            $view['newRound'] = false;
        }

        if ($json || $this->params()->fromQuery('json')) {
            $view = new JsonModel($this->toArray($view));
        } else {
            $view = new ViewModel($view);
        }
        if ($this->params()->fromQuery('ajax')) {
            $view->setTerminal(true);
        }

        return $view;
    }

    public function roundAction()
    {
        return $this->getRoundInfoViewModel();
    }

    public function logoutAction()
    {
        $session = $this->serviceLocator->get('session');
        $session->player = null;

        return $this->redirect()->toRoute('home');
    }

    public function loginAction()
    {
        $data = $this->getRequest()->getPost();

        $form = $this->getLoginForm();
        $form->setData($data);

        if ($form->isValid()) {
            $player = $form->getData();

            $player = $this->getPlayerService()->create($player);
        }

        if (!empty($data['backurl'])) {
            return $this->redirect()->toUrl($data['backurl']);
        } else {
            return $this->redirect()->toRoute('home');
        }
    }

    public function joinAction()
    {
        $player = $this->getPlayerService()->getSessionPlayer();
        $gameId = $this->params('game_id');
        $this->getGameService()->joinGame($gameId, $player->id);
        return $this->redirect()->toRoute('games/game', array('game_id' => $gameId));
    }

    public function submitAction()
    {
        $data = $this->params()->fromPost();
        $player = $this->getPlayerService()->getSessionPlayer();
        $test = $this->getGameService()->submitAnswers($this->params('game_id'), $data['round_id'], $player->id, $data['cards']);
        return $this->redirect()->toRoute('games/game/round', array('game_id' => $this->params('game_id'), 'round_id' => $data['round_id']));
    }

    public function newAction()
    {
        return new ViewModel(array('createGameForm' => $this->getCreateGameForm()));
    }

    public function createAction()
    {
        $data = $this->getRequest()->getPost();

        $form = $this->getCreateGameForm();
        $form->setData($data);

        $player = $this->getPlayerService()->getSessionPlayer();

        if ($player && $form->isValid()) {
            $game = $form->getData();

            $game = $this->getGameService()->create($game, $player);

            if ($game) {
                return $this->redirect()->toRoute('games/game', array('game_id' => $game->id));
            }
        }

        return $this->redirect()->toRoute('home');
    }

    public function setPlayer(Entity\Player $player) {
        $this->player = $player;
    }

    protected function getGameService()
    {
        if (!$this->gameService) {
            $this->gameService = $this->getServiceLocator()->get('edpcardsclient_gameservice');
        }

        return $this->gameService;
    }
    protected function getPlayerService()
    {
        if (!$this->playerService) {
            $this->playerService = $this->getServiceLocator()->get('edpcardsclient_playerservice');
        }

        return $this->playerService;
    }

    protected function getLoginForm()
    {
        if (!$this->loginForm) {
            $this->loginForm = $this->getServiceLocator()->get('EdpCardsClient\Form\Login');
        }

        return $this->loginForm;
    }

    protected function getCreateGameForm()
    {
        if (!$this->createGameForm) {
            $this->createGameForm = $this->getServiceLocator()->get('EdpCardsClient\Form\CreateGame');
        }

        return $this->createGameForm;
    }

    protected function toArray($value)
    {
        if (is_array($value) or $value instanceof \Traversable) {
            $return = array();
            foreach ($value as $key => $item) {
                $return[$key] = $this->toArray($item);
            }
            return $return;
        } else if (is_object($value)) {
            return $this->toArray($this->getHydrator()->extract($value));
        }
        return $value;
    }

    protected function getHydrator()
    {
        if (!$this->hydrator) {
            $this->hydrator = new Hydrator\ClassMethods;
        }

        return $this->hydrator;
    }
}
