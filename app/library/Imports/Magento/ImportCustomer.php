<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19.02.15
 * Time: 12:32
 */


namespace Crm\Import\Magento;

use Phalcon\Logger\Adapter\File as FileAdapter;


class ImportCustomer
{
    public $logger;
    public $loggerStringId;
    public $soapSession;
    public $soapClient;

    function __construct() {
        $config = include __DIR__ . "/../../app/config/config.php";
        date_default_timezone_set($config->application->timezone);
        $this->logger = new FileAdapter($config->import->log_path . "ImportCustomer.log");
        $this->soapClient = new \SoapClient($config->soap->url);
        $this->soapSession = $this->soapClient->login($config->soap->username, $config->soap->password);
    }

    public function import($stepId=1, $rewrite=false, $updateAll=false)
    {
        $this->logger->log("Start import");

        if ($updateAll) {//all AnalyticsCustomers pass
            $itemMongo = AnalyticsCustomers::findFirst(array(
                "sort" => array("customer_id" => -1)
            ));
            if ($itemMongo){
                $startId = $itemMongo->customer_id+1;
            } else {
                $startId=1;
            }
//            return;
            $endId=$startId+$stepId-1;
            $chunkSoap=30;
            $countChunk=ceil(($endId-$startId+1)/$chunkSoap);
            $countNullResult=0;
            for ($i = 0; $i < $countChunk; $i++) {
                $stringIDS='';
                for ($j = 1; $j <= $chunkSoap; $j++) {
                    $idCurrent = $startId-1+($chunkSoap*$i)+$j;
                    if ($idCurrent<=$endId) {
                        $stringIDS=($stringIDS=='') ? $idCurrent : $stringIDS.','.$idCurrent;
                    }
                }
                $this->loggerStringId='';

                $params = array(
                    'complex_filter' => array(
                        array(
                            'key' => 'customer_id',
                            'value' => array('key' => 'in', 'value' => $stringIDS)
                        ),
                    )
                );
                $this->logger->log("Soap request start IDS: ".$stringIDS);
                $start = microtime(true);
                $result = $this->soapClient->customerCustomerList($this->soapSession, $params);
                $time = microtime(true) - $start;
                $this->logger->log("Soap request end, time request chunkSoap=$chunkSoap: ".$time);
                if ($result==array()){
                    $countNullResult+=1;
                    if ($countNullResult>2){
                        break;
                    }
                } else {
                    $countNullResult=0;
                }
                foreach ($result as $itemSoap){
                    $this->saveSingleCustomer($itemSoap, $rewrite);
                }
                $this->logger->log("Import customer: ".$this->loggerStringId);
            }
        } else {//only updated and created customer pass
            $rewrite = true;//always updating
            $itemMongo = AnalyticsCustomers::findFirst(array(
                "sort" => array("updated_at" => -1)
            ));
            if (!$itemMongo){
                $this->logger->log("Not updated, no record in mongo");
                $this->logger->log("End import============================================================================");
                return 0;
            }
            $params = array(
                'complex_filter' => array(
                    array(
                        'key' => 'updated_at',
                        'value' => array('key' => 'from', 'value' => date("Y-m-d H:i:s",strtotime($itemMongo->updated_at.' + 1 SECONDS')))
                    ),
                )
            );
            $result = $this->soapClient->customerCustomerList($this->soapSession, $params);
            foreach ($result as $itemSoap){
                $this->saveSingleCustomer($itemSoap, $rewrite);
            }
        }

        $this->logger->log("End import============================================================================");
        return 1;
    }

    private function saveSingleCustomer($itemSoap, $rewrite=false)
    {
//        $this->logger->log("Soap item request start ID: ".$itemSoap->customer_id);
//        $start = microtime(true);
        $itemSoap = $this->soapClient->customerCustomerInfo($this->soapSession, $itemSoap->customer_id);
//        $time = microtime(true) - $start;
//        $this->logger->log("Soap item request end, time: ".$time);


        $itemMongo = AnalyticsCustomers::findFirst(array(
            array('customer_id' => (int)$itemSoap->customer_id)
        ));

        if (!$itemMongo){
            $itemMongo = new AnalyticsCustomers();
        } else {
            if (!$rewrite){
                return false;
            } else {
                //full rewrite
//                $itemMongo->delete();
//                $itemMongo = new AnalyticsCustomers();
            }
        }
        foreach ($itemSoap as $key => $value) {
            $typeField = array(
                'customer_id'=>'int',
                'store_id'=>'int',
                'website_id'=>'int',
                'group_id'=>'int',
                );
            if ( array_key_exists($key, $typeField)) {
                settype($value, $typeField[$key]);
            }
            if ($key=='categories' or $key=='category_ids'){
                foreach ($value as $keyCat=>$cat){
                    settype($cat, 'int');
                    $value[$keyCat]=$cat;
                }
            }
            $itemMongo->$key = $value;

        }
        if ($itemMongo->save()){
            $this->loggerStringId=($this->loggerStringId=='') ? $itemMongo->customer_id : $this->loggerStringId.','.$itemMongo->customer_id;
        } else {
            $this->logger->error("Not save item MongoDB: ".$itemSoap->customer_id);
        }
        return true;
    }

}