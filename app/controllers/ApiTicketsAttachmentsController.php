<?php

/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 26.06.15
 * Time: 14:02
 */
class ApiTicketsAttachmentsController extends ControllerBase
{

    private $mapper = array(
        0 => 'type',
        1 => 'id',
    );

    private $valuesFilter = array(
        'type' => array('tickets', 'comments'),
    );

    public function startUploadAction()
    {
        $this->view->disable();

        $request = new \Phalcon\Http\Request();

        $success = $uid = $info = $msg = $link = false;

        if ($request->isPost() && $request->isAjax() && $request->hasFiles()) {

            $params = $this->dispatcher->getParams(0);

            if (empty($params)) {
                throw new \Exception('Invalid params');
            }

            $preparedParams = [];
            foreach ($params as $k => $v) {
                if (!array_key_exists($k, $this->mapper)) {
                    continue;
                }

                if (array_key_exists($this->mapper[$k], $this->valuesFilter)) {
                    if (gettype($this->valuesFilter[$this->mapper[$k]]) == 'array') {
                        if (!in_array($v, $this->valuesFilter[$this->mapper[$k]]))
                            continue;
                    }
                }

                if ($this->mapper[$k] == 'id' && empty($preparedParams)) {
                    continue;
                }
                $preparedParams[$k] = $v;

            }

            if (empty($preparedParams)) {
                $msg = 'Cann\'t initialize upload. Invalid params';
            } else {

                $data = \Crm\Helpers\AttachHelper::createTemporaryAttachment($preparedParams);

                list($uid, $file, $link, $success, $msg, $ext) = $data;

                $sessionFileData = ['uid' => $uid,
                    'file' => $file,
                    'link' => $link,
                    'suid' => $params,
                    'ext' => $ext,
                    'originalName' => $file['originalName'],
                    'type' => $file['type'],
                    'size' => $file['size'],
                    'created' => $file['created']
                ];

                $this->session->set('uploads-' . $uid, $sessionFileData);
            }

        } else {
            $msg = 'This and- point accepts only POST AJAX requests with file uploading';
        }

        //header('Content-type: application/json');

        echo json_encode(['success' => $success, 'msg' => $msg, 'uid' => $uid, 'suid' => $params, 'original' => $file['originalName']]);
    }

    public function finishUploadAction()
    {
        $this->view->disable();

        $success = $msg = false;

        if ($this->request->isPost() && $this->request->isAjax() && !empty($this->dispatcher->getParam(0))) {

            $hashes = $this->dispatcher->getParam(0);

            $items = \Crm\Helpers\AttachHelper::finishUpload($hashes);

            $success = true;
        } else {
            $msg = 'This and- point accepts only POST AJAX requests with uid params';
        }

        header('Content-type: application/json');

        echo json_encode(['success' => $success, 'msg' => $msg, 'items' => $items]);

        //@todo Подумать, а надо ли - перезагрузка списка объекта (- ов ) ?
    }

    public function removeTemporaryAction()
    {

        $this->view->disable();

        //removing attach with uid
    }

    public function getAttachmentsAction()
    {
        $this->view->disable();

        $id = $this->dispatcher->getParam(0);
        //@tiodo sanitize

        $ticket = \Crm\Models\Tickets::findById($id);

        if (!$ticket) {
            throw new \Exception('Invalid ticket ID or ticket has been removed');
        }

        // вынести отдельно
        // должно быть $attachments = Helpers\AttachmentsServices::prepareAttachments();
        if (is_array($ticket->attach) && !empty($ticket->attach)) {
            $haveBrokenAttachments = false;

            $targetFolder = './files/' . 'tickets' . '/' . $id . '/main';

            //@todo вставить сюда логгер если файл не найден
            foreach ($ticket->attach as $k => $v) {

                if (!is_file($targetFolder . '/' . $ticket->attach[$k]['uniqName'])) {
                    ///////unset($ticket->attach[$k]);
                    $haveBrokenAttachments = true;
                }
            }

//            if ($haveBrokenAttachments) {
//                if (!$ticket->save()) {
//                    throw new \Exception('Error while saving ticket data');
//                }
//            }

            header('Content-type: application/json');
            echo json_encode($ticket->attach);
        }

    }

}