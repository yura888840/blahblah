<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 15.07.15
 * Time: 14:01
 */

/**
 * Class TestaController
 *
 * @module("tickets")
 */
class TestaController extends ControllerBase
{

    /**
     * Method index
     *
     * @permission({permission_grou1p="list", module="Tickets", default="allow"})
     */
    public function indexAction()
    {
        // ... code
    }

    /**
     * Method list
     *
     * @permission({permission_group="list", module="Tickets", default="allow"})
     */
    public function listAction()
    {
        // ... code
    }


    /**
     * Method creates instance of ...smthng
     *
     * @permission({permission_group="create", module="Tickets", default="allow"})
     */
    public function createAction()
    {

    }

    public function isconsoleAction()
    {

        var_dump(defined('IS_CONSOLE'));
    }

}