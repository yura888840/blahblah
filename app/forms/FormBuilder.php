<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 26.05.15
 * Time: 12:29
 */

namespace Crm\Forms;


class FormBuilder extends CrmForm
{

    public function initialize()
    {

        if ($this->elementsSet) {
            foreach ($this->elementsSet as $element) {
                if (!array_key_exists('classname', $element)
                    || !array_key_exists('elementParams', $element)
                    || !array_key_exists('element_id', $element)
                ) {
                    throw new \Exception('Error in element data');
                }

                $cls = self::ELEMENTS_NAMESPACE . $element['classname'];

                if (!array_key_exists('elementParams1', $element) || empty($element['elementParams1'])) {
                    $field = new $cls($element['element_id'], $element['elementParams']);
                } else {
                    $field = new $cls($element['element_id'], $element['elementParams'], $element['elementParams1']);
                }

                if (array_key_exists('label', $element) && !empty($element['label'])) {
                    $field->setLabel($element['label']);
                }

                if (array_key_exists('validators', $element) && !empty($element['validators'])) {
                    foreach ($element['validators'] as $validatorName => $validatorParams) {
                        $validatorWthNS = self::VALIDATORS_NAMESPACE . $validatorName;

                        $field->addValidator(new $validatorWthNS($validatorParams));

                    }
                }

                $this->add($field);
            }
        }
    }
}