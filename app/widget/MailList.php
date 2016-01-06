<?php

namespace Crm\Widget;


use Crm\Models;

class MailList extends \Phalcon\Mvc\Controller implements \Crm\Widget\IWidget, \Crm\Widget\IInstantiable
{
    public function run($param = array())
    {
        $queryArr = array(
        array(

            ),
            "sort" => array("uid" => -1),
            "limit" => 5,
        );
        $mails = \Crm\Models\Mail::find($queryArr);
        if ($mails == array()) {
            $stringContent = '';
        } else {
            $mailList = array();
            $row = 0;
            foreach ($mails as $mail) {
                $row = $row+1;
                $mailOne = array();
                $mailOne['email'] = $mail->email;
                $mailOne['from_name'] = $mail->from_name;
                $mailOne['from_email'] = $mail->from_email;
                $mailOne['to_email'] = $mail->to_email;

                $mailOne['subject'] = mb_convert_encoding($mail->subject, 'UTF-8');
                $mailOne['subject'] = $mail->subject;
                $mailOne['id'] = $mail->getId()->{'$id'};
                $mailOne['row'] = $row;
                $mailOne['date'] = \Crm\Helpers\DateTimeFormatter::format ($mail->date)['created'];
                $mailList[] = $mailOne;
            }
            $params = array(
                'mails' => $mailList,
            );
            $stringContent=$this->simple_view->render('widget/mailList',$params);
        }

        return $stringContent;
    }

    public function install()
    {
        $return = array(
            'description' => 'Mail List',
            'widgetFactoryType' => 'MailList',
            'paramsWidget' => array(
                'typeWidget' => 'Profitability',
                'typeChart' => 'discreteBarChart',
                'idDiv' => 'chart2'
            ),

            'jsList' => array(// put your values here
            ),
            'css' => array(// put your values here
            ),
            'template' => 'chart',
            'idDiv' => 'chart2',
            'typeChart' => '',

        );

        return $return;
    }
}