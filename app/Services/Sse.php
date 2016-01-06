<?php
/**
 * Created by PhpStorm.
 * User: Kostja
 * Date: 06.08.15
 * Time: 14:54
 */

namespace Crm\Services;


class Sse
{
    public function sendBrowser($eventName, $eventData, $eventRetry, $eventId)
    {
        $dataJson = json_encode($eventData);
        $str = "event: {$eventName}\n" .
            "retry: {$eventRetry}\n" .
            "id: {$eventId}\n" .
            "data: {$dataJson}" .
            "\n\n";
        echo $str;
    }

    /**
     * Function for test purposes
     *
     * @param $eventName
     * @param $eventData
     * @param $eventRetry
     * @param $eventId
     */
    public function _sendBrowser($eventName, $eventData, $eventRetry, $eventId)
    {
        $eventData = [rand(1, 30000), 'sdfsdf', 'werwer'];
        $eventName = 'message';
        $eventId = "123fac34";
        $eventRetry = 10;

        $dataJson = json_encode($eventData);
        $str = "event: {$eventName}\n".
            "retry: {$eventRetry}\n".
            "id: {$eventId}\n".
            "data: {$dataJson}".
            "\n\n";
        echo $str;
    }

    /**
     * send Event in Redis cache
     *
     * @param $eventName
     * @param $eventData
     * @param int $eventRetry
     * @param int $eventId
     */
    static function sendEvent($eventName, $eventData, $eventRetry=3000, $eventId=1)
    {
        $config = \Phalcon\DI::getDefault()->get('config');
        $redisCache = \Phalcon\DI::getDefault()->get('redisCache');
        $prefixSse = $config->redis_sse_prefix;
        $data = array(
            'eventName' => $eventName,
            'eventId' => $eventId,
            'eventRetry' => $eventRetry,
            'eventData' => $eventData,
        );
        $rand = rand(1, 10000);
        $redisCache->save($prefixSse.$eventName.'_'.$rand, $data);
    }

    public function dispatcher()
    {
        $config = \Phalcon\DI::getDefault()->get('config');
        $redisCache = \Phalcon\DI::getDefault()->get('redisCache');

        $prefix = $config->redis_prefix;
        $prefixSse = $config->redis_sse_prefix;

        $this->sendBrowser(1, 2, 3, 4);

        foreach ($redisCache->queryKeys($prefix.$prefixSse) as $key) {
            $key = substr($key,strlen($prefix));
            $data = $redisCache->get($key);
            $this->sendBrowser($data['eventName'], $data['eventData'], $data['eventRetry'], $data['eventId']);
            $redisCache->delete($key);
        }
    }
}