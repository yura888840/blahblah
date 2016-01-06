<?php
namespace Crm\Helpers;

use Crm\Models\Tickets;

class AjaxAdd {

    public static function ajaxSave($model, $form, $request){

        $dataForm = $request->getPost();
        if($request->hasPost('form')){
            parse_str($request->getPost('form'), $dataForm);
        }

        if ($form->isValid($dataForm)) {

            if (array_key_exists('parent_name', $dataForm) && 'tickets' == $dataForm['parent_name']) {
                //check if ticket from e-mail
                $ticket = Tickets::findById($dataForm['parent_id']);

                if ($ticket && $ticket->isFromEmail && !empty($ticket->fromEmail)) {
                    //send e-mail to recepient from - ticket department

                    //@todo - toName
                    $userEmail = $ticket->fromEmail;
                    $subj = $ticket->subject;
                    $body = $dataForm['text'];

                    \Phalcon\DI::getDefault()->getMail()->sendFromAddress(array(
                            $userEmail => $userEmail)

                        , $subj, 'response2ticket', array(
                            'body' => $body,
                        ),

                        $ticket->department);

                    $notifiers = [];
                    foreach ($ticket->notify as $userId) {
                        $usr = UsersWrapper::findActiveById($userId);

                        if (!empty($usr)) {
                            $notifiers[] = $usr->email;
                        }
                    }

                    if ($notifiers) {
                        $event = new \Crm\Events\TicketNewComments();

                        $event->emailBody = $body;
                        $event->text = 'New comment on ticket ' . $ticketUrl;

                        \Crm\Helpers\MailWrapper::batchSend($notifiers, $event);

                    }

                }
            }

            $vars = array_keys(get_object_vars($model));
            foreach($vars as $var){
                if($var != '_id'){
                    if (isset($dataForm[$var])) {
                        //@todo fix hardcode
                        if ("profile" == $var) {

                            if ($model->$var != $dataForm[$var]) {
                                $mongo = \Phalcon\DI::getDefault()->get('mongo');
                                $collection = $mongo->widgets_custom_set_users;

                                $collection->remove(['user' => $model->email]);
                                $mongo->widgets_custom_grid_users->remove(['user' => $model->email]);


                                $model->$var = $dataForm[$var];
                            }
                        } elseif ("" == $var) {

                        } elseif ("password" != $var) {
                            $model->$var = $dataForm[$var];
                        }
                    }
                }
            }

            return false;
        } else {

            $messagesArr = [];
            foreach ($form->getMessages(true) as $attribute => $messages) {
                $msgString = '';
                foreach ($messages as $message) {
                    $msgString = $msgString.$message.'. ';
                }
                $messagesArr[$attribute] = $msgString;
            }
            echo json_encode($messagesArr);
            return true;
        }

    }


}