<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19.02.15
 * Time: 12:32
 */


namespace Crm\Import\Magento;

use Phalcon\Logger\Adapter\File as FileAdapter;


class ImportProducts
{
    public $logger;
    public $loggerStringId;
    public $soapSession;
    public $soapClient;

    function __construct() {
        $config = include __DIR__ . "/../../app/config/config.php";
        date_default_timezone_set($config->application->timezone);
        $this->logger = new FileAdapter($config->import->log_path . "ImportProducts.log");
        $this->soapClient = new \SoapClient($config->soap->url);
        $this->soapSession = $this->soapClient->login($config->soap->username, $config->soap->password);
    }

    public function import($stepId=1, $rewrite=false, $updateAll=false)
    {
        $this->logger->log("Start import");

        if ($updateAll) {//all products pass
            $itemTemp = AnalyticsProductsTemp::findFirst();
            if ($itemTemp){
                $items = AnalyticsProductsTemp::find(array(
                    'conditions' => array("processed" => 0),
                    'sort' => array("product_id" => 1),
                    'limit' => $stepId
                ));
                foreach ($items as $item) {
                    $resSave = $this->saveSingleProduct($item, $rewrite);
                    if ($resSave){
                        $item->processed = 1;
                        $item->save();
                    }
                }
            }else{//Collection AnalyticsProductsTemp empty
                $params = array(
                    'complex_filter' => array(
                        array(
                            'key' => 'updated_at',
                            'value' => array('key' => 'from', 'value' => '1900-01-01 01:01:01')
                        ),
                    )
                );
                $this->logger->log("Soap request start");
                $start = microtime(true);
                $result = $this->soapClient->catalogProductList($this->soapSession, $params);
                $time = microtime(true) - $start;
                $this->logger->log("Soap request end, time request: ".$time);
                $count=0;
                foreach ($result as $itemSoap){
                    $itemMongoTemp = new AnalyticsProductsTemp();
                    if (!$rewrite){
                        $itemMongo = AnalyticsProducts::findFirst(array(
                            "conditions" => array("product_id" => (int)$itemSoap->product_id)
                        ));
                        if ($itemMongo){
                            continue;
                        }
                    }
                    $itemMongoTemp->product_id = (int)$itemSoap->product_id;
                    $itemMongoTemp->processed = 0;
                    if (!$itemMongoTemp->save()){
                        $this->logger->error("Not save item MongoDB: ".$itemSoap->product_id);
                    }else{
                        $count=$count+1;
                    }
                }
                $this->logger->log("Items in AnalyticsProductsTemp collections: ".$count);
            }
        } else {//only updated and created products pass
            $rewrite = true;//always updating
            $itemMongo = AnalyticsProducts::findFirst(array(
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
            $result = $this->soapClient->catalogProductList($this->soapSession, $params);
            foreach ($result as $itemSoap){
                $this->saveSingleProduct($itemSoap, $rewrite);
            }
        }

        $this->logger->log("End import============================================================================");
        return 1;
    }

    private function saveSingleProduct($itemSoap, $rewrite=false)
    {
        $this->logger->log("Soap item request start ID: ".$itemSoap->product_id);
        $start = microtime(true);
        $itemSoap = $this->soapClient->catalogProductInfo($this->soapSession, $itemSoap->product_id);
        $time = microtime(true) - $start;
        $this->logger->log("Soap item request end, time: ".$time);


        $itemMongo = AnalyticsProducts::findFirst(array(
            array('product_id' => (int)$itemSoap->product_id)
        ));

        if (!$itemMongo){
            $itemMongo = new AnalyticsProducts();
        } else {
            if (!$rewrite){
                return true;
            } else {
                //full rewrite
//                $itemMongo->delete();
//                $itemMongo = new AnalyticsProducts();
            }
        }
        foreach ($itemSoap as $key => $value) {
            $typeField = array(
                'product_id'=>'int',
                'set'=>'int',
                'status'=>'int',
                'price'=>'float',
                'visibility'=>'int',
                'has_options'=>'int',
                'tax_class_id'=>'int',
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
        $resSave = $itemMongo->save();
        if ($resSave){
            $this->loggerStringId=($this->loggerStringId=='') ? $itemMongo->product_id : $this->loggerStringId.','.$itemMongo->product_id;
        } else {
            $this->logger->error("Not save item MongoDB: ".$itemSoap->product_id);
        }
        return $resSave;
    }

}