<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 03.07.15
 * Time: 13:13
 */

namespace Crm\Helpers;


class FilesService
{

    const DS = '/';

    public static function getInfo(\Phalcon\Http\Request\File $fileRequest)
    {

        $fileName = $fileRequest->getName();

        $arr = explode('.', $fileName);

        $ext = array_pop($arr);

        $hash = sha1(time() . $fileName);

        $uniqName = $hash . '.' . $ext;

        $fileInfo = [
            'hash' => $hash,
            'originalName' => $fileName,
            'uniqName' => $uniqName,
            'ext' => $ext,
            'type' => $fileRequest->getRealType(),
            'size' => $fileRequest->getSize(),
            'created' => time(),
        ];

        return $fileInfo;
    }

    public static function createFolder($folderPath)
    {

        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
    }


    public static function moveToTmp($uniqName, $fileRequest)
    {
        $tmpFolder = \Phalcon\DI::getDefault()->get('config')->uploadFiles['tmp'];

        self::createFolder($tmpFolder);

        $fileRequest->moveTo($tmpFolder . self::DS . $uniqName);
    }

    // Functions to move file from temp folder to new one
    public static function moveToFileStructure($filename)
    {
        $nameSource = \Phalcon\DI::getDefault()->get('config')->uploadFiles['tmp'] . self::DS . $filename;
        $nameDest = self::createNestedCatalogs() . $filename;

        rename($nameSource, \Phalcon\DI::getDefault()->get('config')->tickets['attachmentsDir'] . self::DS . $nameDest);

        return $nameDest;
    }

    private static function createNestedCatalogs()
    {
        $subDir = substr(md5(microtime()), mt_rand(0, 30), 2) . self::DS . substr(md5(microtime()), mt_rand(0, 30), 2);

        $basePath = \Phalcon\DI::getDefault()->get('config')->tickets['attachmentsDir'];

        $dir = $subDir . self::DS;

        // try ...catch for - if can't write to FS
        mkdir($basePath . self::DS . $dir, 0777, true);

        return $dir;
    }


}