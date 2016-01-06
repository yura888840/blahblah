<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 26.02.15
 * Time: 14:56
 */

namespace Crm\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Url as UrlValidator;
use Crm\Models\Users;
use Crm\Helpers\DataSets;


class TicketEditForm extends CrmForm
{
    private $pullDowns = array(
        'priority' => array(
            'label' => 'Priority',
            'attr' => array(),
        ),

        'status' => array(
            'label' => 'Status',
            'attr' => array(),
        ),

        'type' => array(
            'label' => 'Type',
            'attr' => array(),
        ),

        'department' => array(
            'label' => 'Department',
            'attr' => array(),
        ),
    );

    private $texts = array(
        'subject' => array(
            'label' => 'Subject',

        )
    );

    private $ticket;

    public function __construct($entity = NULL)
    {
        if($entity)
        {
            $this->ticket = $entity;
        }

        parent::__construct();
    }

    public function initialize()
    {
        foreach($this->pullDowns as $name => $attrs)
        {
            $var = $name . 'List';
            $data = DataSets::getValuesBy($name);

            $pullDown = new Select($name, $data ,array_merge($attrs['attr'], array(
                'value' => $this->ticket->$name,
                'class' => 'form-control'
            )));

            $pullDown->setLabel($attrs['label']);

            $pullDown->addValidators(array(
                new InclusionIn(array(
                    'message' => ucfirst($name) . ' must be in list : ' . implode(',', $data),
                    'domain' => array_keys($data),
                    'allowEmpty' => false
                ))
            ));

            $this->add($pullDown);
        }

        $field = new Text('subject',array(
            'value' => $this->ticket->subject,
            'class' => 'form-control'
        ));

        $field->setLabel('Subject');

        $field->addValidators(array(
            new PresenceOf(array(
                    'message' => 'The subject is required'
            ))
        ));

        $this->add($field);


        $field = new Text('pageUrl', array(
            'value' => $this->ticket->pageUrl,
            'class' => 'form-control'
        ));

        $field->setLabel('Page URL');

        $field->addValidators(array(
            new UrlValidator(array(
                'message' => 'The url is not valid',
                'allowEmpty' => true,
            ))
        ));

        $this->add($field);


        $field = new TextArea('description',array(
            'value' => $this->ticket->description,
            'class' => 'form-control'
        ));

        $field->setLabel('Description');

        $field->addValidators(array(
            new PresenceOf(array(
                'message' => 'Description is missing'
            ))
        ));

        $this->add($field);

        $users = Users::find(array());
        $usrs = array();
        foreach($users as $k => $v)
        {
            $vv = $v->toArray();
            $usrs[$vv['email']] = $vv['email'];
        }
        $field = new Select('name', array_merge($usrs, array(
            'value' => $this->ticket->created
        )), array(
            'class' => 'form-control'
        ));

        $field->setLabel('Created by');

        $this->add($field);

        $usrs = array();

        array_walk($users, function($usr) use (&$usrs) {
            $v = $usr->toArray();
            $usrs[strval($v['_id'])] = $v['name'];
        });

        $select = new Select('notify[]', $usrs, array(
            'multiple' => 'multiple',
            'class' => 'form-control',
        ));

        $select->setLabel('Watchers');

        $this->add($select);


        $field = new Select('assignTo', array_merge($usrs, array(
            'value' => ( $this->isEntitySet ? $this->entity->name : "" ), //@todo select me
        )), array(
            'class' => 'form-control'
        ));

        $field->setLabel('Assign to :');

        $this->add($field);

        $field = new Text('deadline', array(
            'value' => $this->ticket->deadline,
            'class' => 'form-control'
        ));

        $field->setLabel('Deadline');


        $this->add($field);


        $this->add(new Hidden('id', array('value' => $this->ticket->_id)));


        $this->add(new Submit('Create', array(
            'class' => 'btn btn-primary'
        )));

        $this->add(new Submit('Update', array(
            'class' => 'btn btn-primary'
        )));

    }

    public function getElementOptions($element){
        return $this->get($element)->getOptions();
    }
        
    public function setElementDefaultOptions($element, $arrDefOptions){
        return $this->get($element)->setDefault($arrDefOptions);
    }
} 