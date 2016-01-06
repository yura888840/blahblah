<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 13.05.15
 * Time: 17:44
 */

namespace Crm\tasks;


class DbintegrityfixTask
{


    public function mainAction()
    {

        // WidgetsCustomSetUsers - fix
        //@todo aprt of code
        if (!is_array($widgetsList->set)) {

            $widgetsList->grid = [];
            if (!$widgetsList->save()) {
                throw new \Exception('Error saving widgets for roles');
            }
        }

        // fix - так же, и на сетку

    }
    // все фиксы - сюда

    /// $widgetsList = WidgetsCustomSetUsers  - поле set  .-  не массив

}