<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace EdpCardsClient;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\Hydrator;
use EdpCards\Entity;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $e->getViewModel()->loginForm = $e->getApplication()->getServiceManager()->get('EdpCardsClient\Form\Login');
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'edpcardsclient_gamemapper' => function($sm) {
                    $config = $sm->get('config');
                    $url = $config['edpcardsclient_service_url'];
                    return new Mapper\Game($url);
                },
                'edpcardsclient_playermapper' => function($sm) {
                    $config = $sm->get('config');
                    $url = $config['edpcardsclient_service_url'];
                    $mapper = new Mapper\Player($url);
                    return $mapper;
                },
                'EdpCardsClient\Form\CreateGame' => function($sm) {
                    $form = new Form\CreateGame;
                    $form->setHydrator(new Hydrator\ClassMethods);
                    $form->bind(new Entity\Game);

                    $decks = array();
                    if ($gameService = $sm->get('edpcardsclient_gameservice')) {
                        foreach ($gameService->getDecks() ?: array() as $deck) {
                            $decks[$deck['id']] = $deck['description'];
                        }
                    }

                    $form->get('decks')->setValueOptions($decks);

                    return $form;

                },
                'EdpCardsClient\Form\Login' => function($sm) {
                    $form = new Form\Login;
                    $form->setHydrator(new Hydrator\ClassMethods);
                    $form->bind(new Entity\Player);

                    return $form;
                },
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'cards' => function($sm) {
                    return new ViewHelper\Cards($sm->getServiceLocator()->get('edpcardsclient_playerservice'));
                },
            ),
        );
    }
}
