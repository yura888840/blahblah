<?php
namespace Crm\Helpers;

use Crm\Models\Comments;
use Crm\Helpers\DateTimeFormatter;
use Crm\Models\Users;

class CommentsHelper{

    /**
     *
     * @param $parentId
     * @return array of \Crm\Models\Comments
     */
    public static function commentsView($parentId){
        $comments = Comments::Find(
            array(
                array("parent_id" => $parentId),
                "sort" => array("created" => -1)
            )
        );

        foreach($comments as $k => $comment){
            $comments[$k] = $comment->toArray();
            $comments[$k] = array_merge($comments[$k], DateTimeFormatter::format($comments[$k]['created']));

            $outsideTicketData = \Crm\Helpers\TicketsHelper::getExternalTicketAuthorData($comments[$k]['parent_id']);

            $comments[$k]['recepient_name'] = $outsideTicketData['name'];
            $comments[$k]['recepient_email'] = $outsideTicketData['email'];
            $userId = $comments[$k]['user_id'];
            $userAbbrev = '';

            if ($comment->isPrivate &&
                $userId != 'admin'
            ) {
                $user = Users::findById($userId);
                if ($user) {
                    $userFullname = $user->name;
                    $userAbbrev = '';

                    $unameData = explode('\s', $user->name);
                    foreach ($unameData as $v) {
                        $userAbbrev .= substr(ucfirst($v), 0, 1);
                    }

                    $comments[$k] = array_merge($comments[$k], ['userAbbrev' => $userAbbrev, 'userFullname' => $userFullname, 'userEmail' => $user->email]);
                }
            } else {
                $unameData = explode('\s', $comments[$k]['user_name']);
                foreach ($unameData as $v) {
                    $userAbbrev .= substr(ucfirst($v), 0, 1);
                }
                $comments[$k] = array_merge($comments[$k],
                    ['userAbbrev' => $userAbbrev,
                        'userFullname' => $comments[$k]['user_name'],
                        'userEmail' => $comments[$k]['user_name']
                    ]
                );
            }
        }

        return $comments;
    }

    public static function ajaxReloadAttach($comment){
        if($comment['attach']){

            // @todo - вынести в кусочек шаблона

            $string = '<h5><b>Attach:</b></h5><ul>';
            foreach($comment['attach'] as $attach){
                $string .= '<li><a class="comment-download-file" href="./files/comments/'. $comment['_id'] .'/main/'. $attach["uniqName"] .'">'.$attach["originalName"].'</a></li>';
            }
            $string .= '</ul>';
            $comment['attach'] =  $string;
        }
        else{
            $comment['attach'] = '';
        }
        return $comment['attach'];
    }

}