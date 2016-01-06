<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 02.07.15
 * Time: 17:35
 */

namespace Crm\Helpers;

use Phalcon\Validation\Validator\InclusionIn;

class AttachHelperOld
{

    ////////////////////////////////////////*************************

    public static function renameSavedFiles($model, $parentName)
    {

        $modelID = $model->_id;

        $tmp = \Phalcon\DI::getDefault()->get('config')->uploadFiles['tmp'];

        $targetFolder = self::targetFolderPath($parentName, $modelID);

        self::createFolder($targetFolder);

        foreach ($model->attach as $k => $v) {
            if (isset($v["uniqName"])) {
                rename($tmp . '/' . $v["uniqName"], $targetFolder . '/' . $v["uniqName"]);
            }
        }
    }


    public static function attachInfo($fileRequest)
    {

        $fileName = $fileRequest->getName();

        $arr = explode('.', $fileName);

        $ext = array_pop($arr);

        $uniqName = sha1(time() . $fileName) . '.' . $ext;

        $fileInfo = [
            'originalName' => $fileName,
            'uniqName' => $uniqName,
            'type' => $fileRequest->getRealType(),
            'size' => $fileRequest->getSize(),
            'created' => time(),
        ];

        return $fileInfo;
    }

    public static function ajaxAttachInfo($request)
    {

        $attach = [];

        foreach ($request->getPost('attach') as $k => $v) {

            if (!isset($v["uniqName"])) {
                continue;
            }

            $attach[] = $v;
        }

        return $attach;
    }


    public static function  saveAttachDropzone(\Phalcon\Mvc\Collection $modelData, $p = NULL)
    {

        $request = new \Phalcon\Http\Request();

        if (!array_key_exists('file', $_FILES)) {
            return false;
        }

        $fileRequest = new \Phalcon\Http\Request\File($_FILES['file']);

        if ($request->isPost() == true && $request->isAjax() == true && $fileRequest->isUploadedFile() == true) {

            $isInvalid = AttachHelper::fileTypeValidation($_FILES['file']);

            if (!$isInvalid) {
                //valid
                $modelID = $p ? $p : $request->getPost('parent_id');
                $parentName = CommonHelper::getShortClassName($modelData);


                $model = $modelData::findById($modelID);
                $model->created = null;
                $fileInfo = AttachHelper::attachInfo($fileRequest);
                $model->attach[] = $fileInfo;

                $targetFolder = AttachHelper::targetFolderPath($parentName, $modelID);
                AttachHelper::createFolder($targetFolder);
                $fileRequest->moveTo($targetFolder . '/' . $fileInfo['uniqName']);

                if ($model->save()) {

                    $messagesArr['success'] = true;
                    $messagesArr['link'] = $targetFolder . '/' . $fileInfo['uniqName'];
                    echo json_encode($messagesArr);
                }

            } else {
                return false;
            }
        } else {
            return 'This and- point accepts only POST AJAX requests with file uploading';
        }

    }


    public static function reloadExistAttachDropzone(\Phalcon\Mvc\Collection $modelData)
    {

        $request = new \Phalcon\Http\Request();

        if ($request->isPost() == true && $request->isAjax() == true) {

            $modelID = $request->getPost('parent_id');

            $parentName = CommonHelper::getShortClassName($modelData);

            $model = $modelData::findById($modelID);

            $targetFolder = AttachHelper::targetFolderPath($parentName, $modelID);


            if (is_array($model->attach)) {
                $noExist = 0;

                //@todo вставить сюда логгер если файл не найден
                foreach ($model->attach as $k => $v) {

                    if (!is_file($targetFolder . '/' . $model->attach[$k]['uniqName'])) {
                        ///////unset($ticket -> attach[$k]);
                        $noExist++;
                    }
                }
                if ($noExist > 0) {
                    if ($model->save()) {

                    }
                }
            }

            echo json_encode($model->attach);
        }
    }

    ///////////////////////////////////////////////


    ////////////////// .*** isolated

    public function removeOneAttachDropzone(\Phalcon\Mvc\Collection $modelData)
    {

        $request = new \Phalcon\Http\Request();

        if ($request->isPost() == true && $request->isAjax() == true) {

            $uniqName = $request->getPost('uniqName');
            $modelID = $request->getPost('parent_id');
            $parentName = CommonHelper::getShortClassName($modelData);
            $model = $modelData::findById($modelID);
            $targetFolder = AttachHelper::targetFolderPath($parentName, $modelID);


            $noExist = 0;

            foreach ($model->attach as $k => $v) {
                if (!is_file($targetFolder . '/' . $model->attach[$k]['uniqName'])) {
                    ///unset($model -> attach[$k]);
                    $noExist++;
                } elseif ($v['uniqName'] == $uniqName) {
                    unlink($targetFolder . '/' . $model->attach[$k]['uniqName']);
                    unset($model->attach[$k]);
                    $noExist++;
                }
            }
            if ($noExist > 0) {
                if ($model->save()) {

                }
            }
        }
    }

    //////////////////////////////////////////////


}