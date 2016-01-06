<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 25.05.15
 * Time: 13:20
 */

namespace Crm\Helpers;

use Crm\Models\Tickets;

class TicketsHelper
{
    public static function fixMissingAttachments(\Crm\Models\Tickets $ticket)
    {
        $ticketID = $ticket->_id;

        if ($ticket->attach) {
            $noExist = 0;
            foreach ($ticket->attach as $k => $v) {
                if (!is_file('./files/tickets/' . $ticketID . '/main/' . $ticket->attach[$k]['uniqName'])) {
                    unset($ticket->attach[$k]);
                    $noExist++;
                }
            }
            if ($noExist > 0) {
                if ($ticket->save()) {

                }
            }
        }
    }

    public static function getOptions(\Crm\Models\Tickets $ticket, \Crm\Forms\TicketEditForm $ticketForm)
    {
        $elOptions = [];

        $varsForView = [];

        $varsForView['assignToName'] = '';

        foreach (['priority', 'status', 'type', 'department', 'assignTo', 'notify[]'] as $v) {
            if ($v == 'notify[]') {
                $notifyName = '';
                $optString = '[';
                foreach ($ticketForm->getElementOptions($v) as $k2 => $v2) {
                    $optString .= "{id: '" . $k2 . "', text: '" . $v2 . "'}, ";
                    if ($ticket->notify) {
                        foreach ($ticket->notify as $nv) {
                            if ($nv == $k2) {
                                $notifyName .= $v2 . ', ';
                            }
                        }
                    }
                }
                $optString = $optString . ']';

                $varsForView['notifyName'] = $notifyName;
            } else {
                $optString = '{';
                foreach ($ticketForm->getElementOptions($v) as $k2 => $v2) {
                    $optString .= "'" . $k2 . "' : '" . $v2 . "', ";
                    if ($v == 'assignTo' && $k2 == $ticket->assignTo) {

                        $varsForView['assignToName'] = $v2;
                    }
                }
                $optString .= '}';
            }
            $elOptions[$v] = $optString;
        }

        return ['elOptions' => $elOptions, 'varsForView' => $varsForView];
    }

    public static function prepareTicketsList(array $tickets)
    {
        $preparedList = [];

        foreach ($tickets as $ticket) {
            if (!($ticket instanceof \Crm\Models\Tickets)) {
                throw new \Exception('Error in data type for prepareTicketsList');
            }
            $ticket = $ticket->toArray();

            $dt = DateTimeFormatter::format($ticket['created']);

            $preparedList[] =
                array(
                    '_id' => strval($ticket['_id']),
                    'subject' => $ticket['subject'],
                    'priority' => $ticket['priority'],
                    'type' => $ticket['type'],
                    'department' => $ticket['department'],
                    'created' => $dt['created_date1_text'],
                    'status_short' => substr(ucfirst($ticket['status']), 0, 1),
                    'status' => $ticket['status'],
                );
        }

        return $preparedList;
    }

    public static function countNew()
    {
        return count(Tickets::find([['status' => 'New']]));
    }

    public static function getShortNew()
    {
        $ticketsWithStatusNew = Tickets::find([['status' => 'New']]);

        $ticketsMapped = [];

        foreach ($ticketsWithStatusNew as $ticket) {
            $ticket = $ticket->toArray();

            $ticketsMapped[] = [
                '_id' => $ticket['_id'],
                'subject' => $ticket['subject'],
                'subject' => $ticket['subject'],
                'name' => $ticket['name'],
            ];
        }

        return $ticketsMapped;
    }

    public static function getExternalTicketAuthorData($ticketId)
    {
        $ticket = Tickets::findById($ticketId);
        if (!$ticket) {
            throw new \Exception('Data integrity violation on ticket');
        }

        if ($ticket->fromEmail) {
            return ['email' => $ticket->fromEmail, 'name' => $ticket->userName];
        }

        return false;
    }

    public static function buildTicketSettingsForm($ticket = NULL)
    {
        $users = \Crm\Helpers\UsersHelper::getPairsUsernameWithId();

        $elements = [
            [
                'classname' => 'Select',
                'elementParams' => \Crm\Helpers\DataSets::getValuesBy('status'),
                'elementParams1' => empty($ticket) ? [] : ['value' => $ticket->status, 'class' => 'form-control'],
                'element_id' => 'status',
                'label' => 'Status',
                'validators' => [
                    'PresenceOf' => ['message' => 'The status is required']
                ],
                'template' => 'viewedit/ticketsettings-lg-6',
            ],

            [
                'classname' => 'Select',
                'elementParams' => \Crm\Helpers\DataSets::getValuesBy('priority'),
                'elementParams1' => empty($ticket) ? [] : ['value' => $ticket->priority, 'class' => 'form-control'],
                'element_id' => 'priority',
                'label' => 'Priority',
                'validators' => [
                    'PresenceOf' => ['message' => 'The priority is required']
                ],
                'template' => 'viewedit/ticketsettings-lg-6',
            ],

            [
                'classname' => 'Select',
                'elementParams' => \Crm\Helpers\DataSets::getValuesBy('type'),
                'elementParams1' => empty($ticket) ? [] : ['value' => $ticket->type, 'class' => 'form-control'],
                'element_id' => 'type',
                'label' => 'Type',
                'validators' => [
                    'PresenceOf' => ['message' => 'The priority is required']
                ],
                'template' => 'viewedit/ticketsettings-lg-6',
            ],

            [
                'classname' => 'Select',
                'elementParams' => \Crm\Helpers\DataSets::getValuesBy('department'),
                'elementParams1' => empty($ticket) ? [] : ['value' => $ticket->department, 'class' => 'form-control'],
                'element_id' => 'department',
                'label' => 'Department',
                'validators' => [
                    'PresenceOf' => ['message' => 'The field is required']
                ],
                'template' => 'viewedit/ticketsettings-lg-6',
            ],
        ];

        if (\Crm\Helpers\SimplePermissions::canAssign()) {
            $elements[] = [
                'classname' => 'Select',
                'elementParams' => $users,
                'elementParams1' => empty($ticket) ? [] : ['value' => $ticket->assignTo, 'class' => 'form-control'],
                'element_id' => 'assign_to',
                'label' => 'Assign to',
                'validators' => [
                    'PresenceOf' => ['message' => 'The field is required']
                ],
                'template' => 'viewedit/ticketsettings-lg-12',
            ];
        }

        $elements = array_merge($elements,
            [
                [
                    'classname' => 'Select',
                    'elementParams' => $users,
                    'elementParams1' => empty($ticket)
                        ? ['multiple' => 'multiple']
                        : ['value' => $ticket->notify,
                            'multiple' => 'multiple',
                            'class' => 'form-control'
                        ],
                    'element_id' => 'watchers',
                    'label' => 'Watchers',
                    'validators' => [
                        'PresenceOf' => ['message' => 'The field is required']
                    ],
                    'template' => 'viewedit/ticketsettings-lg-12',
                ],

                [
                    'classname' => 'Text',
                    'elementParams' => [
                        'class' => 'form-control',
                        'data-table-id' => "ajax-table-date-deadline",
                        "data-column" => "deadline",
                        "data-toggle" => "dropdown"
                    ],
                    'element_id' => 'deadline',
                    'label' => 'Deadline',
                    'validators' => [
                        'PresenceOf' => ['message' => 'The field is required']
                    ],
                    'template' => 'viewedit/ticketsettings-calendar-lg-12',
                ],
            ]
        );

        return self::buildFormByParams($elements, $ticket);
    }

    public static function buildReplyOnTicketSubform($ticket = NULL)
    {
        $users = \Crm\Helpers\UsersHelper::getPairsUsernameWithId();

        $ticket->notify = !($ticket->notify) ? [] : $ticket->notify;

        $notifiers = array_merge($ticket->notify, self::getOutgoingRecepientEmail($ticket));

        $elements = [
            [
                'classname' => 'Select',
                'elementParams' => $users,
                'elementParams1' => array_merge(
                    (empty($ticket)
                        ? ['multiple' => 'multiple']
                        : ['value' => $notifiers, 'multiple' => 'multiple']),
                    ['class' => 'form-control', 'data-placeholder' => "Choose watchers..."]
                ),
                'element_id' => 'notifiers',
                'label' => 'Notifiers',
                'validators' => [
                    'PresenceOf' => ['message' => 'The status is required']
                ],
                'template' => 'viewedit/ticketsreplyform-public-note-notifiers',
            ],
            [
                'classname' => 'Text',
                'elementParams' => ['value' => "Re: " . $ticket->subject, 'class' => 'form-control'],
                'element_id' => 'subject',
                'label' => 'Subject',
                'validators' => [
                    'PresenceOf' => ['message' => 'The field is required']
                ],
                'template' => 'viewedit/ticketsreplyform-public-note-subject',
            ],
            [
                'classname' => 'TextArea',
                'elementParams' => [
                    'value' => "",
                    'class' => 'form-control textarea-resize-none ticket-textarea textarea-autosize',
                    'placeholder' => "Enter your public reply..."
                ],
                'element_id' => 'publicreply',
                'label' => 'Public reply',
                'validators' => [
                    //'PresenceOf' => ['message' => 'The field is required']
                ],
                'template' => 'viewedit/ticketsreplyform-public-note-reply',
            ],
            [
                'classname' => 'TextArea',
                'elementParams' => ['value' => "",
                    'class' => 'form-control textarea-resize-none ticket-textarea textarea-autosize',
                    'placeholder' => "Enter your private reply..."
                ],
                'element_id' => 'privatereply',
                'label' => 'Public reply',
                'validators' => [
                    //'PresenceOf' => ['message' => 'The field is required']
                ],
                'template' => 'viewedit/ticketsreplyform-private-note',
            ],
        ];

        return self::buildFormByParams($elements, $ticket);
    }

    private static function buildFormByParams($elements, $ticket)
    {
        $formFieldsList = [];
        $templates = [];

        foreach ($elements as $v) {
            if (!array_key_exists('element_id', $v))
                continue;

            $formFieldsList[] = $v['element_id'];
            $templates[$v['element_id']] =
                array_key_exists('template', $v)
                    ? $templates[$v['element_id']] = $v['template']
                    : 'default';
        }

        $form = new \Crm\Forms\FormBuilder($ticket, $elements);

        return ['formFieldlist' => $formFieldsList, 'form' => $form, 'templates' => $templates];
    }

    // this is stub
    public static function getOutgoingRecepientEmail($ticket)
    {
        return [];
    }
}