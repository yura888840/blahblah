<?php

namespace Crm\Widget;

use
    Phalcon\Validation,
    Phalcon\Validation\Validator\PresenceOf;


class WidgetValidation extends Validation {

    public function initialize(){
        $this->add('name', new PresenceOf(array(
            'message' => 'The name is required'
        )));

        $this->add('optName', new PresenceOf(array(
            'message' => 'The optName is required'
        )));

        $this->add('paramsWidget', new PresenceOf(array(
            'message' => 'The optName is required'
        )));

        $this->add('jsList', new PresenceOf(array(
            'message' => 'The jsList is required'
        )));

        $this->add('css', new PresenceOf(array(
            'message' => 'The jsList is required'
        )));

        $this->add('template', new PresenceOf(array(
            'message' => 'The template is required'
        )));

        $this->add('permissions', new PresenceOf(array(
            'message' => 'The permissions is required'
        )));
    }
}