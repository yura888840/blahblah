<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 04.09.15
 * Time: 13:01
 */

namespace Crm\Helpers;

class MailQueue
{

    public static function  sendToMailQueue($data)
    {
        $gearman = \Phalcon\DI::getDefault()->get('gearmanClient');

        $data = self::sendDataForQueue($data);

        $data = serialize($data);

        //$result = $gearman->doBackground("MailSend", $data);

        return true;//$result;
    }

    // @todo добавление watcher- ов в новый пакет
    private static function sendDataForQueue($data)
    {

        $di = \Phalcon\DI::getDefault();

        $redisCache = $di->get('redisCache');
        $config = $di['config'];
        $identity = $di->get('auth')->getIdentity();

        $storage = $redisCache->get($config->mailing->MailingQueueStorage);

        if (!$storage) $storage = [];

        $brokenItems = [];
        $findItem = false;

        foreach ($storage as $k => $v) {
            if (!is_array($v) || !array_key_exists('objectCls', $v) || !array_key_exists('_id', $v)) {
                $brokenItems[] = $k;
                continue;
            }

            if ($v['objectCls'] == $data['objectCls'] && $v['_id'] == $data['_id']) {
                $findItem = $k;
                break;
            }
        }

        $packet = (false !== $findItem) ? $storage[$findItem] : array_merge(
            \Crm\Schemas\MailingQueuePackage::$schema,
            ['_id' => $data['_id'], 'objectCls' => $data['objectCls']]
        );

        // clean- up
        foreach ($brokenItems as $v) {
            unset($storage[$v]);
        }

        if (!array_key_exists('items', $packet) || !is_array($packet['items'])) {
            $packet['items'] = [];
        }

        if (array_key_exists($data['field'], $packet['items'])) {
            // placeholder for value history
        }

        // preprocessing
        switch ($data['field']) {
            case 'assignTo':
                $data['oldValue'] = array($data['oldValue']);
                $data['newValue'] = array($data['newValue']);

            case 'notify':
                $usersListOld = \Crm\Helpers\UsersHelper::getUsersWthEmailsByUserIds($data['oldValue']);
                $data['oldValue'] = array_values($usersListOld);

                $usersListNew = \Crm\Helpers\UsersHelper::getUsersWthEmailsByUserIds($data['newValue']);
                $data['newValue'] = array_values($usersListNew);
                break;

            case 'deadline':

                foreach (['oldValue', 'newValue'] as $v) {
                    $dt1 = \Crm\Helpers\DateTimeFormatter::format($data[$v]);
                    $data[$v] = $dt1['created_date_time'];
                }

                break;
        }


        $packet['items'][$data['field']] = [
            'timestamp' => \Crm\Helpers\DateTimeFormatter::format(strtotime('now'))['created_date_time'],
            'changedBy' => $identity['name'],
            'oldValue' => $data['oldValue'],
            'newValue' => $data['newValue'],
        ];


        if ($config->mailing->real_notifiers_from_ticket) {
            $ids = \Crm\Models\Tickets::findById($packet['_id'])->notify;
            $packet['watchers_list'] = array_values(\Crm\Helpers\UsersHelper::getUsersWthEmailsByUserIds($ids));
        }


        if ($findItem !== false) {

            $storage[$findItem] = $packet;

        } else {

            $storage[] = $packet;
        }

        // достаем из тикета нотифайеров.. Т.е., это выставляется в конфиге, что, да как ..

        ksort($storage);
        $redisCache->save($config->mailing->MailingQueueStorage, $storage);

        return $packet;
    }


}