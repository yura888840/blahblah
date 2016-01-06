<?php

use Crm\Import\Magento\ImportProducts;
use Crm\Import\Magento\ImportCustomer;
use Crm\Import\Magento\ImportOrder;

class importTask extends \Phalcon\CLI\Task
{

    public function productAction()
    {
        $importProducts = new ImportProducts();
        $rewrite=false;
        $updateAll=true;
        $countRecord=300;//chunk
        $importProducts->import($countRecord, $rewrite, $updateAll);
    }

    public function customerAction()
    {
        $importCustomer = new ImportCustomer();
        $rewrite=false;
        $updateAll=false;
        $countRecord=100;//chunk
        $importCustomer->import($countRecord, $rewrite, $updateAll);
    }

    public function orderAction($params = array())
    {
        $importOrder = new ImportOrder();
        $rewrite=false;
        $updateAll=true;
        $countRecord=100;//chunk
        $importOrder->import($countRecord, $rewrite, $updateAll, $params);
    }

    public function testAction()
    {
        sleep (300);
    }
}