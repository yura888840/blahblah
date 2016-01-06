<?php
/**
 * Created by PhpStorm.
 * User: Konstantin
 * Date: 09.03.15
 * Time: 22:17
 */

use Phalcon\Mvc\View;
use Crm\Calendar\GoogleAPI;
use Crm\Models;
use Crm\Widget\WidgetValidation;
use Phalcon\Validation\Message;
use Crm\Models\WidgetsCustomSetUsers;
use Crm\Models\WidgetsCustomGridUsers;


class ApiController extends \Phalcon\Mvc\Controller
{
    public function chartDataAction()
    {
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);

        $widgetData = new Crm\Widget\ChartData();
		$widgetData->typeChart = 'lineChart';
		$data = $widgetData->getData(new \DataProvider());
		echo json_encode($data);
    }

    public function calendarAction()
    {

        $this->view->disable();
        $params = $this->request->getPost();

        $apiCalendar = new GoogleAPI($params);
        $apiCalendar->run();

        $data = $apiCalendar->getData();

        //header('Content-type: application/json');
        echo json_encode($data);

    }

    /**
     * Get list from collection
     *
     *@param string $collection
     *
     *
     *@param string $query
     *
     * Json example:
     *{"0":{"email":"testcrmcrmtest@gmail.com","uid":{"$in":[1,2,3,4]}},"sort":{"uid":-1},"limit":8}
     *
     * $queryArr = array(
     *   array(
     *   'email' => "testcrmcrmtest@gmail.com",
     *   'uid' => array('$in' => array(1,2,3)),
     *   ),
     *   "sort" => array("uid" => -1),
     *   "limit" => 8,
     *   );
     *
     * для передачи лат в условия запроса нужно к полю даты добавить префикс "mongo_date_"
     *
     *
     * @return string $jsonList
     */
    public function findDbAction($collection='', $query='')
    {
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);

        $rows = array();
        $mongoArray = false;

        if ( $query=='' ) {
            if (isset($_GET['collection'])) {
                $collection = $_GET['collection'];
            }
            if (isset($_GET['query'])) {
                $query = $_GET['query'];
            }
        }

        $model = 'Crm\Models\\'.$collection;
        $queryArray = json_decode($query, true);

        if (isset($queryArray[0])){
            foreach ($queryArray[0] as $key => &$value) {
                // для поиска по ИД и по списку ИД, список ИД передается строкой разделенной ","
                if ($key == '_id' && $value>'') { // если передали в условиях поиска ИД то его надо привести к "удобному" для Монги
                    if (strpos($value, ',') === false) {
                        $value = new MongoId($value);
                    } else {
                        $values = explode (',', $value);
                        foreach ($values as &$val) {
                            $val = new MongoId($val);
                        }
                        $value = array('$in' => $values);
                    }
                }
                if (substr($key, 0, 11) == 'mongo_date_') {
                    $fieldName = substr($key, 11);
                    if (is_array($value)) {//если услрвие для даты < или >
                        foreach ($value as $keyDate => &$valueDate) {
                            $valueDate = new MongoDate(strtotime($valueDate));
                            $queryArray[0][$fieldName] = $value;
                        }
                    } else {//если услрвие для даты простое равенство
                        $queryArray[0][$fieldName] = new MongoDate(strtotime($value));
                    }

                    unset($queryArray[0][$key]);
                }
            }
        }


        if ($queryArray) {
            if (class_exists($model)) {
                try {
                    $mongoArray = $model::find($queryArray);
                } catch (\Exception $e) {
//                    echo $e->getMessage();
                }
            }
            if ($mongoArray) {
                foreach ($mongoArray as $mongoOb) {
                    $row = array();
                    foreach ($mongoOb as $key => $value) {
                        if (substr($key, 0, 1)=='_' AND $key!='_id') {
                            continue;
                        }
                        if ($key=='_id'){
                            $value = (string)$value;
                        }
                        if (is_a($value, 'MongoDate')) {
                            $value = date('Y-m-d H:i:s', $value->sec);
                        }
                        $row[$key] = $value;
                    }
                    $rows[] = $row;
                }
            }
        }

        $resJson = json_encode($rows);
        echo $resJson;
    }

    /**
     * Save document to collection
     *
     * Request example:
     * /api/saveDb?collection=Tickets&fields={"id":"55b019d374f2dd6d078b456a","priority":2} - update
     *  or
     * /api/saveDb?collection=Tickets&fields={"priority":3,"status":"Reassign","type":"Task","name":"ks2@bk.ru",
     * "department":"financial","subject":"su1","description":"de2","authorName":"ks2@bk.ru",
     * "assignTo":"54eb928151be0bec9d8b4567","deadline":"07/30/2015"} - insert
     *
     * @return string $json
     */
    public function saveDbAction()
    {
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
        $res = false;
        try {
            if (isset($_GET['collection'])) {
                $collection = $_GET['collection'];
            }
            if (isset($_GET['fields'])) {
                $fieldsStr = $_GET['fields'];
            }

            $model = 'Crm\Models\\'.$collection;
            $fields = json_decode($fieldsStr, true);

            if ($fields) {
                if (class_exists($model)) {
                    if (isset($fields['id'])) {
                        $mongoOb = $model::findById($fields['id']);
                    } else {
                        $mongoOb = new $model;
                    }
                }
            }
            foreach ($fields as $key => $value) {
                if ($key != 'id') {
                    $mongoOb->$key = $value;
                }
            }
            $res = $mongoOb->save();
        } catch (\Exception $e) {
            echo json_encode($res);
        }
        echo json_encode($res);
    }

    public function widgetsAction(){
        $this->view->disable();
        $request = new \Phalcon\Http\Request();
        $id =  $request->getPost('id');

        if($request->isPost() == true && $request->isAjax() == true && $id) {
            $obj =  $request->getPost('obj');
            $validation = new WidgetValidation();
            $messages = $validation->validate($obj);
            if (count($messages)) {
                $messagesArr = [];
                foreach ($messages as  $message) {
                    $messagesArr[] = $message -> getMessage();
                }
                echo json_encode($messagesArr);
            }
            else{
                $messagesArr['success'] = true;
                echo json_encode($messagesArr);
            }
        }
    }

    /**
     * Блок отвечает за админку виджеты
     */
    public function widgetssaveAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if ($request->isPost() == true && $request->isAjax()) {

            $user = $this->auth->getName();
            $group = $this->auth->getProfile();

            $widgets = WidgetsCustomSetUsers::findFirst([['user' => $user]]);

            if (!$widgets) {
                $widgets = new WidgetsCustomSetUsers();

                $widgets->user = $user;
            }

            $widgets->grid = $request->getPost('obj');

            $success = ($widgets->save()) ? true : false;

            $widgetsGroup = WidgetsCustomSetGroups::findFirst([['group' => $group]]);

            if (!$widgetsGroup) {
                $widgetsGroup = new WidgetsCustomSetGroups();

                $widgetsGroup->group = $group;
            }

            $grid = $request->getPost('obj');

            if (!array_key_exists(1, $grid) && array_key_exists(1, $widgetsGroup->grid)) {
                $grid[1] = $widgetsGroup->grid[1];
            }

            if (!array_key_exists(2, $grid) && array_key_exists(2, $widgetsGroup->grid)) {
                $grid[2] = $widgetsGroup->grid[2];
            }

            $widgetsGroup->grid = $grid;

            $success = ($widgetsGroup->save()) ? true : false;

            echo json_encode(array('success' => $success, 'items' => []));
        } else {
            echo 'This end- point accepts only Ajax requests';
        }
    }

    public function widgetssaveforpanelAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if ($request->isPost() == true && $request->isAjax()) {
            $user = $this->auth->getName();

            $widgets = WidgetsCustomGridUsers::findFirst([['user' => $user]]);

            if (!$widgets) {
                $widgets = new WidgetsCustomGridUsers();

                $widgets->user = $user;

                $widgets->grid = [1 => [], 2 => []];
            }

            $panelData = $request->getPost('obj');
            $panelId = $request->getPost('id');

            if (!$panelId) {
                throw new \Exception('Not enough params in request. Usage: parametrs: id, obj');
            }

            if (empty($panelData)) {
                $panelData = [];
            }

            $origGrid = $widgets->grid[$panelId];
            $widgets->grid[$panelId] = [];

            foreach ($panelData as $k => $v) {
                $widgets->grid[$panelId][] = $v;
            }

            $success = ($widgets->save()) ? true : false;

            echo json_encode(array('success' => $success, 'items' => []));
        } else {
            echo 'This end- point accepts only Ajax requests';
        }
    }

    /**
     * Блок отвечает - за юзер- actions, с виджетами
     */
    public function profitProductsAction()
    {
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
        $request = new \Phalcon\Http\Request();

        $profitData = array();
        if($request->isGet() == true && $request->isAjax() == true){
            $receive = $request->getQuery();
            $profit = new \Crm\MongoSelect\ProfitProduct();
            $profitData = $profit->getData($receive);
        }
        echo json_encode($profitData);
    }

    public function profitProductOneAction()
    {
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
        $request = new \Phalcon\Http\Request();
        $profitData = array();
        if($request->isGet() == true && $request->isAjax() == true){
            $receive = $request->getQuery();
            $profit = new \Crm\MongoSelect\ProfitProduct();
            $profitData = $profit->getDataProductChannels($receive);
        }
        echo $profitData;
    }

    public function widgetToAddAction()
    {
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
        $widgetAdd = Crm\Widget\WidgetHelper::widgetsToAdd();
        echo json_encode($widgetAdd);
    }

    public function widgetAddGridAction()
    {
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
        $widgetAdd = Crm\Widget\WidgetHelper::widgetAddGrid();
        echo json_encode($widgetAdd);
    }

    public function widgetRemoveAction()
    {
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
        $widgetRemove = Crm\Widget\WidgetHelper::widgetRemove();
        echo json_encode($widgetRemove);
    }

}
