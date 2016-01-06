<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.03.15
 * Time: 17:53
 */

namespace Crm\Helpers;

class DataSets
{
    private static $vals = array(

        'TicketsStatus' => array(
            '*' => 'New',
        ),
        'TicketsTypes' => array(
            '*' => 'Task',
        ),
        'TicketsPriority' => array(
            '*' => 5
        ),
        'TicketsGroups' => array(
            '*' => 'Main',
        ),

        'status' => array(
            'New' => 'New',
            'Assigned' => 'Assigned',
            'Need Assistance' => 'Need Assistance',
            'Verify & Close' => 'Verify & Close',
            'Reassign' => 'Reassign',
            'Client Review' => 'Client Review',
            'Closed' => 'Closed',
        ),
        'type' => array(
            'Task' => 'Task',
            'Email' => 'Email',
        ),
        'priority' => array(
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
            '6' => 6,
            '7' => 7,
            '8' => 8,
            '9' => 9,
        ),
        'department' => array(
            'financial' => 'financial',
            'support' => 'support',
            'technical' => 'technical',
        ),
    );

    public static function getSetByName($name)
    {
        if(array_key_exists($name, self::$vals))
        {
            return self::$vals[$name];
        }

        return false;
    }

    public static function getValuesBy($name)
    {
        if (array_key_exists($name, self::$vals)) {
            return self::$vals[$name];
        }

        throw new \Exception('No data found in directory for: ' . $name);
    }

    public static function getDefaultValueFor($name)
    {
        if(array_key_exists($name, self::$vals))
        {
            return self::$vals[$name]['*'];
        }

        return false;
    }

} 