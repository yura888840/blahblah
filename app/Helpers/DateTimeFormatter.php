<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 26.03.15
 * Time: 15:51
 */

namespace Crm\Helpers;


class DateTimeFormatter {

    //@todo прописать в конфигах форматы
    public static function format($value = 0)
    {
        $a = array();

        $dt =  is_object($value)
            ? $value->sec
            : $value;

        $a['created'] = date('Y-m-d H:i:s', $dt);
        $a['created_date'] = date('Y-m-d', $dt);
        $a['created_time'] = date('H:i:s', $dt);
        $a['created_date_time'] = date('Y-m-d H:i:s', $dt);
        $a['created_date_text'] = date('d M Y, g:i A', $dt);
        $a['created_date1_text'] = date('d M Y', $dt);

        return $a;
    }

    public static function deadlineFormat($value)
    {
        return new \MongoDate(strtotime($value));
    }

    public static function reverseDeadlineFormatter($value)
    {
        if (!($value instanceof \MongoDate)) {
            return false;
        }

        return date('m/d/Y', $value->sec);
    }

} 