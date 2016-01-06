<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 15:01
 */

use Phalcon\DI\FactoryDefault;
use Crm\Models\Users;
use Crm\Models\Profiles;
use Crm\Models\Permissions;
use Crm\Models\ImportProducts;
use Crm\Models\ImportCustomer;
use Crm\Models\ImportOrder;
use Crm\Tasks;

class TestController extends ControllerBase{

    public function salesAction()
    {
        $this->view->setRenderLevel(Phalcon\Mvc\View::LEVEL_NO_RENDER);
//        $products = \Crm\Models\AnalyticsOrders::aggregate(array(
//            array(
//                '$project' => array(
//                    'items.product_id' => 1,
//                )
//            ),
//            array(
//                '$unwind' => '$items'
//            ),
//            array(
//                '$group' => array(
//                    '_id' => array('items.product_id' => '$items.product_id'),
//                    'product_id' => array('$first' => '$items.product_id'),
//                )
//            ),
//            array(
//                '$sort' => array('product_id' => 1)
//            ),
//        ));
//
//        $filter = array(
//            array(
//                'text'=>'By goods',
//                'value'=>0,
//            ),
//            array(
//                'text'=>'All',
//                'value'=>0,
//            ),
//        );
//        foreach ($products['result'] as $value) {
//            //TODO
//            $pr=\Crm\Models\AnalyticsProducts::findFirst(array(
//                array('product_id' => $value['product_id'])
//            ));
//            if ($pr) {
//                $filterValue = array (
//                    'text'=>$pr->sku,
//                    'value'=>$value['product_id'],
//                );
//                $filter[] = $filterValue;
//            }
//        }
//
//        $cu=\Crm\Models\AnalyticsProducts::findFirst();
//        var_dump($filter);
//        return;
//
//        $hh=8;

        $sale = \Crm\Models\AnalyticsOrders::aggregate(array(
            array(
                '$project' => array(
//                    '_id' => 0,
//                    'status' => 1,
//                    'increment_id' => 1,
//                    'order_id' => 1,
//                    'created_at' => 1,
//                    'updated_at' => 1,
                    'month_upd' => array( '$substr' => array('$created_at',0,10) ),
                    'subtotal' => 1,
//                    'grand_total' => 1,
//                    'total_canceled' => 1,
//                    'base_subtotal' => 1,
//                    'base_grand_total' => 1,
//                    'base_total_canceled' => 1,
//                    'items.price' => 1,
                    'items.qty_ordered' => 1,
//                    'items.base_price' => 1,
//                    'items.original_price' => 1,
//                    'items.base_original_price' => 1,
//                    'items.row_total' => 1,
//                    'items.base_row_total' => 1,
//                    'count' =>array('$add' => 1),
                )
            ),

            array(
                '$unwind' => '$items'
            ),
//            array(
//                '$project' => array(
//                    '_id' => 0,
//                    'items.price' => 1,
//                    'items.qty_ordered' => 1,
//                )
//            ),
            array(
                '$match' => array(
                    'items.price' => array(
                        '$gt' => 0
                    ),
                )
            ),
            array(
                '$group' => array(
                    '_id' => array('month_upd' => '$month_upd'),
//                    'number' => array('$sum' => '$count'),
                    'sum' => array('$sum' => '$subtotal'),
                    'sumqty' => array('$sum' => '$items.qty_ordered'),
                    'date' => array('$first' => '$month_upd'),
//                    'max_updated' => array('$max' => '$updated_at'),
//                    'max_price' => array('$max' => '$items.price'),
                )
            ),
            array(
                '$sort' => array('date' => 1)
            ),
        ));

//        $sale = \Crm\Models\AnalyticsOrders::aggregate(array(
//            array(
//                '$project' => array(
//                    'status' => 1,
//                    'increment_id' => 1,
//                    'order_id' => 1,
////                    'created_at' => 1,
//                    'updated_at' => 1,
//                    'month_upd' => array( '$substr' => array('$created_at',0,7) ),
//                    'subtotal' => 1,
////                    'grand_total' => 1,
////                    'total_canceled' => 1,
////                    'base_subtotal' => 1,
////                    'base_grand_total' => 1,
////                    'base_total_canceled' => 1,
////                    'items.price' => 1,
////                    'items.base_price' => 1,
////                    'items.original_price' => 1,
////                    'items.base_original_price' => 1,
////                    'items.row_total' => 1,
////                    'items.base_row_total' => 1,
//                    'count' =>array('$add' => 1),
//                    )
//            ),
////            array(
////                '$group' => array(
////                    '_id' => array('status' => '$status'),
////                    'number' => array('$sum' => '$count'),
////                    'max_updated' => array('$max' => '$updated_at'),
////                    'max_price' => array('$max' => '$items.price'),
////                )
////            ),
////            array(
////                '$sort' => array('max_updated' => 1)
////            ),
//        ));
        var_dump ($sale);

    }

    public function tAction()
    {
        $mongo= $this->mongo;

        $mongo->createCollection('users');
        $user = new Users;
        $user->name = "crm-master@ecomitize.com";
        $user->email = "crm-master@ecomitize.com";
        $user->password = "754850ec73d06d07ca38f2fd5c459dab";
        $user->save();

        $mongo->createCollection('profiles');

        $profs = array("admin", "user", "guest");
        foreach($profs as $p)
        {
            $prof = new \Crm\Models\Profiles();
            $prof->name = $p;
            $prof->active = "Y";
            $prof->save();
        }

        $mongo->createCollection('permissions');
        $perm = new \Crm\Models\Permissions();
        $perm->profile = "user";
        $perm->resource = "dashboard";
        $perm->action = "index";
        $perm->save();
    }

    public function m2302Action()
    {
        $mongo= $this->mongo;

        $mongo->createCollection('reset_password');

    }

    public function m2302_1Action()
    {
        $mongo= $this->mongo;

        $user = new Users;

        $user->email = "yura888840@gmail.com";
        $user->name = "yura888840@gmail.com";

        $user->password = '$2a$08$Nit6EuIjc.i0pDYgvXgpqecy6u4rmcEyejaA3PGmhHyurVxsvoSYC';

        $user->save();

    }


    public function productAction()
    {
        $importProducts = new ImportProducts();
        $rewrite=true;
        $updateAll=false;
        $countRecord=15;

        var_dump ($importProducts->import($countRecord, $rewrite, $updateAll));

    }

    public function customerAction()
    {
        $importCustomer = new ImportCustomer();
        $rewrite=true;
        $updateAll=true;
        $countRecord=2;//chunk

        var_dump ($importCustomer->import($countRecord, $rewrite, $updateAll));

    }

    public function orderAction()
    {
        $importOrder = new ImportOrder();
        $rewrite=true;
        $updateAll=true;
        $countRecord=100;//chunk

        var_dump ($importOrder->import($countRecord, $rewrite, $updateAll));

    }

    public function ttAction()
    {
        $this->view->setVar('module', 'list');
    }

    private function setupComments()
    {

    }

    public function unauthPageAction(){

    }

    public function unauthorizedtest401Action(){
        $this->view->disable();
        //http_response_code(401);
        //echo json_encode(['reason' => 'Unauthorized']);
        echo json_encode([
            'reason' => 'no-access',
            /*'msg' => 'No access to this module'*/
        ]);
    }

    public function iAction()
    {
        $this->auth->remove();
    }

    public function isadminAction()
    {
        $r = $this->auth->getIsAdmin();

        var_dump($r);
    }

    public function testEmailAction()
    {
        $mails = $this->config->mail_fetcher;

        $mailReceiver = new \Crm\Mail\MailReceiver();

        // departments - predefined list of departments to ticket
        foreach ($mails as $department => $emailsConfig) {

            //var_dump($emailsConfig);
            // в парам етры тикет -а
            var_dump($department);
            var_dump($emailsConfig);
            echo('**');
        }

    }

    public function sendemailAction()
    {
        $userEmail = 'yuri.oblovatskyy@ecomitize.com';
        $userName = 'Yuri';

        $this->getDI()->getMail()->sendFromAddress(array(
                $userEmail => $userName)

            , "Reset your password", 'response2ticket', array(
                'resetUrl' => 'reset-password/',
            ),

            'financial');
    }

    public function cAction()
    {

    }

    public function eventsmanagerAction()
    {
        echo '**';

        $m = new \Crm\Models\PrivateResources();

        $m->save();
    }

    public function tstTicketsAction()
    {
        $this->view->disable();

        $t = \Crm\Models\Tickets::find([]);

        var_dump(count($t));
    }

    public function tstNewWorkAction()
    {

    }

    public function sendevtestAction()
    {
        $this->view->disable();
        \Crm\Services\Sse::sendEvent("message", array('j' => 1, 'k' => 2));
    }

    // апдейт работает по принципу - истории

    private $_key = 'package_test';

    // как быть с - пользователями, если он - ну, не зареген в системе ?
    public function ccAction()
    {
        $this->view->disable();

        $cacheStorage = $this->redisCache;

        // История значений - стэк
        $package = [
            '_id' => '54f47e5e51be0bd69d8b4568',

            'items' => [
                'priority' => [
                    [
                        'timestamp' => '',
                        'changedBy' => '',
                        'oldValue' => '',
                        'newValue' => '',
                    ],

                ],
            ],

            'additionalInfo' => [],

            // _id of user => email

            // Алгоритм - если, допустим, пользователь - ну его нету у нас в БД
            //                                           (возможен ли вариант ?)
            // Тогда - хэш состоит из 2-х частей - UNKNOWN_USER_порядковый номер
            'watchers_list' => [
                '551ea2f751be0b022b8b4568' => 'dimabogatov@gmail.com',
                '550800e351be0bcc0d8b4567' => 'a44545@a.com',
                '54eddce351be0bec9d8b4568' => 'ks275@yandex.ru',
                'UNKNOWN_USER_1' => 'a@a.bb',
                'UNKNOWN_USER_2' => 'b@a.bb',
            ],

        ];

        $cacheStorage->save('package_test', $package);

        $package = null;

        $package = $cacheStorage->get('package_test');

        var_dump($package);
        // @todo проверить как хранится в storage (чтоб не было б , коллизий, т.д. )

        // У нас зреет необходимость - добавлять неИзвестных пользователей, в отдельную коллекцию.

        // С возможностью их менеджить, после

        // Тогда - вопрос, а как спам фильтровать ?
    }

    public function ddAction()
    {
        $this->view->disable();

        $cacheStorage = $c = $this->redisCache;

        var_dump($cacheStorage->exists($this->_key));

        var_dump($c->queryKeys(''));

        var_dump($cacheStorage->get($this->_key));
        $cacheStorage->delete($this->_key);

        /**
         *
         * getFrontend ()
         *  setFrontend - ???
         *  getOptions ()
         *   setOptions
         *   getLastKey ()
         *   setLastKey - ???
         *
         */
    }

    public function ffAction()
    {
        $this->view->disable();

        echo 'Testing mailing system :' . PHP_EOL . PHP_EOL;
        $p = new Crm\Components\Mailing\Queue\Package();

        var_dump($p);
    }

    protected $simpleObjectConstantKeys = [
        '_id',
        'objectCls',
    ];

    protected $complexObjectConstantKeys = [
        'items',
        'additionalInfo',
        'watchers_list',
    ];

    /// вот это можно из справочника загружать
    protected $keysListOfComplexObjectsWithConstantKeys = [
        'items' => [
            'priority',
            'department',
        ]
    ];

    protected $fixedStructureComplexValues = [
        'items' => [
            'timestamp',
            'changedBy',
            'oldValue',
            'newValue'
        ],
    ];

    /// в одном случае - наполнение данными

    /// в другом - чтение
    //// т.е., модуль состоит из 2- х частей


    /// часть доставания данных, из

    ///

    public function fillqueueAction()
    {
        $c = $this->redisCache;

        $data = [
            [
                // ключ - константа, значение - переменная, тип - строка , + некое значен ие
                '_id' => '54f47e5e51be0bd69d8b4568',
                'objectCls' => 'tickets',

                // ключ - константа, значение - сложное
                'items' => [
                    'priority' =>
                        [
                            'timestamp' => '',
                            'changedBy' => '',
                            'oldValue' => '',
                            'newValue' => '',
                        ],
                    // ключ в данном случае - это переменная
                    'department' =>
                    // сложное значение, состоящее из фиксированного набора простых значений
                        [
                            'timestamp' => '',
                            'changedBy' => '',
                            'oldValue' => '',
                            'newValue' => '',
                        ],

                ],
                'additionalInfo' => [],
                'watchers_list' => [
                    '54eb928151be0bec9d8b4567' => 'yuri.oblovatskyy@ecomitize.com',
                ],
            ],
        ];

        $c->save($this->config->mailing->MailingQueueStorage, $data);

        var_dump(json_encode($data));

        echo 'Done filling test mail queue';
    }

    public function testMailSendAction()
    {
        $this->view->disable();

        $mailer = new Crm\Mail\Mail();

        $body = 'Test mail sending by Swiftmailer for php 5.6';

        $to = 'yuri.oblovatskyy@ecomitize.com';
        $subject = 'Test sending from php 5.6';
        $fromDepartment = 'financial';
        $tplName = 'response2ticket';
        $params = ['body' => $body];

        $mailer->sendFromAddress($to, $subject, $tplName, $params, $fromDepartment);


        echo 'Mail sended' . PHP_EOL;
    }

    public function gearmanAction()
    {
        $res = \Phalcon\DI::getDefault()->get('gearmanClient');

        // на вход - массив с обновлениями

        // затем-  сравнить старое - новое значение

        // если есть изменения - записать в пакет


        $data = ['e-mail' => 'my@email.com', 'subj' => 'subject', 'fromDepartment' => 'technical'];
        $data = serialize($data);

        $r = $res->doBackground("MailSend", $data);
        var_dump($r);
    }

    public function gearmanmonitorAction()
    {
        $c = $this->redisCache;

        echo '<pre>';
        print_r($c->get($this->config->mailing->MailingQueueStorage));
        echo '</pre>';
    }


    public function qqAction()
    {
        echo 'testing Model permssion' . PHP_EOL;

        $a = Crm\Models\TicketsWrapper::find([]);

        var_dump($a);

        $auth = $this->auth;

        echo 'isAdmin : ' . $auth->getIsAdmin();

    }

    public function eaAction()
    {
        $identity = $this->auth->getIdentity();

        $userId = strval($identity['id']);

        var_dump($identity);

        var_dump($userId);

        $user = \Crm\Models\Users::findById($userId);

        $simplePermissions = \Crm\Models\SimplePermissions::find([['userId' => $userId]]);

        //var_dump($user);
        //var_dump($simplePermissions);

        // Default permissions is - Deny

        // в форме - можем запретить вывод поля

        $permissionGoogleCalendar = false;
        $permissionTicketAssign = false;

        if (!empty($simplePermissions)) {
            $perms = $simplePermissions[0]->permisssions;

            $permissionGoogleCalendar = $perms['google_calendar'];
            $permissionTicketAssign = $perms['ticket_assign'];

        }

        if ($this->auth->getIsAdmin() && false) {
            // override permissions from Db
            echo 'User role: is admin' . '<br/><br/><br/>';

            $permissionTicketAssign = true;

            $permissionGoogleCalendar = true;
        }

        echo 'Permission to access company Google Calendar is ' . ($permissionGoogleCalendar ? 'true' : 'false') . '<br/><br/>';

        echo 'Permission to Assign Tickets is ' . ($permissionTicketAssign ? 'true' : 'false') . '<br/><br/>';
    }


    public function initeventsAction()
    {
        $object = [
            'my-component',

            // instance of class Listener
            '\Crm\Listeners\MyListener'
        ];

        $o = $this->di->getComponent('My')->init();

        $o->getAllObjects();

        // Т.е., в таком варианте - все построено на компонентах ( в т.ч., модели, и т.д., организаторы - пОстроители view- х)

        // кмаждая компонента - стэк функций, доступных тому или, той или иной роли.

        //  если, нет доступа - перенаправление, по стэку вниз (на самом низшем ур- не - forbidden- функция )

        // в принципе, эт можно поменять в объекте BaseListener

        //var_dump($o);

        die();

        $this->testWrapper('My');


        // core init
        $eventsManager = new Phalcon\Events\Manager();

        $myComponent = new \Crm\Components\MyComponent('my-component');

        $myComponent->setEventsManager($eventsManager);

        $eventsManager->attach('my-component', new \Crm\Listeners\MyListener());

        // run

        // someTask - может  быть однотипной коммандой
        //$myComponent->run();

        $myComponent->getAllObjects();

        // можно сделать однотипные методы, например..
    }

    private function testWrapper($component)
    {
        $class = "\\Crm\\Components\\{$component}Component";

        $eventsManager = new Phalcon\Events\Manager();

        $myComponent = new $class($component);

        $myComponent->setEventsManager($eventsManager);

        $eventsManager->attach($component, new \Crm\Listeners\MyListener());

        return $myComponent;

    }


    public function emailchainAction()
    {
        // эмулируем ситуацию запроса цепочки писем по текущему Id
        $childIdMongo = "562f8d98c9a599857d8b4569";

        $childIdGmail = "<CAKKj76ioFnFErx0DvEMnQySspYD6b=vwJv-T8L0aQH5WkP8VfA@mail.gmail.com>";

        $email = \Crm\Models\Email::find([["messageId" => "<CAKKj76gxByrpD59pUXZOA5W0+iJzW+gTYyFLS_jnFRrDqsT78A@mail.gmail.com>"]]);
        if ($email) $email = $email[0];
        else throw new \Exception("Message with Id not found"); // сахар

        //var_dump($email);

        $parentIdGmail = $email->inReplyTo;

        $parentEmail = \Crm\Models\Email::find([["messageId" => $parentIdGmail]]);

        ///var_dump($parentEmail);

        // Как устроены цепочки е- мейлов на gmail- е ?
        //   @todo провести рисеарч

        // Мини- рисеарч. Найти все письма, что привязаны к корневому, у кторрого - -(ых )
        /// messageId
        $allemailsWithSameParent = \Crm\Models\Email::find([["inReplyTo" => $parentIdGmail]]);

        echo count($allemailsWithSameParent) . PHP_EOL . "<br>" . PHP_EOL . "<br>";

        var_dump($allemailsWithSameParent);
    }

    public function sendReplyToEmailAction()
    {
        $message = Swift_Message::newInstance();

        //var_dump($message);

        /// сейчас тут - оригинальный id  с письма gmail с ящика - yura888840
        $parentId = "<CAKKj76ixPB0xJt91w2BCGxY9G5dDMw1nBCmEWm_t7JzLSarFrw@mail.gmail.com>";

        $body = "In reply to chain#id#2710.4 - to the same parent - reply 2";

        ///var_dump(get_class_methods($message));

        $message->getHeaders()->addTextHeader('X-Tags', 'tag1 tag2 tag3');

        $message->getHeaders()->addTextHeader('In-Reply-To', $parentId);

        $message->getHeaders()->addTextHeader('References', $parentId);

        $message->setSubject("re: re: chain#id#2710.4")
            ->setTo(["yura888840@gmail.com" => "Yuri O"])
            ->setFrom(array(
                "crmfinancialdep@gmail.com" => "Crm Financial"
            ))
            ->setBody($body, 'text/html');


        var_dump($message->getHeaders());

        $mailSettings = $this->config->mailer;

        $transport = Swift_SmtpTransport::newInstance(
            $mailSettings->host,
            $mailSettings->port,
            $mailSettings->security
        )
            ->setUsername($mailSettings->username)
            ->setPassword($mailSettings->password);

        $mailer = \Swift_Mailer::newInstance($transport);

        $res = $mailer->send($message);

        var_dump($res);
    }

    // Вот тут - оправим письмо children- у

    /// 	Crm Financial <testcrmcrmtest@gmail.com>
    /// Кому:	Yuri O <yura888840@gmail.com>
    /// 	re: chain#id#2710.4

    /// MessageId - <eff5978aa2e2b9337d24735351cfb2ba@crm>

    /// BИтак, у нас цепочка, что привязано, к одному и тому же родителю :
    //// <CAKKj76ixPB0xJt91w2BCGxY9G5dDMw1nBCmEWm_t7JzLSarFrw@mail.gmail.com>
    // Заголовки, последовательно, по время отПравлениЯ
    //  In reply to chain#id#2710.1
    // Message-ID: <eff5978aa2e2b9337d24735351cfb2ba@crm>       (**1)
    //
    // In reply to chain#id#2710.4 - to the same parent
    // Message-ID: <8d1ad58c0b37fb2084d9129178883204@crm>       (**2)
    //
    // In reply to chain#id#2710.4 - to the same parent - reply 2
    // Message-ID: <8df43c5f59d5a40691045f5e00769b7c@crm>       (**3)
    //
    // Построим цепочки :

    // Тест- кейс
    //  Ответ - на (**2) - 2 письма на этого родИтеля

    // После этого - фиксация, как оно выглядит

    // Ответ - на (**1) - 2 письма на этого родителя
    //  фиксация вида
    // Затем - ответ на после- корневого 1- го предка - 2 письма
    //   как оно выглядит


    private $messageIdsChain = [
        '<eff5978aa2e2b9337d24735351cfb2ba@crm>', // (**1)
        '<8d1ad58c0b37fb2084d9129178883204@crm>', // (**2)
        '<8df43c5f59d5a40691045f5e00769b7c@crm>', // (**3)
    ];

    public function testGmailChains1Action()
    {
        //... тут код - для последовательности тест- кейс ов

        // Ответ - на (**2)
        $this->sendReplyToSpecifiedByHashParent("<8d1ad58c0b37fb2084d9129178883204@crm>", "**2.1");
        $this->sendReplyToSpecifiedByHashParent("<8d1ad58c0b37fb2084d9129178883204@crm>", "**2.2");
    }

    // DONE

    public function testGmailChains2Action()
    {

    }

    public function testGmailChains3Action()
    {

    }

    // subNum - string - направление цепочки по нодам (thread s)
    public function sendReplyToSpecifiedByHashParent($hash, $subNum)
    {
        $message = Swift_Message::newInstance();

        $parentId = $hash;

        $body = "In reply to chain#id#2710.4{$subNum}";

        $message->getHeaders()->addTextHeader('X-Tags', 'tag1 tag2 tag3');
        $message->getHeaders()->addTextHeader('In-Reply-To', $parentId);
        $message->getHeaders()->addTextHeader('References', "<CAKKj76ixPB0xJt91w2BCGxY9G5dDMw1nBCmEWm_t7JzLSarFrw@mail.gmail.com>\r\n" . $parentId); // вот тут references вступают

        $message->setSubject("re: re: chain#id#2710.4{$subNum}")
            ->setTo(["yura888840@gmail.com" => "Yuri O"])
            ->setFrom(array(
                "crmfinancialdep@gmail.com" => "Crm Financial"
            ))
            ->setBody($body, 'text/html');

        $mailSettings = $this->config->mailer;

        $transport = Swift_SmtpTransport::newInstance(
            $mailSettings->host,
            $mailSettings->port,
            $mailSettings->security
        )
            ->setUsername($mailSettings->username)
            ->setPassword($mailSettings->password);

        $mailer = \Swift_Mailer::newInstance($transport);

        $res = $mailer->send($message);

        var_dump($res);
    }


    /**
     * <<< БЛОК - все что касаемо е- меЙлов, цепочек >>>
     *
     */
    public function emailchainwithdisplayAction()
    {
        // находим все родительские е-мейлы
        $parentEmails = \Crm\Models\Email::find([['inReplyTo' => NULL]]);

        var_dump(count($parentEmails));
        $emailRFCIds = [];
        foreach ($parentEmails as $email) {
            $emailRFCIds[] = $email->messageId;
        }

        // этот цикл можно вложить в пред цикл, но в целях исследования - отдельно, пока шо
        foreach ($emailRFCIds as $emailId) {
            var_dump($emailId);
            $childrens = \Crm\Models\Email::find([['references' => ['$regex' => $emailId]]]);

            if (!count($childrens)) continue;
            //подготовка данных к передаче шаблонизатору для рендеринга

            $emails = [];


            foreach ($childrens as $child) {
                $emailData = [];
                $dataAsArray = get_object_vars($child);

                foreach ($mapping as $v) {
                    if (array_key_exists($v, $dataAsArray))
                        $emailData[$v] = $dataAsArray[$v];
                    if (array_key_exists($v, $transform)) {
                        $emailData[$v] = $transform[$v] ($emailData[$v]);
                    }
                }
                var_dump($emailData);

            }
        }
    }

    public function trtrAction()
    {
        $emailRFCIds = $preparedParents = [];

        $parentEmails = \Crm\Models\Email::find([['inReplyTo' => NULL]]);

        foreach ($parentEmails as $email) {
            $emailRFCIds[] = $email->messageId;
            $preparedParents[$email->messageId] = $email;
        }

        // Сюда заполняем е- мейлы последовательно
        // parent
        // .. child
        // .. child
        //  ...
        // parent
        // ...
        $emailSeries = [];

        foreach ($emailRFCIds as $emailId) {

            $rootNode = $this->mapEmailToCommonView($preparedParents[$emailId]);
            $emailSeries[] = $preparedParents[$emailId];
            // приводим к виду, удобо- варимому к чтению шаблонизатором
            //var_dump($rootNode);
            // BAD value of emailId
            if (!$emailId) {
                continue;
            }
            var_dump($emailId);

            $preparedRoot = $this->mapEmailToCommonView($preparedParents[$emailId]);

            var_dump($preparedRoot);

            //$childrens = [];
            $childrens = \Crm\Models\Email::find([['references' => ['$regex' => $emailId]]]);

            if (!count($childrens)) continue;

            echo '&nbsp;&nbsp;&nbsp;&nbsp;>>>>>>>>>>>';
            foreach ($childrens as $child) {

                $emailSeries[] = $child;

                $child = $this->mapEmailToCommonView($child);

                var_dump($child);
            }

            echo '-----------------------------------------<br/><br/>';
        }

        // Складируем это в массив -> загоняем во View
        //  (как оно сейчас сделанно )
        //
        //
    }

    private function mapEmailToCommonView($emailStructure)
    {
        $mapping = [
            '_id',
            'messageId',
            'date',
            'mailDate',
            'subject',
            'fromAddress',
            'messageId',
            'inReplyTo'
        ];
        $transform = [
            '_id' => function ($v) {
                return strval($v);
            }
        ];

        $dataAsArray = get_object_vars($emailStructure);
        $emailData = [];

        foreach ($mapping as $v) {
            if (array_key_exists($v, $dataAsArray))
                $emailData[$v] = $dataAsArray[$v];
            if (array_key_exists($v, $transform)) {
                $emailData[$v] = $transform[$v] ($emailData[$v]);
            }
        }

        return $emailData;
    }

    private function updateEmailProperties($messageId, $properties)
    {
        $messageId = htmlspecialchars_decode($messageId);
        array_walk($properties, function (&$v) {
            $v = htmlspecialchars_decode($v);
        });
        $email = \Crm\Models\Email::find([["messageId" => $messageId]]);
        $email = $email[0];
        if (!$email) return false;

        foreach ($properties as $k => $v) {
            $email->{$k} = $v;
        }
        if (!$email->save()) return false;
        return true;
    }

    private function getAggregated()
    {
        $threadData = \Crm\Models\Email::aggregate(
            [
                [
                    '$group' => [
                        '_id' => ['inReplyTo' => '$inReplyTo'],
                        'emailIds' => ['$push' => '$messageId'],
                    ],
                ],
            ]
        );

        if (($threadData['ok'] == 1)) {
            foreach ($threadData['result'] as $k => $v) {
                if (empty($v['_id']['inReplyTo'])) unset($threadData['result'][$k]);
            }
        }
        return ($threadData['ok'] == 1) ? $threadData['result'] : [];
    }


    /**
     * Функция строит thread - Перенос, ур- нь тасков, для - Е-мейЛ
     * @throws Exception
     */
    public function threadAction()
    {
        $threadData = $this->getAggregated();
        echo '<pre>';

        foreach ($threadData as $k => $v) {
            array_walk_recursive($v, function (&$v) {
                $v = htmlspecialchars($v);
            });
            if ($v['_id']['inReplyTo']) {
                // проверяем цепочку. Наличие - предка
                $isLocalParent = $this->checkIfNotExistsByEmailId($v['_id']['inReplyTo']);
                if ($isLocalParent) {
                    $newRootElementId = array_shift($v['emailIds']);

                    $this->updateEmailProperties($newRootElementId, [
                        'inReplyTo' => $newRootElementId,
                        'isLocalParent' => true,
                        'originalParentId' => $v['_id']['inReplyTo']
                    ]);

                    foreach ($v['emailIds'] as $v2) {
                        $this->updateEmailProperties($v2, ['inReplyTo' => $newRootElementId]);
                    }
                }
            }
        }

        // Строим цепочку
        // построенное наново цепи
        $data = $this->getAggregated();
        foreach ($data as $threadData) {
            if ($v['_id']['inReplyTo']) {
                $this->buildThread($threadData);
            }
        }

        // Обработка списка родительских элементов
        echo '</pre>';
    }

    private function checkIfNotExistsByEmailId($emailId)
    {
        return empty(\Crm\Models\Email::find([['messageId' => $emailId]]));
    }

    private function buildThread($threadData)
    {
        $parentId = array_shift($threadData['emailIds']);

        $t = \Crm\Models\MailThreads::find([['parentId' => $parentId]]);
        if (!empty($t)) {
            $t = $t[0];
        } else {
            $t = new \Crm\Models\MailThreads();
            $t->parentId = $parentId;
        }

        $t->childrens = $threadData['emailIds'];
        if (!$t->save()) {
            throw new \Exception('Error while building thread');
        }
    }

    public function loadThreadsAction()
    {
        $threads = \Crm\Models\MailThreads::find([[]]);

        foreach ($threads as $msg) {
            // Нашли parent
            $rootMsg = $this->getEmail($msg->parentId);
            // Нашли остальную переписку


        }
    }

    // Это все работает при условии присутствия всех Е- мейлов
    // А, ну да, еще - на отправление

    // .. ---


    private function getEmail($childEmailId)
    {
        $email = \Crm\Models\Email::find([['messageId' => $childEmailId]]);

        return ($email) ? $email->toArray() : null;
    }

    public function getMailThreadsListTestAction()
    {
        $mails = \Crm\Helpers\EmailsHelper::getThreadsAction();

        $mapped = \Crm\Helpers\EmailsHelper::mapEmails($mails);
        //ready for output
        var_dump($mapped);
    }

    public function getMailThreadTestAction()
    {
        $mails = \Crm\Helpers\EmailsHelper::getThreadsAction();

        $mail = $mails[3];
        $parentId = $mail->messageId;

        $emailThread = \Crm\Helpers\EmailsHelper::getThread($parentId);

        $mapped = \Crm\Helpers\EmailsHelper::mapChildrenEmails($emailThread);

        var_dump($mapped);
    }
}