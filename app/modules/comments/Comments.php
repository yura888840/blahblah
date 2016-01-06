<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 01.04.15
 * Time: 17:27
 */

namespace Crm\Modules\Comments;

class Comments extends \Phalcon\Acl\Resource
{
    public function addNewComment()
    {
        $this->view->disable();

        $authData = $this->auth->getIdentity();

        $form = new TicketCommentsForm();

        $request = new \Phalcon\Http\Request();

        if($request->isPost() == true && $request->isAjax() == true){
            if($form->isValid($request->getPost()) != false){

                $comments = new Comments();
                $comments->text = $this->request->getPost('text', 'striptags');
                $comments->user_name = $authData['name'];
                $comments->user_id = $authData['id'];
                $comments->ticket_id = $this->request->getPost('id', 'striptags');


                if ($comments->save()) {
                    $messagesArr['success'] = true;
                    echo json_encode($messagesArr);
                }
            }
            else{
                $messagesArr = [];
                foreach ($form->getMessages(true) as $attribute => $messages) {
                    $msgString = '';
                    foreach ($messages as $message) {
                        $msgString = $msgString.$message.'. ';
                    }
                    $messagesArr[$attribute] = '<p class="text-danger" id="'.$attribute.'">'.$msgString.'</p>';
                }
                echo json_encode($messagesArr);
            }
        }
    }

    public function reloadComments(){
        $this->view->disable();
        $request = new \Phalcon\Http\Request();
        if($request->isPost() == true && $request->isAjax() == true){
            $ticketID = $request->getPost('ticket_id');
            $comments = Comments::Find(
                array(
                    array("ticket_id" => $ticketID),
                    "sort" => array("created" => -1)
                )
            );
            foreach($comments as $k => $comment){
                $comments[$k] = $comment->toArray();
                $comments[$k] = array_merge($comments[$k], DateTimeFormatter::format($comments[$k]['created']));
            }
            echo json_encode($comments);
        }
    }

}