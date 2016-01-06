<?php

/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 22.06.15
 * Time: 14:10
 */
class ApiReplyTemplatesController extends ControllerBase
{

    public function savetemplateAction()
    {
        $this->view->disable();

        $inVarNames = ['name', 'body'];

        if ($this->request->isPost() == true
            && $this->request->isAjax()
            && array_intersect(array_keys($this->request->getPost()), $inVarNames) == $inVarNames
        ) {

            $sanitizedName = htmlspecialchars(trim(preg_replace('/\s\s+/', ' ', $this->request->getPost('name'))));
            $sanitizedBody = htmlspecialchars(trim(preg_replace('/\s\s+/', ' ', $this->request->getPost('body'))));

            $id = $this->dispatcher->getParam(0);

            if (!$id) {
                $m = new \Crm\Models\Replytemplates;
            } else {
                try {
                    $m = \Crm\Models\Replytemplates::findById($id);
                } catch (\Exception $e) {
                    $m = new \Crm\Models\Replytemplates;
                }
            }


            $m->name = $sanitizedName;
            $m->body = $sanitizedBody;

            $success = false;
            $msg = 'Error saving template';

            if ($m->save()) {
                $success = true;
                $msg = 'Successfully saved';
            }

            header("Content-type: application/json");
            echo json_encode(['success' => $success, 'msg' => $msg]);
        } else {
            echo "This end- point accepts only AJAX- requests";
        }

    }


    public function replyTemplatesAction()
    {
        $this->view->disable();

        if ($this->request->isGet() && $this->request->isAjax()) {

            $q = $this->request->getQuery();

            $term = trim(preg_replace('/\s\s+/', ' ', $q['term']));

            // replace multiple spaces with one
            $term = preg_replace('/\s+/', ' ', $term);

            $a_json_invalid = array(array("id" => "#", "value" => $term, "label" => "Only letters and digits are permitted..."));
            $json_invalid = json_encode($a_json_invalid);

            // SECURITY HOLE ***************************************************************
            // allow space, any unicode letter and digit, underscore and dash
            if (preg_match("/[^\040\pL\pN_-]/u", $term)) {
                print $json_invalid;
                exit;
            }
            // *****************************************************************************

            $a_json = array();
            $a_json_row = array();

            $t = \Crm\Models\Replytemplates::find([[]]);

            foreach ($t as $k => $row) {
                $row = $row->toArray();
                $a_json_row["id"] = strval($row['_id']);
                $a_json_row["value"] = $row['name'];
                $a_json_row["label"] = $row['name'];

                array_push($a_json, $a_json_row);
            }


            header('Content-type: application/json');
            echo json_encode($a_json);

        } else {
            echo "This end- point accepts only AJAX- GET requests";
        }
    }

    public function loadtemplateAction()
    {
        $this->view->disable();

        if ($this->request->isGet() /*&& $this->request->isAjax()*/) {

            $id = $this->dispatcher->getParam(0);

            $tpl = '';
            try {
                $tpl = \Crm\Models\Replytemplates::findById($id);
            } catch (\Exception $e) {

            }

            $success = true;
            $items = [];
            $msg = false;

            if (!$tpl) {
                $success = false;
                $msg = 'No template associated with ID given';
            } else {
                $items[] = [
                    'id' => $tpl->_id,
                    'name' => $tpl->name,
                    'body' => htmlspecialchars_decode($tpl->body),
                ];
            }

            header('Content-type: application/json');
            echo json_encode(['success' => $success, 'items' => $items, 'msg' => $msg]);

        } else {
            echo "This end- point accepts only AJAX- GET requests";
        }
    }


}