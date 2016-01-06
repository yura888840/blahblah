<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 06.03.15
 * Time: 10:33
 */


namespace Crm\Import\Magento;

use Phalcon\Logger\Adapter\File as FileAdapter;


class ImportOrder
{
    public $logger;
    public $loggerStringId;
    public $soapSession;
    public $soapClient;
    public $config;
    public $lockFile;

    function __construct() {
        $config = include __DIR__ . "/../../app/config/config.php";
        date_default_timezone_set($config->application->timezone);
        $this->config = $config;
    }

    public function import($stepId=1, $rewrite=false, $updateAll=false, $params)
    {
        if (isset($params[0])){
            $processNumber = $params[0];
        } else {
            $processNumber = 0;
        }
        $this->logger = new FileAdapter($this->config->import->log_path . "ImportOrder" . $processNumber . ".log");
        $this->lockFile = $this->config->import->cache_path . "importOrder" . $processNumber . ".lock";
        if (file_exists($this->lockFile)) {
            echo "Process ImportOrder".$processNumber." is already running\n";
            return;
        }
        file_put_contents($this->lockFile, '1');

        $this->soapClient = new \SoapClient($this->config->soap->url);
        $this->soapSession = $this->soapClient->login($this->config->soap->username, $this->config->soap->password);

        $this->logger->debug("Start import");

        $updateAll = $this->config->importParams['order_all'];
        $orderDay = $this->config->importParams['order_day'];

        $importParamsFind = \Crm\Models\ImportParams::findFirst(array(
            array(
                'order_date' => array(
                    '$exists' => true,
                    '$type' => 9,
                ),
            ),
        ));
        if (!$importParamsFind) {
            $dateStart = $this->config->importParams['order_date_start'];
            $dateStart = Date('Y-m-d H:i:s', strtotime(Date($dateStart)));
            $dateEnd = Date("Y-m-d H:i:s",strtotime($dateStart.' + '.$orderDay.' DAY'));
            $importParams = new \Crm\Models\ImportParams();
            $importParams->order_date = new \MongoDate(strtotime(Date($dateEnd)));
            $importParams->save();
        } else {
            $dateStart = Date('Y-m-d H:i:s', $importParamsFind->order_date->sec);
            $dateEnd = Date("Y-m-d H:i:s",strtotime($dateStart.' + '.$orderDay.' DAY'));
            $importParamsFind->order_date = new \MongoDate(strtotime(Date($dateEnd)));
            $importParamsFind->save();
        }
        $this->logger->debug("dateStart = ".$dateStart);
        $this->logger->debug("dateEnd = ".$dateEnd);

        if ($updateAll) {//all AnalyticsOrders pass===============================================================================
            $params = array(
                'complex_filter' => array(
                    array(
                        'key' => 'CREATED_AT',
                        'value' => array('key' => 'from', 'value' => $dateStart)
                    ),
                    array(
                        'key' => 'created_at',
                        'value' => array('key' => 'to', 'value' => $dateEnd)
                    ),
                )
            );
            $this->logger->debug("Soap request start");
            $start = microtime(true);
            $result = $this->soapClient->salesOrderList($this->soapSession, $params);
            $time = microtime(true) - $start;
            $this->logger->debug("Soap request end, time request rowCount=".count($result).": ".$time);
            foreach ($result as $itemSoap){
                $this->saveSingleOrder($itemSoap, $rewrite);
            }
        } else {//only updated and created AnalyticsOrders pass===================================================================
            $rewrite = true;//always updating
            $itemMongo = AnalyticsOrders::findFirst(array(
                "sort" => array("updated_at" => -1)
            ));
            if (!$itemMongo){
                $this->logger->debug("Not updated, no record in mongo");
                $this->logger->debug("End import============================================================================");
                $updateAt = '1900-01-01 01:01:01';
            }else{
                $updateAt = $itemMongo->updated_at;
            }
            $params = array(
                'complex_filter' => array(
                    array(
                        'key' => 'updated_at',
                        'value' => array('key' => 'from', 'value' => date("Y-m-d H:i:s",strtotime($updateAt.' + 1 SECONDS')))
                    ),
                )
            );
            $result = $this->soapClient->salesOrderList($this->soapSession, $params);
            foreach ($result as $itemSoap){
                $this->saveSingleOrder($itemSoap, $rewrite);
            }
        }

        $this->logger->debug("End import============================================================================");
        unlink($this->lockFile);
        return 1;
    }

    private function saveSingleOrder($itemSoap, $rewrite=false)
    {

        $itemMongo = AnalyticsOrders::findFirst(array(
            array('increment_id' => (int)$itemSoap->increment_id)
        ));

        if (!$itemMongo){
            $itemMongo = new AnalyticsOrders();
        } else {
            if (!$rewrite){
                return false;
            } else {
                //full rewrite
//                $itemMongo->delete();
//                $itemMongo = new AnalyticsOrders();
            }
        }

        $itemSoap = $this->soapClient->salesOrderInfo($this->soapSession, $itemSoap->increment_id);

        foreach ($itemSoap as $key => $value) {
            $itemMongo->$key = $value;
        }

        if ($itemMongo->save()){
            $this->loggerStringId=($this->loggerStringId=='') ? $itemMongo->order_id : $this->loggerStringId.','.$itemMongo->order_id;
        } else {
            $this->logger->error("Not save item MongoDB: ".$itemSoap->order_id);
        }
        return true;
    }

}