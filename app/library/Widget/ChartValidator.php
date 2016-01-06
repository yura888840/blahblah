<?php
/**
 * Created by PhpStorm.
 * User: Konstantin
 * Date: 11.03.15
 * Time: 11:04
 */

namespace Crm\Widget;

use Phalcon\Validation,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Regex;


class ChartValidator extends Validation
{
    public function initialize()
    {
        $this->add('type_widget', new PresenceOf(array(
            'message' => 'The type_widget is required',
            'cancelOnFail' => true
        )));
        $this->add('type_widget', new Regex(array(
            'pattern' => '/^[A-Za-z_]+$/',
            'message' => 'The type_widget is fail',
            'cancelOnFail' => true
        )));

        $this->add('indicators', new Regex(array(
            'pattern' => '/^[0-9]+$/',
            'message' => 'The indicators not numeric'
        )));
        $this->add('intervals', new Regex(array(
            'pattern' => '/^[0-9]+$/',
            'message' => 'The intervals not numeric'
        )));
        $this->add('period', new Regex(array(
            'pattern' => '/^[0-9]+$/',
            'message' => 'The period not numeric'
        )));
        $this->add('goods', new Regex(array(
            'pattern' => '/^[0-9]+$/',
            'message' => 'The goods not numeric'
        )));

    }
}