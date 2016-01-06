<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 06.11.15
 * Time: 12:50
 */

namespace Crm\Models;

/**
 * Class MailThreads
 *   Все письма, как входящие, так и исходящие, выстроены, в виде последовательности (идут,
 *     один за одним. )
 *   Тогда, отсылка е- мейла, происходит след. образом - он, отсылается в пулл (мейл- модуля )
 *    У него, оригинал, к которому он приписан, идет письмо верхнего уровня (хотя, не совсем корректно ).
 *          Должно быть - в references
 *  Оно, добавилось в конец цепочки
 *   Так же, записалось в список е- мейлов
 *     Выдача - забрали инфу по письмам (начиная с родителя)
 * @package Crm\Models
 */
class MailThreads extends CollectionBase
{
    public $_id;

    public $parentId;

    /**
     * Pull of object Ids linked to parent
     * @var array
     */
    public $childrens = [];
}