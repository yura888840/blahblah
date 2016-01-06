<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 16:03
 */

namespace Crm\Admin\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\InclusionIn;
use Crm\Models\Role;
use Crm\Models\Profiles;

class AddUserForm extends Form {

    public function initialize($entity = null, $options = null)
    {                
        $name = new Text('name', array( 'class' => 'form-control'));

        $name->setLabel('Name');

        $name->addValidators(array(
            new PresenceOf(array(
                'message' => 'The name is required'
            ))
        ));

        $this->add($name);

        // Email
        $email = new Text('email', array('class' => 'form-control'));

        $email->setLabel('E-Mail Address (used as login for CRM system)');

        $email->addValidators(array(
            new PresenceOf(array(
                'message' => 'The e-mail is required'
            )),
            new Email(array(
                'message' => 'The e-mail is not valid'
            ))
        ));

        $this->add($email);
        
        $roles = Profiles::find();
        $rolesFormatted = $rolesList = array();
        foreach($roles as $role)
        {
            $rolesFormatted[$role->name] = $role->name;
        }

        $rolesList = array_keys($rolesFormatted);

        $role = new Select('profile', $rolesFormatted, array('class' => 'form-control'));
        
        $role -> setLabel('Role of the user');
        
        $role->addValidators(array(
            new PresenceOf(array(
                'message' => 'The role is required'
            )),
            new InclusionIn(array(
                'message' => 'Must be ' . implode(' or ', $rolesList),
                'domain' => array_keys($rolesList),
                'allowEmpty' => false
            ))
        ));
        
        $this->add($role);
        
        
        // Status
        $status = new Select('status', ['Active' => 'Active', 'Blocked' => 'Blocked'], array('class' => 'form-control'));
        
        $status -> setLabel('Status of the user');
        
        $status->addValidators(array(
            new PresenceOf(array(
                'message' => 'The status is required'
            )),            
        ));
        
        $this->add($status);
        
        
        // Password
        $password = new Password('password', array('class' => 'form-control'));

        $password->setLabel('Password');

        $password->addValidators(array(
            new PresenceOf(array(
                'message' => 'The password is required'
            )),
            new StringLength(array(
                'min' => 8,
                'messageMinimum' => 'Password is too short. Minimum 8 characters'
            )),
            new Confirmation(array(
                'message' => 'Password doesn\'t match confirmation',
                'with' => 'confirmPassword'
            ))
        ));

        $this->add($password);

        // Confirm Password
        $confirmPassword = new Password('confirmPassword', array('class' => 'form-control'));

        $confirmPassword->setLabel('Confirm Password');

        $confirmPassword->addValidators(array(
            new PresenceOf(array(
                'message' => 'The confirmation password is required'
            ))
        ));

        $this->add($confirmPassword);        

        // Save
        $this->add(new Submit('Save', array(
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

    public function getElementOptions($element){
        return $this->get($element)->getOptions();
    }

    public function setElementDefaultOptions($element, $arrDefOptions){
        return $this->get($element)->setDefault($arrDefOptions);
    }


} 