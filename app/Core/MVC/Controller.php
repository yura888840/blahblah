<?php

/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 19.01.16
 * Time: 16:15
 */

namespace App\MVC;

class Controller extends \Phalcon\Mvc\Controller
{
    public function redirect($url, $code = 302)
    {
        switch ($code) {
            case 301:
                header('HTTP/1.1 301 Moved Permanently');
                break;
            case 302:
                header('HTTP/1.1 302 Moved Temporarily');
                break;
        }
        header('Location: ' . $url);
        $this->response->send();
    }

    public function returnJSON($response)
    {
        $this->view->disable();

        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setContent(json_encode($response));
        $this->response->send();
    }

    public function flashErrors($model)
    {
        foreach ($model->getMessages() as $message) {
            $this->flash->error($message);
        }
    }

    public function setAdminEnvironment()
    {
        $this->view->setMainView(MAIN_VIEW_PATH . 'admin');
        $this->view->setLayout(null);
    }

}