<?php

namespace Crm\Helpers;

use Crm\Helpers\DateTimeFormatter;
use MongoRegex;

class AjaxTablesorter
{

    public $headers;

    public $total_rows;

    public $rows = [];

    const ALL_VALS = '-- All --';

    /**
     *
     * @param $sort
     * @param $dbSelectors
     * @return array
     */
    public function createDbSort($sort, $dbSelectors){

        if (!is_array($sort)) {
            return [];
        }

        $dbSort = [];

        foreach ($sort as $k => $v) {
            $dbSort[$dbSelectors[$k]] = $v ? 1 : -1;
        }

        return $dbSort;
    }

    public function createMongoDbFilters($filters, $dbSelectors)
    {

        $dbFilters = [];

        if (!$filters) {
            return [];
        }

        foreach ($filters as $k => $v) {

            if ($v == self::ALL_VALS) {
                continue;
            }

            if ($dbSelectors[$k] != 'created') {
                if ($dbSelectors[$k] == 'priority') {
                    $dbFilters[$dbSelectors[$k]] = (int)$v;
                    continue;
                }
                $dbFilters[$dbSelectors[$k]] = new MongoRegex("/" . $v . "/");
                continue;
            }

            //created
            if (strpos($v, '>=') !== false) {
                $dbFilters[$dbSelectors[$k]] = array(
                    '$gt' => new \MongoDate(substr($v, 2) / 1000)
                );
            } elseif (strpos($v, '<=') !== false) {
                $date2 = strtotime('+1 day ' . date('Y-m-d', substr($v, 2) / 1000));
                $dbFilters[$dbSelectors[$k]] = array(
                    '$lt' => new \MongoDate($date2)
                );
            } elseif (strpos($v, ' - ') !== false) {
                $vAr = explode(' - ', $v);
                $date2 = strtotime('+1 day ' . date('Y-m-d', $vAr[1] / 1000));
                $dbFilters[$dbSelectors[$k]] = array(
                    '$gt' => new \MongoDate($vAr[0] / 1000),
                    '$lt' => new \MongoDate($date2),
                );
            } else {
                $dbFilters[$dbSelectors[$k]] = array(
                    '$gt' => new \MongoDate(strtotime($v)),
                    '$lt' => new \MongoDate(strtotime('+1 day ' . $v)),
                );
            }
        }

        return $dbFilters;
    }

    public function tdStandartWrapper($dbData, $headers, $dbSelectors, $title, $created)
    {

        foreach ($dbData as $k => $v) {
            $row = [];

            foreach ($headers as $k2 => $v2) {

                if($v2 == '&nbsp'){
                    // 1-й чекбокс в таблице
                    $row['&nbsp'] = '<label><input class="tablesorter-checkbox" type="checkbox" name="item'
                        . $dbData[$k]->_id
                        . '"><span class="lbl"></span></label>';

                } elseif ($v2 == '&nbsp&nbsp') {

                    // для определенного типа таблиц
                    $row['&nbsp&nbsp'] = '<div class="table-crm-btn not-touch-me text-right text-nowrap"'
                        . ' tablesorter-td-data-name="'
                        . $dbSelectors[$k2] . '">'
                        . '<a class="btn btn-sm" href=""><i class="glyphicon glyphicon glyphicon-pencil"></i></a>'
                        . '<a class="btn btn-sm" href=""><i class="glyphicon glyphicon glyphicon-remove"></i></a>'
                        . '</div>';

                } elseif ($v2 == $title) {
                    $row[$v2] = '<span class="tablesorter-td-data" tablesorter-td-data-name="subject">' .
                        $dbData[$k]->$dbSelectors[$k2]
                        . '</span>';

                } elseif ($v2 == $created && isset($dbData[$k]->$dbSelectors[$k2])) {
                    // Дата в таблице
                    $row[$v2] = DateTimeFormatter::format($dbData[$k]->$dbSelectors[$k2]);
                    $row[$v2] = $row[$v2]['created_date1_text'];

                } elseif ($v2 == 'Status') {

                    $row[$v2] = '<span class="tablesorter-td-data" tablesorter-td-data-name="'
                        . $dbSelectors[$k2] . '">'
                        . substr(ucfirst($dbData[$k]->$dbSelectors[$k2]), 0, 1)
                        . '</span>';

                    // это колонка статуса. Да, и + еще бейдж
                    $row[$v2] = '<div class="relative">
                                    <span class="badge status-badge status-'
                        . $dbData[$k]->$dbSelectors[$k2]
                        . '" data-toggle="tooltip" data-placement="left"'
                        . ' tablesorter-td-data-name="Status"'
                        . ' title="' . $dbData[$k]->$dbSelectors[$k2] . '">'
                        . substr(ucfirst($dbData[$k]->$dbSelectors[$k2]), 0, 1)
                        . '</span></div>';

                } else {

                    $row[$v2] = '<span class="tablesorter-td-data" tablesorter-td-data-name="'
                        . $dbSelectors[$k2] . '">'
                        . $dbData[$k]->$dbSelectors[$k2]
                        . '</span>';
                }
            }

            $this-> rows[] = $row;
        }

        return $this-> rows;
    }

    public function tdEmailWrapper($emailData, $headers, $dbSelectors, $title, $created)
    {

        // dbSelector- s - фикс значение

        $this->rows = [];

        foreach ($dbData as $k => $v) {
            $row = [];
            foreach ($headers as $k2 => $v2) {
                //$row[$v2] = $emailData[$k];
            }
            $this->rows[] = $row;
        }

    }

    public static function ajaxTableSelectOptions($form, $selectArr)
    {
        $request = new \Phalcon\Http\Request();

        if($request->isPost() == true && $request->isAjax() == true) {

            $elOptions = [];

            foreach ($selectArr as $k => $v) {

                $optString = [self::ALL_VALS];
                foreach ($form->getElementOptions($v) as $k2 => $v2) {
                    $optString[] = $v2;
                }

                $elOptions[$k] = $optString;
            }

            echo json_encode($elOptions);
        }
    }

}