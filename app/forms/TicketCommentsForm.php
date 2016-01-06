<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 05.03.15
 * Time: 12:44
 */

namespace Crm\Forms;

use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Submit;
use Phalcon\Validation\Validator\PresenceOf;

class TicketCommentsForm extends CrmForm
{

    public function initialize()
    {
        $field = new TextArea('text', array('class' => 'form-control'));

        $field->setLabel('text');

        $field->addValidators(array(
            new PresenceOf(array(
                'message' => 'The text of comment is required'
            ))
        ));

        $this->add($field);
        
        
        
        $id = new Text('parent_id', array( 'class' => 'not-clean hidden', 'value' => $this->dispatcher->getParam(0)));

        $id->addValidators(array(
            new PresenceOf(array(               
            ))
        ));
        
        $this->add($id);

        $submit = new Submit('Add comment', array());

        $this->add($submit);
    }
}