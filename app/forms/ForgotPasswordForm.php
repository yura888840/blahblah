<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23.02.15
 * Time: 11:42
 */

namespace Crm\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email as EmailValidator;

class ForgotPasswordForm extends CrmForm
{

    public function initialize()
    {
        $email = new Email('email', array(
            'placeholder' => 'Email',
            'name' => 'email',
            'class' => 'form-control',
        ));

        $email->addValidators(array(
            new PresenceOf(array(
                'message' => 'The e-mail is required'
            )),
            new EmailValidator(array(
                'message' => 'The e-mail is not valid'
            ))
        ));

        $this->add($email);

        $this->add(new Submit('Send', array(
            'value' => 'Remind me'
        )));
    }
} 