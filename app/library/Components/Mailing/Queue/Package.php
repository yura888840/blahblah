<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 28.08.15
 * Time: 18:00
 */

namespace Crm\Components\Mailing\Queue;


class Package
{
    /**
     * Entity data. Package structure described earlier.
     * @var array
     */
    private $entityData;

    /**
     * Entity object
     * @var object
     */
    public $entity;

    private $packageSchema = [
        '_id',
        'objectCls',
        'items',
        'additionalInfo',
        'watchers_list',
    ];

    public function __construct($data = NULL)
    {
        if ($data) {
            $this->setData($data);
        }
    }

    public function setData($entityData = NULL)
    {
        if ($entityData) {
            $this->entityData = $entityData;

            $this->parseData();
        }
    }


    private $changes;

    private $watchers;

    public $objectClass;

    private $_id;

    private function parseData()
    {
        foreach ($this->entityData as $k => $v) {
            if (!in_array($k, $this->packageSchema))
                continue;

            if (is_array($v)) {
                $arr = [];
                foreach ($v as $k1 => $v1) {
                    // preparation
                    $arr[$k1] = $v1;
                }

                if ("items" == $k) {
                    $this->changes = $arr;
                } elseif ("watchers_list" == $k) {
                    $this->watchers = $arr;
                }
            } else {
                if ("objectCls" == $k) {
                    $this->objectClass = ucfirst($v);
                } elseif ("_id" == $k) {
                    $this->_id = $v;
                }
            }
        }

        $this->entity = call_user_func("\Crm\Models\\$this->objectClass::findById", $this->_id);
    }

    //@todo - getEntityName
    public function getEntity()
    {
        $entity = !empty($this->objectClass) ? $this->objectClass : NULL;

        return $entity;
    }

    public function getRecepients()
    {
        $recepients = $this->watchers;
        array_walk($recepients, function (&$v) {
            $v = strip_tags($v);
        });
        $recepients = array_values($recepients);

        return $recepients;
    }

    public function getChanges()
    {
        $arr = [];

        foreach ($this->changes as $k => $v) {
            $v['element'] = $k;
            array_walk($v, function (&$v) {
                if (is_array($v)) $v = implode(',', $v);
            });

            $arr[] = $v;
        }

        return $arr;
    }

}