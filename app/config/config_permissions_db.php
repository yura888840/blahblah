<?php

$configPermissions = new \Phalcon\Config(array(
    'permissions' => array(
        'setDefaultDENY' => false,
        'resources' => array(
            'public' => array(
                'widgets' => array('create'),
            ),
            'private' => array(
                'users' => array('update', 'create'),
                'tickets' => array('update', 'create'),
                'tickets-assignTo' => array('update', 'create'),
            ),
        ),
        'allow' => array(
            array('admin', 'users', 'create'),
            array('admin', 'users', 'update'),
            array('admin', 'tickets', 'create'),
            array('admin', 'tickets', 'update'),
//            array('admin', 'tickets-assignTo', 'create'),
//            array('admin', 'tickets-assignTo', 'update'),
        ),
        'deny' => array(
            array('user', 'users', 'create'),
            array('user', 'users', 'update'),
//            array('admin', 'tickets-assignTo', 'update'),
        ),
    ),
));

return $configPermissions;