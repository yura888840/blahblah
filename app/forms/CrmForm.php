<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23.03.15
 * Time: 15:59
 */

namespace Crm\Forms;


class CrmForm extends \Phalcon\Forms\Form
{
    use FormBaseTrait;

    protected $entity;

    protected $isEntitySet = false;

    protected $elementsSet = NULL;

    const VALIDATORS_NAMESPACE = '\Phalcon\Validation\Validator\\';
    const ELEMENTS_NAMESPACE = '\Phalcon\Forms\Element\\';

    public function __construct($entity = NULL, $elementsSet = NULL)
    {
        if($entity)
        {
            $this->setEntity($entity);
        }

        if ($elementsSet) {
            $this->elementsSet = $elementsSet;
        }

        parent::__construct();
    }

    public function setEntity($entity)
    {
        if(is_array($entity))
        {
            $this->entity = $entity[0];
        } else {
            $this->entity = $entity;
        }

        $this->isEntitySet = true;

        $this->initialize();
    }

} 