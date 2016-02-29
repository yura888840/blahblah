<?php

namespace admin\Controllers;

class IndexController extends \App\MVC\Controller
{
    public function indexAction()
    {
        $this->setAdminEnvironment();
    }
    
}

