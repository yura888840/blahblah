<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 18.09.15
 * Time: 16:03
 */

namespace Crm\Listeners;


class MyListener extends BaseListener
{

    public function beforeRun($event, $myComponent)
    {
        echo "beforeMyTask\n";
    }

    public function afterRun($event, $myComponent)
    {
        echo "afterSomeTask\n";
    }

    public function beforeGetAllObjects($event, $myComponent)
    {
        // current user has permissions for this function ?

        // if no - return on next function on stack

        //echo "checking permissions\n";
    }

    // Т.е., у такой компоненты, есть 2 параметра - стэк приоритетов функций (для разл. ролей ) . Расположенных в порядке убывания

    // и, имя метода, что вызван

    // А, ну да, инжектирован еще, набор прав

    // (граничные условия ?! Поглядеть)

    // Обязательно, access переменная - это массив, с 0- вым элементом. Пока что, вот так.

    public function beforeAction($event, $myComponent)
    {
        echo "<pre>";
        //print_r($myComponent);
        //print_r($event);
        print_r($myComponent->calledMethod);

        echo "<br/><br/>";

        $pr = $myComponent->getPriorityStack();

        print_r(array_shift($pr));

        echo "<br/><br/>";

        print_r($pr);

        echo "<br/><br/>";

        // Вот здесь - алгоритм - находим первую доступную функцию

        // U, A, G
        $myComponent->setAccess("U");
        $pr = $myComponent->getPriorityStack();
        $firstAllowedComponent = $myComponent->access[0];

        // Нам нужен следующий компонент, в случае, если вызываемый метод, не равен 1- му методу в access- листе

        $firstAllowedIdx = array_search($pr, $firstAllowedComponent);
        $calledMethodIdx = array_search($pr, $myComponent->calledMethod);

        // если компонент не найден - тогда - последний компонент
        if ($firstAllowedIdx === false) {
            $calledMethod = array_pop($pr);
        } elseif ($calledMethodIdx < $firstAllowedIdx) {

        } elseif ($calledMethodIdx == (sizeof($pr) - 1)) {

        } else {

        }

        if ($firstAllowedComponent != $myComponent->calledMethod) {
            while (!empty($pr) && array_shift($pr) != $firstAllowedComponent) {
                var_dump($pr);
            }
        }


        var_dump($pr);
        die();
        // Нужно из- за -   Strict standards: Only variables should be passed by reference - даже в php 5.6
        $pr = $myComponent->getPriorityStack();

        /// Если - она не найдена - берем последнюю из стэка
        $lastFunc = array_pop($pr);

        echo "<br/><br/>";

        print_r($lastFunc);

        echo "<br/><br/>";

        parent::beforeAction($event, $myComponent);
    }


}