<?php
namespace EdpCardsClient\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Stdlib\Hydrator;

use Application\Form;
use EdpCards\Entity;

class GameController extends AbstractActionController
{
    protected $loginForm;
    protected $createGameForm;
    protected $playerService;
    protected $gameService;

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

        $player = $this->getPlayerService()->getSessionPlayer();
        if ($player) {
            $view->cards = $this->getGameService()->getPlayerCards($game->id, $player->id);
        }

        $round = $this->getGameService()->getRoundInfo($game->id);
        $view->blackCard = $round['black_card'];

        return $view;
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
}
