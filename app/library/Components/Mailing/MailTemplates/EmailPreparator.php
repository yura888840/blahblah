<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 28.08.15
 * Time: 18:08
 */

namespace Crm\Components\Mailing\MailTemplates;

use \Phalcon\Mvc\View;


class EmailPreparator
{

    public static function getBody(\Crm\Components\Mailing\Queue\Package $package, $template)
    {
        $view = \Phalcon\DI::getDefault()->get('view');

        $params['changes'] = $package->getChanges();
        $params['entity'] = $package->getEntity();
        $params['element'] = $package->getEntity();

        $body = $view->getRender('MailChanges', $template, $params, function ($view) {
            $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
        });

        return $body;
    }

    public static function getSubject(\Crm\Components\Mailing\Queue\Package $package)
    {
        $subject = "Ecomitize CRM: " . $package->objectClass . " - " . $package->entity->subject;

        return $subject;
    }

}