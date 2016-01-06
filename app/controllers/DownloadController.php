<?php

use Crm\Helpers\DateTimeFormatter;
use Phalcon\Tag;
use Phalcon\Filter;

class DownloadController extends ControllerBase
{

    public function initialize()
    {
                 
    }

    public function indexAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if (!$this->request->isGet()) {
            exit();
        }

        $param = $this->dispatcher->getParam(0);
        if (strrpos($param, '.')) {
            $param = substr($param, 0, strrpos($param, '.'));
        }

        $params = [
            'hash' => $param,
            ////'hash' => $request->getQuery('h'),
            //'ticket_id' => $request->getQuery('ticket_id'),
            //'linkedto' => $request->getQuery('linkedto')
        ];

        $file = \Crm\Helpers\AttachHelper::getRealAttachmentname($params);

        $file['uniqName'] = \Phalcon\DI::getDefault()->get('config')->tickets['attachmentsDir'] . '/' . $file['uniqName'];

        if (ob_get_level()) {
            ob_end_clean();
        }

        if (!file_exists($file['uniqName']) || !($fd = fopen($file['uniqName'], 'rb'))) {
            exit();
        }

        $info = fstat($fd);

        //header('Content-Description: File Transfer');
        //header('Content-Type: application/octet-stream');
        header('Content-Type: image/jpeg');
        //header('Content-Disposition: attachment; filename=' . basename($file['originalName']));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $info['size']);

        while (!feof($fd)) {
            print fread($fd, 1024);
        }

        fclose($fd);
    }
    
}
