<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 26.03.15
 * Time: 14:11
 */

namespace Crm\Forms;

use Crm\Forms\TicketCreateForm;
use Crm\BinaryAccessRules;
use Crm\Acl;
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\Acl\Resource as AclResource;
use Phalcon\Acl\Role as AclRole;

class FormFactory
{
    private static $mappingMethodsToAclActions = array(
        'getForm' => 'show',
        'customizeForm' => 'canCustomize'
    );

    private static $di;

    public static function getForm($formName, $params = NULL, $ns = 'Crm\Forms\\')
    {
        self::$di = \Phalcon\DI::getDefault();

        $class = $ns . $formName;

        if(class_exists($class) && self::hasAccess($formName))
        {
            return new $class($params);
        }

        return NULL;
    }

    public static function customizeForm($formName)
    {

    }

    /**
     * @param $formName name of the form
     * @return bool
     */
    private static function hasAccess($formName)
    {
        return true;

        $idenity = \Phalcon\DI::getDefault()->get('auth')->getIdentity();
        $idenity = self::$di->get('auth')->getIdentity();

        if(empty($idenity))
        {
            self::loadDefaultPermissions();

            $role = 'guest';
        } else {
            $role = $idenity['profile'];

            $user = strval($idenity['profile']->id);
        }
        //checking permissions for identity
        $callers=debug_backtrace();
        $func = $callers[1]['function'];

        $action = self::$mappingMethodsToAclActions[$func];

        //@todo обработка наложений прав пользователя на группу

        $acl = new \Crm\Acl\Acl();
        $acl = $acl->getAcl();

        $allowedGroup = $acl->isAllowed($role, $formName, $action);

        if($user)
        {
            $allowedUser = $acl->isAllowed($user, $formName, $action);
        }

        return $allowedGroup;
    }

    /**
     * Return guest permissions
     *
     * @return array list of permissions
     */
    private static function loadDefaultPermissions()
    {


    }

    public static function init($di)
    {
        //loading permissions from db
        self::$di = $di;

    }

    /**
     * Default permissions
     *  if in config set - use safe mode
     *
     */
    public function defaultParameters()
    {
        //setting default user

        //setting default permission, factory permissions

        //

    }

}