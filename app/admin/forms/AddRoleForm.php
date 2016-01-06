<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 06.04.15
 * Time: 23:11
 */

namespace Crm\Admin\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Crm\Models\Profiles;


class AddRoleForm extends Form
{

    public function initialize()
    {
        $name = new Text('name', array( 'class' => 'form-control'));

        $name->setLabel('Name');

        $name->addValidators(array(
            new PresenceOf(array(
                'message' => 'The name is required'
            ))
        ));

        $this->add($name);

        $active = new Select('active', ['Y' => 'Y', 'N' => 'N'], array('class' => 'form-control'));

        $active->setLabel('Status of the role');

        $active->addValidators(array(
            new PresenceOf(array(
                'message' => 'The status is required'
            )),
        ));

        $this->add($active);

        $this->add(new Submit('Add', array(
            'class' => 'btn btn-success'
        )));
    }

    /**
     * Prints messages for a specific element
     */
    public function messages($name)
    {
        if($this->hasMessagesFor($name)) {
            foreach ($this->getMessagesFor($name) as $message) {
                $this->flash->error($message);
            }
        }
    }

}