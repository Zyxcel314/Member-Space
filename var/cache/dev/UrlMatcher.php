<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/_profiler' => [[['_route' => '_profiler_home', '_controller' => 'web_profiler.controller.profiler::homeAction'], null, null, null, true, false, null]],
        '/_profiler/search' => [[['_route' => '_profiler_search', '_controller' => 'web_profiler.controller.profiler::searchAction'], null, null, null, false, false, null]],
        '/_profiler/search_bar' => [[['_route' => '_profiler_search_bar', '_controller' => 'web_profiler.controller.profiler::searchBarAction'], null, null, null, false, false, null]],
        '/_profiler/phpinfo' => [[['_route' => '_profiler_phpinfo', '_controller' => 'web_profiler.controller.profiler::phpinfoAction'], null, null, null, false, false, null]],
        '/_profiler/open' => [[['_route' => '_profiler_open_file', '_controller' => 'web_profiler.controller.profiler::openAction'], null, null, null, false, false, null]],
        '/admin' => [[['_route' => 'admin', '_controller' => 'App\\Controller\\AdminController::index'], null, null, null, false, false, null]],
        '/gestionnaire/showUsers' => [[['_route' => 'gestionnaire.showUsers', '_controller' => 'App\\Controller\\GestionnaireController::showUsers'], null, ['GET' => 0], null, false, false, null]],
        '/gestionnaire/addUsers' => [[['_route' => 'Gestsionnaire.addUsers', '_controller' => 'App\\Controller\\GestionnaireController::addUsers'], null, ['GET' => 0], null, false, false, null]],
        '/gestionnaire/validAddUsers' => [[['_route' => 'Gestionaire.validUsers', '_controller' => 'App\\Controller\\GestionnaireController::validAddUsers'], null, ['POST' => 0], null, false, false, null]],
        '/gestionnaire/validEditUsers' => [[['_route' => 'Gestionnaire.validFormEditUsers', '_controller' => 'App\\Controller\\GestionnaireController::validFormEditUsers'], null, ['POST' => 0], null, false, false, null]],
        '/representant' => [[['_route' => 'representant_famille_index', '_controller' => 'App\\Controller\\RepresentantFamilleController::index'], null, ['GET' => 0], null, true, false, null]],
        '/representant/inscription' => [[['_route' => 'representant_famille_new', '_controller' => 'App\\Controller\\RepresentantFamilleController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/' => [[['_route' => 'home', '_controller' => 'App\\Controller\\SecurityController::home'], null, null, null, false, false, null]],
        '/inscription' => [[['_route' => 'securityRegistration', '_controller' => 'App\\Controller\\SecurityController::registration'], null, null, null, false, false, null]],
        '/login' => [[['_route' => 'securityLogin', '_controller' => 'App\\Controller\\SecurityController::login'], null, null, null, false, false, null]],
        '/checkActivation' => [[['_route' => 'securityCheckActivation', '_controller' => 'App\\Controller\\SecurityController::checkActivation'], null, null, null, false, false, null]],
        '/logout' => [[['_route' => 'securityLogout', '_controller' => 'App\\Controller\\SecurityController::logout'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_(?'
                    .'|error/(\\d+)(?:\\.([^/]++))?(*:38)'
                    .'|wdt/([^/]++)(*:57)'
                    .'|profiler/([^/]++)(?'
                        .'|/(?'
                            .'|search/results(*:102)'
                            .'|router(*:116)'
                            .'|exception(?'
                                .'|(*:136)'
                                .'|\\.css(*:149)'
                            .')'
                        .')'
                        .'|(*:159)'
                    .')'
                .')'
                .'|/gestionnaire/(?'
                    .'|(\\d+)/deleteUsers(*:203)'
                    .'|(\\d+)/editUsers(*:226)'
                .')'
                .'|/representant/([^/]++)(?'
                    .'|(*:260)'
                    .'|/edit(*:273)'
                    .'|(*:281)'
                .')'
                .'|/emailConfirmation/([^/]++)(*:317)'
                .'|/activationUser/([^/]++)(*:349)'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        38 => [[['_route' => '_twig_error_test', '_controller' => 'twig.controller.preview_error::previewErrorPageAction', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        57 => [[['_route' => '_wdt', '_controller' => 'web_profiler.controller.profiler::toolbarAction'], ['token'], null, null, false, true, null]],
        102 => [[['_route' => '_profiler_search_results', '_controller' => 'web_profiler.controller.profiler::searchResultsAction'], ['token'], null, null, false, false, null]],
        116 => [[['_route' => '_profiler_router', '_controller' => 'web_profiler.controller.router::panelAction'], ['token'], null, null, false, false, null]],
        136 => [[['_route' => '_profiler_exception', '_controller' => 'web_profiler.controller.exception::showAction'], ['token'], null, null, false, false, null]],
        149 => [[['_route' => '_profiler_exception_css', '_controller' => 'web_profiler.controller.exception::cssAction'], ['token'], null, null, false, false, null]],
        159 => [[['_route' => '_profiler', '_controller' => 'web_profiler.controller.profiler::panelAction'], ['token'], null, null, false, true, null]],
        203 => [[['_route' => 'Gestionnaire.deleteUsers', '_controller' => 'App\\Controller\\GestionnaireController::deleteUsers'], ['id'], ['GET' => 0], null, false, false, null]],
        226 => [[['_route' => 'Gestionnaire.editUsers', 'id' => null, '_controller' => 'App\\Controller\\GestionnaireController::editUsers'], ['id'], ['GET' => 0], null, false, false, null]],
        260 => [[['_route' => 'representant_famille_show', '_controller' => 'App\\Controller\\RepresentantFamilleController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        273 => [[['_route' => 'representant_famille_edit', '_controller' => 'App\\Controller\\RepresentantFamilleController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        281 => [[['_route' => 'representant_famille_delete', '_controller' => 'App\\Controller\\RepresentantFamilleController::delete'], ['id'], ['DELETE' => 0], null, false, true, null]],
        317 => [[['_route' => 'securitySendConfirmationEmail', '_controller' => 'App\\Controller\\SecurityController::sendConfirmationEmail'], ['email'], null, null, false, true, null]],
        349 => [
            [['_route' => 'securityActivationUser', '_controller' => 'App\\Controller\\SecurityController::activationUser'], ['email'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
