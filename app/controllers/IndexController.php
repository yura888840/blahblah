<?php

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);

        return $this->response->redirect('dashboard');
    }
    
    public function route404Action()
    {

    }

}

