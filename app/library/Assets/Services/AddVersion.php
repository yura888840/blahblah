<?php

namespace Crm\Assets\Services;
/**
 * Created by PhpStorm.
 * User: Kostja
 * Date: 18.08.15
 * Time: 12:35
 */

use Phalcon\Assets\FilterInterface;

class AddVersion
{
    static function run($assets)
    {
        $collections = $assets->getCollections();
        foreach ($collections as $collection) {
            $resources = $collection->getResources ();
            foreach ($resources as $resource) {
                if ($resource->getLocal()) {
                    $fPath = $resource->getPath();
                    $suffix = '_version_'.date ("Y-m-d_H-i-s", filemtime($fPath));
                    if (substr($fPath, -3)=='.js') {
                        $fPath = substr($fPath, 0, -3).$suffix.substr($fPath, -3);
                    } else {
                        $fPath = substr($fPath, 0, -4).$suffix.substr($fPath, -4);
                    }
                    $resource->setPath($fPath);
                }
            }
        }
        return true;
    }
}