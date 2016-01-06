<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 22.10.15
 * Time: 11:22
 */

namespace Crm\models;

class Email extends CollectionBase
{
    /**
     * Mapping between mailfetcher module and model/ collection fields
     * @var array
     */
    public static $mapping = [
        'message_id' => 'messageId',
        'in_reply_to' => 'inReplyTo',
        'MailDate' => 'mailDate',
        'fromaddress' => 'fromAddress',
        'toaddress' => 'toAddress',
    ];

    /**
     * Сигнализирует о том, что е- мейл из системы
     *   Тогда вопрос - как построить цепочки ?
     * @var bool
     */
    public $isInSent = false;

    /**
     * Flag that tells us if it sets that :
     *  is child in mail thread but parent in local
     * @var bool
     */
    public $isLocalParent = false;

    public $originalParentId = NULL;

    /**
     * Internal id
     * @var string
     */
    public $_id;

    // Headers
    public $messageId;

    /**
     * Chains of ids for emails
     * @var string needs to be parsed to get chain of emails
     */
    public $inReplyTo;

    public $references;

    public $mailDate;

    public $date;

    public $subject;

    public $toAddress;

    public $fromAddress;

    // Body
    public $body;

    public $attachments;
}