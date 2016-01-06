<?php
/**
 * Created by PhpStorm.
 * User: Kostja
 * Date: 06.08.15
 * Time: 15:50
 */

class SseController extends ControllerBase
{

    public function indexAction()
    {
        $this->view->disable();
        header("Content-Type: text/event-stream\n\n");
        header('Cache-Control: no-cache');
        $sse = new \Crm\Services\Sse();
        $sse->dispatcher();
    }

}