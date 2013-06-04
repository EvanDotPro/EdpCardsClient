<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'edpcardsclient_service_url' => 'http://cards.evan.pro/api',
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                __DIR__ . '/../public',
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'EdpCardsClient\Controller\GameController',
                        'action' => 'index',
                    ),
                ),
            ),
            'games' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/games',
                    'defaults' => array(
                        'controller' => 'EdpCardsClient\Controller\GameController',
                        'action' => 'index',
                    ),
                ),
                'child_routes' => array(
                    'game' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'  => '/:game_id',
                            'constraints' => array(
                                'game_id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'game',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'join' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'  => '/join',
                                    'defaults' => array(
                                        'action' => 'join',
                                    ),
                                ),
                            ),
                            'submit' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'  => '/submit',
                                    'defaults' => array(
                                        'action' => 'submit',
                                    ),
                                ),
                            ),
                            'round' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route'  => '/:round_id',
                                    'constraints' => array(
                                        'round_id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'action' => 'round',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'new' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route'  => '/new',
                            'defaults' => array(
                                'action' => 'new',
                            ),
                        ),
                    ),
                    'create' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route'  => '/create',
                            'defaults' => array(
                                'action' => 'create',
                            ),
                        ),
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/logout',
                    'defaults' => array(
                        'controller' => 'EdpCardsClient\Controller\GameController',
                        'action'     => 'logout',
                    ),
                ),
            ),
            'login' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/login',
                    'defaults' => array(
                        'controller' => 'EdpCardsClient\Controller\GameController',
                        'action'     => 'login',
                    ),
                ),
            ),
        ),
    ),

    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Games',
                'route' => 'home',
            ),
        ),
    ),
    'service_manager' => array(
         'factories' => array(
             'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
         ),
        'invokables' => array(
            'edpcardsclient_gameservice'   => 'EdpCardsClient\Service\Game',
            'edpcardsclient_playerservice' => 'EdpCardsClient\Service\Player',
            'session'                      => 'Zend\Session\Container'
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'EdpCardsClient\Controller\GameController' => 'EdpCardsClient\Controller\GameController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ),
    ),
);
