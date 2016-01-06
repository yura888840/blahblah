<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23.03.15
 * Time: 15:57
 */

namespace Crm\Forms;


trait FormBaseTrait {

    /**
     * Prints messages for a specific element
     */
    public function messages($name)
    {
        if ($this->hasMessagesFor($name)) {
            foreach ($this->getMessagesFor($name) as $message) {
                $this->flash->error($message);
            }
        }
    }
} 