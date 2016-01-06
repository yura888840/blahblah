<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 12.11.15
 * Time: 13:09
 */

namespace Crm\Helpers;

use \Crm\Models\MailThreads;
use \Crm\Models\Email;

class EmailsHelper
{
    public static function emailStub()
    {
        $emailRFCIds = $preparedParents = [];

        $parentEmails = Email::find([['inReplyTo' => NULL]]);

        foreach ($parentEmails as $email) {
            $emailRFCIds[] = $email->messageId;
            $preparedParents[$email->messageId] = $email;
        }

        // Сюда заполняем е- мейлы последовательно
        // parent
        // .. child
        // .. child
        //  ...
        // parent
        // ...
        $emailSeries = [];

        // Наложить условия ...

        foreach ($emailRFCIds as $emailId) {
            $emailSeries[] = $preparedParents[$emailId];

            // BAD value of emailId
            if (!$emailId) {
                continue;
            }
            $children = Email::find([['references' => ['$regex' => $emailId]]]);
            if (!count($children)) continue;

            foreach ($children as $child) {
                $emailSeries[] = $child;
            }
        }

        return $emailSeries;
    }


    /**
     * @param $emails array of stdClass objects
     * @return array
     */
    public static function mapEmails($emails)
    {
        // Этот массив можно вынести куда- нить в DI
        $emailMapping = [
            '_id' => function ($in) {
                $out = $in->_id;
                return $out;
            },
            'subject' => function ($in) {
                $out = $in->subject;
                return $out;
            },
            'priority' => function ($in) {
                $out = 5;
                return $out;
            },
            'type' => function ($in) {
                $out = 'Email';
                return $out;
            },
            'department' => function ($in) {
                $out = 'Common';
                return $out;
            },
            'status' => function ($in) {
                $out = 'New';
                return $out;
            },
            'created' => function ($in) {
                $out = strtotime($in->mailDate);
                // @todo timezones shift

                return $out;
            },
            'is_email' => function ($in) {
                return true;
            },
            'is_child' => function ($in) {
                return !empty($in->inReplyTo);
            }
        ];

        return self::mapper($emails, $emailMapping);
    }

    /**
     * @param $emails array of stdClass objects
     * @return array
     */
    public static function mapChildrenEmails($emails = [])
    {
        if (!$emails) return [];
        $emailMapping = [
            '_id' => function ($in) {
                $out = $in->_id;
                return $out;
            },
            'subject' => function ($in) {
                $out = $in->subject;
                return $out;
            },
            'priority' => function ($in) {
                $out = 5;
                return $out;
            },
            'type' => function ($in) {
                $out = 'Email';
                return $out;
            },
            'department' => function ($in) {
                $out = 'Common';
                return $out;
            },
            'status' => function ($in) {
                $out = 'New';
                return $out;
            },
            'created' => function ($in) {
                $out = strtotime($in->mailDate);
                // тут нужно учеть - сдвиг tz, втаймЗон

                return $out;
            },
            'is_email' => function ($in) {
                return true;
            },
            'is_child' => function ($in) {
                return !empty($in->inReplyTo);
            },
            'body' => function ($in) {
                return htmlspecialchars_decode($in->body);
            },
            'fromAddress' => function ($in) {
                return $in->fromAddress;
            },
            'toAddress' => function ($in) {
                return $in->toAddress;
            },
            'isInSent' => function ($in) {
                return $in->isInSent;
            },
        ];

        return self::mapper($emails, $emailMapping);
    }

    public static function mapper($emails, $mapping, $buildStdClass = true)
    {
        $mappedOutput = [];

        foreach ($emails as $email) {
            $mapped = $buildStdClass ? (new \stdClass) : [];
            foreach ($mapping as $k => $v) {
                if ($buildStdClass) {
                    $mapped->$k = $v($email);
                } else {
                    $mapped[$k] = $v($email);
                }
            }

            $mappedOutput[] = $mapped;
        }

        return $mappedOutput;
    }

    /**
     * Функция строит список всех возможныз thread- ов
     *   Сортировка - по наличию новых в цепи
     *    Возврат - корневые элементы, со ссылками на них
     * @param $sort
     * @return array|bool array of Emails
     */
    public static function getThreadsAction($sort = NULL)
    {
        $threads = MailThreads::find([[]]);
        // либо - isLocalParent == true

        $list = [];
        foreach ($threads as $v) {
            if ($r = self::getEmail($v->parentId)) $list[] = $r;
        }
        return $list;
    }

    /**
     * Функция строит thread, на основании 1- го - parent_id
     *  самые новые - идут с самого верха
     *  Доп. функционал. Если, в thread есть сообщение с маркером - Unread
     *    -, то при открытии thread- а, автоматом, все маркируется - readed
     *
     * @param $parentId
     * @return array|bool array of Emails
     */
    public static function getThread($parentId)
    {
        $thread = MailThreads::find([["parentId" => $parentId]]);

        if (!$thread) return false;
        $list = [];
        foreach ($thread[0]->childrens as $childEmailId) {
            if ($r = self::getEmail($childEmailId)) $list[] = $r;
        }
        return $list;
    }

    /**
     * @param $childEmailId
     * @return array|null
     */
    public static function getEmail($childEmailId)
    {
        $email = Email::find([['messageId' => $childEmailId]]);

        return ($email) ? $email[0]/*->toArray()*/ : null;
    }
}