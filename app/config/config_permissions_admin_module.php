<?php

$configPermissions = new \Phalcon\Config(array(
    'permissions' => array(
        'setDefaultDENY' => false,
        'resources' => array(
            'public' => array(
                'auth' => array('view'),
            ),
            'private' => array(
                'permissions' => array('view', 'update'),
                'users' => array('view', 'update'),
            ),
        ),
        'allow' => array(
            array('admin', 'permissions', 'view'),
            array('admin', 'permissions', 'update'),
            array('admin', 'users', 'view'),
            array('admin', 'users', 'update'),
        ),
        'deny' => array(
//            array('guest', 'users', 'view'),
//            array('user', 'users', 'view'),
//            array('admin', 'users', 'view'),
//            array('admin', 'permissions', 'view'),
        ),
    ),
));

return $configPermissions;