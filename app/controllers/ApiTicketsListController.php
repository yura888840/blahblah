<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 26.06.15
 * Time: 17:15
 */

use Crm\Helpers\AjaxTablesorter;

class ApiTicketsListController extends ControllerBase
{
    private $tablesorter;

    public function ajaxGetTicketTableAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();
        if ($request->isGet() == true /*&& $request->isAjax() == true*/) {

            // ага, это некий маппинг, того, что выбирается из БД, по таскам, на
            //   - рядки, tablesorter' а
            $dbSelectors = ['&nbsp', 'subject', 'priority', 'type', 'department', 'status', 'created', '&nbsp&nbsp'];

            // $recieve = request
            $query = $request->getQuery();

            $limit = array_key_exists('size', $query) ? $query['size'] : 100;
            $skip = array_key_exists('page', $query) ? $query['page'] * $limit : 0;
            $sort = array_key_exists('column', $query) ? $query['column'] : [6 => 0];
            $filters = array_key_exists('column', $query) ? $query['filter'] : NULL;

            $this->tablesorter = new AjaxTablesorter();

            // на входе - переменная из request - column
            /// $dbSelectors - предопределенный массив, чего- то,

            // это какая- то отдельная сущность, этот фнкционал
            $dbSort = $this->tablesorter->createDbSort($sort, $dbSelectors);
            $dbFilters = $this->tablesorter->createMongoDbFilters($filters, $dbSelectors);

            // Тут надо подсчитать, что, сколько чего - в отображение. С учетом, каких- то - dbSelector- ов
            // сколько е- мейлов, сколько - тасков
            $dbData = \Crm\Models\Tickets::Find(
                array(
                    $dbFilters,
                    "sort" => $dbSort,
                    "skip" => $skip,
                    "limit" => $limit,
                )
            );

            $e = array(
                $dbFilters,
                "sort" => $dbSort,
                "skip" => $skip,
                "limit" => $limit,
            );

            /*
             $unpreparedEmails = \Crm\Models\Email::Find(
                array(
                    $dbFilters,
                    "sort" => $dbSort,
                    "skip" => $skip,
                    "limit" => $limit,
                )
            );
            */

            /*            $unpreparedEmails = \Crm\Helpers\EmailsHelper::emailStub();
                        $mappedEmails = \Crm\Helpers\EmailsHelper::mapEmails($unpreparedEmails);
            */
            $mails = \Crm\Helpers\EmailsHelper::getThreadsAction();
            $mappedEmails = \Crm\Helpers\EmailsHelper::mapEmails($mails);
            //@todo подсчет кол- ва е- мейлов, и модификация параметров выборки
            $dbData = array_merge($mappedEmails, $dbData);

            /// Все обработки переносим на уровень этого tablesorter Object (писанный Гудаковым Александром)
            $this->prepareResult($dbData, $dbFilters, $dbSelectors);

            header("Content-type: Application/json");

            // тут скорей всего некая сериализация происходит объекта
            echo json_encode($this->tablesorter);
        }
    }

    // на входе переменные $dbFilters, $dbSelectors

    // $data - то, над чем производят, происходят манИпуляции
    //// available values for $type is : ticket, email
    private function prepareResult($data, $dbFilters, $dbSelectors, $type = "ticket")
    {
        $this->tablesorter->headers = ['&nbsp', 'Subject', 'Priority', 'Type', 'Department', 'Status', 'Last Modify', '&nbsp&nbsp'];
        $this->tablesorter->total_rows = \Crm\Models\Tickets::Count([$dbFilters]);
        // debug section to obtain formats & transforms
        /*
                echo "<pre>";
                var_dump(get_object_vars($this->tablesorter));

                var_dump($dbSelectors);

                var_dump($data);
                die();*/
        $this->tablesorter->rows = $this->tablesorter->tdStandartWrapper($data, $this->tablesorter->headers, $dbSelectors, 'Subject', 'Last Modify');

        // внешняя переменная - tablesorter
        // внутренние переменные , этого, данного цикла
        // $k, $v, $k2, $v2

        // костыльный код - потом поДправить - поправить
        foreach ($this->tablesorter->rows as $k => $v) {
            foreach ($v as $k2 => $v2) {

                $formedValue = NULL;
                switch ($k2) {
                    case 'Subject':
                        $ticket = array();
                        $ticket['subject'] =
                            ((property_exists($data[$k], 'is_email') && $data[$k]->is_child) ? '<i class="glyphicon glyphicon-share-alt"></i>' : '')
                            . $v2;
                        $ticket['id'] = (string)$data[$k]->_id;
                        $ticket['is_reply'] = (property_exists($data[$k], 'is_email') && $data[$k]->is_child) ? true : false;
                        $formedValue = json_encode($ticket);

                        break;
                    case '&nbsp&nbsp':

                        $formedValue =

                            ((property_exists($data[$k], 'is_email') && $data[$k]->is_email)
                                ? '<div class="table-crm-btn not-touch-me text-right text-nowrap">'
                                // create task from email
                                . '<a class="btn btn-sm" ajax-tablesorter-element-id="' . $data[$k]->_id
                                . '"  id="ajax-createtask-tablesorter">Create task &nbsp;&nbsp;'
                                . '<i class="glyphicon glyphicon-tasks">'
                                . '</i></a>'
                                : '')

                            // Open chain of e-mails
                            /*
                            . '<a class="btn btn-sm" ajax-tablesorter-element-id="' . $data[$k]->_id
                            . '"  id="ajax-openfolder-tablesorter">Open chain of e-mails&nbsp;&nbsp;'
                            . '<i class="glyphicon glyphicon-folder-open">'
                            . '</i></a>'
                            */

                            // delete
                            . '<a class="btn btn-sm" ajax-tablesorter-element-id="' . $data[$k]->_id
                            . '"  id="ajax-delete-tablesorter"><i class="glyphicon glyphicon-remove"></i></a>'
                            . '</div>';
                        break;
                    default:
                }
                if ($formedValue) $this->tablesorter->rows[$k][$k2] = $formedValue;
            }
        }
    }

    public function ajaxGetFilterOptionsAction()
    {
        $this->view->disable();
        $model = new \Crm\Models\Tickets();
        $form = new \Crm\Forms\TicketEditForm($model);

        AjaxTablesorter::ajaxTableSelectOptions($form,
            [2 => 'priority',
                3 => 'type',
                4 => 'department',
                5 => 'status']);
    }

    /**
     * @todo Это нужно чуть переписать
     *   На входе - 1-н параметр. Если он указан - выставляем, устанавливаем условия выборки
     */
    public function ajaxGetPersonalTicketTableAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if ($request->isGet() == true /*&& $request->isAjax() == true*/) {

            $dbSelectors = ['&nbsp',
                'subject',
                'priority',
                'type',
                'department',
                'status',
                'created',
                '&nbsp&nbsp'];

            // $recieve = request
            $receive = $request->getQuery();
            $limit = $receive['size'];
            $skip = $limit * $receive['page'];
            $sort = $receive['column'];
            $filters = $receive['filter'];

            $tablesorter = new AjaxTablesorter();
            $dbSort = $tablesorter->createDbSort($sort, $dbSelectors);
            $dbFilters = $tablesorter->createMongoDbFilters($filters, $dbSelectors);

            $authData = $this->auth->getIdentity();

            $userId = (string)$authData["id"];

            if (isset($dbFilters['myPosition'])) {

                if ($dbFilters['myPosition'] == new MongoRegex("/Creator/")) {
                    $dbFilters['authorId'] = $authData["id"];
                    unset($dbFilters['myPosition']);

                } elseif ($dbFilters['myPosition'] == new MongoRegex("/Assigned/")) {
                    $dbFilters['assignTo'] = $userId;
                    unset($dbFilters['myPosition']);

                } elseif ($dbFilters['myPosition'] == new MongoRegex("/Watcher/")) {
                    $dbFilters['notify'] = $userId;
                    unset($dbFilters['myPosition']);
                }
            }

            $dbFilters['$or'] = array(array('authorId' => $authData["id"]),
                array('assignTo' => $userId),
                array('notify' => $userId));

            $dbData = \Crm\Models\Tickets::Find(

                array(
                    $dbFilters,
                    "sort" => $dbSort,
                    "skip" => $skip,
                    "limit" => $limit,
                )
            );

            $tablesorter->headers = ['&nbsp',
                'Subject',
                'Priority',
                'Type',
                'Department',
                'Status',
                'Date',
                '&nbsp&nbsp'];

            $tablesorter->total_rows = \Crm\Models\Tickets::Count([$dbFilters]);

            $tablesorter->rows = $tablesorter->tdStandartWrapper($dbData,
                $tablesorter->headers,
                $dbSelectors,
                'Subject',
                'Date');

            foreach ($tablesorter->rows as $k => $v) {
                foreach ($v as $k2 => $v2) {

                    if ($k2 == 'Subject') {
                        $ticket = array();
                        $ticket['subject'] = $v2;
                        $ticket['id'] = (string)$dbData[$k]->_id;
                        $tablesorter->rows[$k][$k2] = json_encode($ticket);

                    } elseif ($k2 == '&nbsp&nbsp') {

                        $tablesorter->rows[$k][$k2] = '<div class="table-crm-btn not-touch-me text-right text-nowrap">'
                            . '<a class="btn btn-sm" ajax-tablesorter-element-id="'
                            . $dbData[$k]->_id . '"  id="ajax-delete-tablesorter">'
                            . '<i class="glyphicon glyphicon glyphicon-remove"></i></a>'
                            . '</div>';
                    }

                }
            }

            header("Content-type: Application/json");
            echo json_encode($tablesorter);
        }
    }

    public function ajaxGetFilterPersonalOptionsAction()
    {

        $this->view->disable();
        $model = new Tickets();
        $form = new \Crm\Forms\TicketEditForm($model);

        AjaxTablesorter::ajaxTableSelectOptions($form, [0 => 'myPosition',
            2 => 'priority',
            3 => 'type',
            4 => 'status']);
    }

}