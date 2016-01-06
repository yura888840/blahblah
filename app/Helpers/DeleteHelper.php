<?php

namespace Crm\Helpers;

use Crm\Models\Trash;

class DeleteHelper {

    public static function moveToTrash($data, $parentCollection)
    {
        $trash = new Trash();
        foreach($data as $k => $v){
            $trash->$k = $v;
        }

        $trash -> trash_parent_collection = $parentCollection;

        if ($trash->save() && $data -> delete()) {
            $messagesArr['success'] = true;
            echo json_encode($messagesArr);
        }
    }

    public static function restoreFromTrash($id)
    {
        $docRestore = Trash::findById($id);
        if (!$docRestore) {
            $messagesArr['success'] = false;
            $messagesArr['message'] = "don't find the element in CRM";
            return $messagesArr;
        }
        $collectionName =  'Crm\Models\\'.$docRestore-> trash_parent_collection;
        if (!class_exists($collectionName)) {
            $messagesArr['success'] = false;
            $messagesArr['message'] = "collection not exists";
            return $messagesArr;
        }
        $collection = new $collectionName();
        foreach($docRestore as $k => $v){
            if (substr($k, 0, 6) != 'trash_') {
                $collection->$k = $v;
            }
        }
        if ($collection->save() && $docRestore->delete()) {
            $messagesArr['success'] = true;
            $messagesArr['message'] = "ok";
            return $messagesArr;
        }
    }

}