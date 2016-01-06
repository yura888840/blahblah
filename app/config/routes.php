<?php

/*
 * Routes APP
 */

$configRoutes = new \Phalcon\Config(array(


    'routes' => array(

        // Default module. THIS ROUTES MUST BE ON TOP
        '/:controller' => array(
            'params' => array(
                'controller' => 1,
                'action' => 'index'
            ),
        ),
        '/:controller/:action' => array(
            'params' => array(
                'controller' => 1,
                'action' => 2
            ),
        ),
        '/:controller/:action/:params' => array(
            'params' => array(
                'controller' => 1,
                'action' => 2,
                'params' => 3,
            ),
        ),
        '/email/:params' => array(
            'params' => array(
                'controller' => 'email',
                'params' => 1,
            ),
        ),
        '/' => array(
            'params' => array(
                'controller' => 'dashboard',
                'action'     => 'index',
            ),
            'name'   => 'dashboard-index',
        ),
        '/login' => array(
            'params' => array(
                'controller' => 'auth',
                'action' => 'login'
            ),
            'name' => 'user-login',
        ),
        '/logout' => array(
            'params' => array(
                'controller' => 'auth',
                'action' => 'logout'
            ),
            'name' => 'user-logout',
        ),
        '/forgotpassword' => array(
            'params' => array(
                'controller' => 'auth',
                'action' => 'forgotPassword'
            ),
            'name' => 'user-forgotpassword',
        ),
        '/passwordsent' => array(
            'params' => array(
                'controller' => 'auth',
                'action' => 'passwordSent'
            ),
            'name' => 'user-passwordsent',
        ),
        '/dashboard' => array(
            'params' => array(
                'controller' => 'dashboard',
                'action'     => 'index',
            ),
            'name'   => 'dashboard-index-loggedin',
        ),
        '/register' => array(
            'params' => array(
                'controller' => 'auth',
                'action'     => 'register',
            ),
            'name'   => 'auth-register',
        ),
        '/reset-password/{code}/{email}' => array(
            'params' => array(
                'controller' => 'user_control',
                'action' => 'resetPassword'
            )
        ),
        '/users/changepassword' => array(
            'params' => array(
                'controller' => 'users',
                'action' => 'changePassword'
            ),
            'name' => 'change-pass'
        ),
        '/tickets/create' => array(
            'params' => array(
                'controller' => 'tickets',
                'action' => 'create'
            )
        ),
        '/api/mail/:action' => array(
            'params' => array(
                'controller' => 'api_mail',
                'action' => 1,
            ),
        ),
        '/api/db/find' => array(
            'params' => array(
                'controller' => 'api',
                'action' => 'findDb',
            ),
        ),

        '/api/tickets/:action/:params' => array(
            'params' => array(
                'controller' => 'api_tickets',
                'action' => 1,
                'params' => 2,
            ),
        ),

        '/api/tickets/attachments/:action/:params' => array(
            'params' => array(
                'controller' => 'api_tickets_attachments',
                'action' => 1,
                'params' => 2,
            ),
        ),

        '/api/tickets/list/:action/:params' => array(
            'params' => array(
                'controller' => 'api_tickets_list',
                'action' => 1,
                'params' => 2,
            ),
        ),

        '/api/reply_templates/:action/:params' => array(
            'params' => array(
                'controller' => 'api_reply_templates',
                'action' => 1,
                'params' => 2,
            ),
        ),

        '/download/:params' => array(
            'params' => array(
                'controller' => 'download',
                'action' => 'index',
                'params' => 1,
            ),
        ),

        // Admin part. ANOTHER MONULES PUT DOWN !!!
        '/admin/:controller' => array(
            'params' => array(
                'module' => 'admin',
                'controller' => 1,
                'action' => 'index'
            ),
        ),
        '/admin/:controller/:action' => array(
            'params' => array(
                'module' => 'admin',
                'controller' => 1,
                'action' => 2
            ),
        ),
        '/admin/:controller/:action/:params' => array(
            'params' => array(
                'module' => 'admin',
                'controller' => 1,
                'action' => 2,
                'params' => 3,
            ),
        ),
        //ADMIN API
        '/admin/api/resources' => array(
            'params' => array(
                'module' => 'admin',
                'controller' => 'api_resources',
                'action' => 1,
                'params' => 2,
            ),
        ),
        '/admin/api/resources/:action/:params' => array(
            'params' => array(
                'module' => 'admin',
                'controller' => 'api_resources',
                'action' => 1,
                'params' => 2,
            ),
        ),
    )

));
$config->merge($configRoutes);
