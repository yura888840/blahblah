<?php

namespace Crm\Helpers;

use Phalcon\Validation\Validator\InclusionIn;
use Crm\Models\Tickets;

class AttachHelper
{

    CONST basepath = './files';

    CONST DS = '/';

    /**
     * FileSystem methods
     */
    public static function targetFolderPath($parentName, $elementID){
        return './files/'.$parentName.'/'.$elementID.'/main';
    }

    /**
     * @param $file
     * @return bool
     *
     * was - fileTypeValidation
     */
    public static function isfileTypeValid($file)
    {
        $validator = new \Phalcon\Validation();

        $validTypes = ['application/pdf',
            'image/jpeg',
            'image/png',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ];

        $validator->add('type', new InclusionIn(array(
            'message' => 'The type is not valid',
                'domain' => $validTypes,))
        );

        $messages = $validator->validate($file);
        if (!$messages->count()) {
            return true;
        }

        return false;
    }

    public static function getRealAttachmentname($params)
    {
        /*if (!self::checkIsValidAttachmentParams($params)) {
            return false;
        }*/

        /*$ticket = Tickets::find([[
                '_id' => new \MongoId($params['ticket_id']),

            ]]
        );

        if (!$ticket) {
            return false;
        }*/

        /* $ticket = $ticket[0];*/

        $rawAttachment = self::findAttachmentInTicket($params['hash']);

        //$isFound = false;

        /*
        if ($params['linkedto'] == 'ticket') {
            if (!$ticket->attach || !is_array($ticket->attach)) return false;

            foreach ($ticket->attach as $attachment) {
                if ($isFound) break;

                if (!array_key_exists('hash', $attachment)) continue;

                if ($attachment['hash'] == $params['hash']) {
                    $isFound = true;
                    $rawAttachment = $attachment;
                }
            }

        } elseif ($params['linkedto'] == 'comment') {

            $comments = \Crm\Models\Comments::find([[
                    'parent_id' => $params['ticket_id'],
                ]]
            );

            if (!$comments) {
                return false;
            }

            foreach ($comments as $comment) {
                if ($isFound) break;

                if (!$comment->attach) continue;

                foreach ($comment->attach as $attachment) {
                    if ($isFound) break;

                    if (!array_key_exists('hash', $attachment)) continue;

                    if ($attachment['hash'] == $params['hash']) {
                        $isFound = true;
                        $rawAttachment = $attachment;
                    }
                }
            }

        } else {
            return false;
        }
        */

        if (!$rawAttachment) return false;

        // mapping
        $fn = [
            "originalName" => $rawAttachment["originalName"],
            "uniqName" => $rawAttachment["uniqName"],
            "type" => $rawAttachment["type"],
            "size" => $rawAttachment["size"],
            "created" => $rawAttachment["created"],
        ];

        return $fn;
    }

    private static function findAttachmentInTicket($hash)
    {

        $attachmentFound = false;
        $tickets = Tickets::find();

        foreach ($tickets as $ticket) {
            if (!$ticket->attach || !is_array($ticket->attach)) {
                continue;
            }

            foreach ($ticket->attach as $attachment) {
                // hack
                if (!array_key_exists('hash', $attachment)) {
                    continue;
                }

                if ($attachment['hash'] == $hash) {
                    $attachmentFound = $attachment;;
                    break;
                }

            }

        }

        return $attachmentFound;
    }

    private static function checkIsValidAttachmentParams($params)
    {
        $inVarNames = ['hash', 'ticket_id', 'linkedto'];

        if (array_intersect(array_keys($params), $inVarNames) == $inVarNames)
            return true;

        return false;
    }

    /**
     * @param $params
     */
    public static function createTemporaryAttachment($params)
    {

        $msg = $success = $uid = false;
        $fileInfo = [];
        $downloadUrl = '';

        if (self::isfileTypeValid($_FILES['file'])) {
            $request = new \Phalcon\Http\Request();

            // @todo fix for multiple attachments
            $fileRequest = new \Phalcon\Http\Request\File($_FILES['file']);

            $fileInfo = \Crm\Helpers\FilesService::getInfo($fileRequest);


            $uid = $fileInfo['hash'];
            $ext = $fileInfo['ext'];
            \Crm\Helpers\FilesService::moveToTmp($fileInfo['uniqName'], $fileRequest);

            $success = true;

        } else {
            $msg = 'You can\'t upload file of this type';
        }

        // @todo fix this
        $res = [
            $uid,
            $fileInfo,
            $downloadUrl,
            $success,
            $msg,
            $ext
        ];

        return $res;
    }

    public static function finishUpload($hashes)
    {
        $r = [];


        return $r;
    }
}