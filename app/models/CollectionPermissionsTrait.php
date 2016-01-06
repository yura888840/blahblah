<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 26.10.15
 * Time: 18:44
 */

namespace Crm\Models;

trait CollectionPermissionsTrait
{
    protected static $isGuestAllowed = true;

    protected static $isUserAllowed = true;

    protected static $isConsoleAllowed = true;

    protected static $functionsUnderPermissions = [
        'find',
        'findFirst',
        'findById',
        'aggregate',
    ];

    protected static function applyFilters(array $params)
    {
        if (static::$isConsoleAllowed && static::$isUserAllowed && static::$isGuestAllowed) {

        }
    }

    protected static function beforeAction(array $params)
    {

        if (static::$isConsoleAllowed && static::$isUserAllowed && static::$isGuestAllowed) {

        }

        self::applyFilters($params);
    }

    protected static $filters = [];


    public function initialize()
    {

        $callers = debug_backtrace();

        $caller = $callers[3]['function'];

        if (in_array($caller, self::$functionsUnderPermissions)) {
            static::beforeAction([]);
        }
    }



    /**
     * Custom functions block
     * Customized triiggers
     */

    /**
     * find
     */
    public static function find(array $parameters = NULL)
    {
        $parameters = static::beforeFind($parameters);

        // check permissions here

        return static::afterFind(parent::find($parameters));
    }

    protected static function beforeFind($parameters)
    {
        // transformation & permissioons checking here


        return $parameters;
    }

    protected static function afterFind($result)
    {

        // permission & may be some common postprocess


        return $result;
    }

    /**
     * findFirst
     */
    public static function findFirst(array $parameters = NULL)
    {
        $parameters = static::beforeFindFirst($parameters);


        return static::afterFindFirst(parent::findFirst($parameters));
    }

    protected static function beforeFindFirst($parameters)
    {


        return $parameters;
    }

    protected static function afterFindFirst($result)
    {


        return $result;
    }

    /**
     * findById
     */
    public static function findById($id)
    {
        $id = static::beforeFindById($id);

        return static::afterFindById(parent::findById($id));
    }

    protected static function beforeFindById($parameters)
    {


        return $parameters;
    }

    protected static function afterFindById($result)
    {


        return $result;
    }


    /**
     * aggregate
     */
    /*
    public static function aggregate(array $parameters = NULL)
    {
        $parameters = static::beforeAggregate($parameters);

        return static::afterAggregate(parent::aggregate($parameters));
    }

    protected static function beforeAggregate($parameters)
    {


        return $parameters;
    }

    protected static function afterAggregate($result)
    {


        return $result;
    }
    */
}