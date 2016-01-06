<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 26.08.15
 * Time: 16:27
 */

use Phalcon\Logger\Adapter\File as FileAdapter;
use Crm\Components\Mailing\Queue\Package;
use Crm\Components\Mailing\MailTemplates\TemplateStorage;
use Crm\Components\Mailing\MailTemplates\EmailPreparator;

class StartMailingTask extends \Phalcon\CLI\Task
{

    private $logger;
    private $mailer;
    private $storage;

    public function mainAction()
    {

        date_default_timezone_set($this->config->application->timezone);

        echo 'Start sending e-mail to recepients' . PHP_EOL . PHP_EOL;

        $this->initLogger();

        $this->logger->log("Starting mail sending process.");

        $this->processStorageForRealEmailSending();

        $this->logger->log("Finishing mail sending process.");

        echo 'Done.' . PHP_EOL;
    }

    private function initLogger()
    {
        $logPath = $this->config->import->log_path;
        $this->logger = new FileAdapter($logPath . "MailingProcess.log");
    }

    // @todo build objects - classes for puposes of this method
    private function processStorageForRealEmailSending()
    {
        $di = Phalcon\DI::getDefault();

        $config = include __DIR__ . "/../config/config.php";

        //@todo fix this
        $packetsList = $di->get('MailingQueueStorage');

        // initing mailer
        $mailer = $di->get('Mailer');

        if (!is_array($packetsList)) return;

        foreach ($packetsList as $packageData) {

            // проверка - битый ли пакет
            //  если битый - пропуск
            if (!$this->checkIsValidPackage($packageData)) continue;

            $package = new Package($packageData);

            $entity = $package->getEntity();

            $recepients = $package->getRecepients($packageData);

            $template = TemplateStorage::getTemplateFor($entity);

            $emailBody = EmailPreparator::getBody($package, $template);

            $emailSubject = EmailPreparator::getSubject($package);

            $resultStr = $mailer->send($emailSubject, $emailBody, $recepients);

            $this->logger->log($resultStr);
        }

        $redis = $di->get('redisCache');
        $redis->save($config->mailing->MailingQueueStorage, []);

    }

    // Блок валидатора

    private $packageRootKeys = [
        '_id',
        'objectCls',
        'items',
        'additionalInfo',
        'watchers_list',
    ];


    private $packageStructure = [
        '_id',
        'objectCls',
        'items' => array(),
        'additionalInfo',
        'watchers_list' => array(//simple value
        ),
    ];

    private $hasChilds = [
        'items',
        'watchers_list',
    ];

    // вынести - отдельно, либо оно есть в конфигах
    private $itemsValues = [

    ];

    // schema item => type of value inside
    private $schemaItems = [
        'timestamp' => 'string',
        'changedBy' => 'string',
        'oldValue' => 'string',
        'newValue' => 'string',
    ];

    private function checkIsValidPackage($packageData)
    {

        if (!is_array($packageData)) return false;

        // check root keys of packet
        if (array_intersect(array_keys($packageData), $this->packageRootKeys) != $this->packageRootKeys) {
            return false;
        }

        foreach ($packageData as $k => $v) {

        }

        return true;
    }
}