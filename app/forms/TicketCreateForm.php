<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23.03.15
 * Time: 15:54
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


class TicketCreateForm extends CrmForm
{
    private $pullDowns = array(
        'priority' => array(
            'label' => 'Priority',
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

    public function initialize()
    {
        foreach($this->pullDowns as $name => $attrs)
        {
            $var = $name;
            $data = DataSets::getSetByName($var);

            $pullDown = new Select($name, $data ,array_merge($attrs['attr'], array(
                'class' => 'form-control'
            )));

            $pullDown->setLabel($attrs['label']);

            $pullDown->addValidators(array(
                new InclusionIn(array(
                        'message' => ucfirst($name) . ' must be in list : ' . implode(',', $data),
                        'domain' => array_keys($data),
                        'allowEmpty' => false
                    )
                )
            ));

            $this->add($pullDown);
        }

        $users = Users::find(array());
        $usrs = array();
        foreach($users as $k => $v)
        {
            $vv = $v->toArray();
            $usrs[$vv['email']] = $vv['email'];
        }


        $field = new Text('subject',array_merge( array(
                    'class' => 'form-control'
                ), ( $this->isEntitySet ? array('value' => $this->entity->subject) : array()  )
            )
        );

        $field->setLabel('Subject');

        $field->addValidators(array(
            new PresenceOf(array(
                'message' => 'The subject is required'
            ))
        ));

        $this->add($field);

        $field = new Text('pageUrl', array_merge( array(
                    'class' => 'form-control'
                ), ( $this->isEntitySet ? array('value' => $this->entity->pageUrl) : array()  )
            )
        );

        $field->setLabel('Page URL (optional)');

        $field->addValidators(array(
            new UrlValidator(array(
                'message' => 'The url is not valid',
                'allowEmpty' => true,
            ))
        ));

        $this->add($field);

        $field = new TextArea('description',array_merge( array(
                    'class' => 'form-control'
                ), ( $this->isEntitySet ? array('value' => $this->entity->description) : array()  )
            )
        );

        $field->setLabel('Description');

        $field->addValidators(array(
            new PresenceOf(array(
                'message' => 'Description is missing'
            ))
        ));

        $this->add($field);

        $usrs = array();

        array_walk($users, function($usr) use (&$usrs) {
            $v = $usr->toArray();
            $usrs[strval($v['_id'])] = $v['name'];
        });

        $select = new Select('notify[]', $usrs,array_merge( array(
                    'useEmpty' => true,
                    'class' => 'form-control',
                    'multiple' => 'multiple',
                ), ( $this->isEntitySet ? array('value' => $this->entity->notify) : array()  )
            )
        );

        if($this->isEntitySet) $select->setDefault($this->entity->notify);

        $select->setLabel('Notify users');

        $this->add($select);

        $field = new Select('name', array_merge($usrs, array(
            'value' => ( $this->isEntitySet ? $this->entity->name : "" ), //@todo select me
        )), array(
            'class' => 'form-control'
        ));

        $field->setLabel('Assign to :');

        $this->add($field);

        $this->add(new Submit('Create', array(
            'class' => 'btn btn-primary'
        )));

    }
} 