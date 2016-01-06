<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.03.15
 * Time: 17:22
 */

namespace Crm\Mail;

use Crm\Models;


class MailReceiver implements IMailReceiver
{
    function __construct() {
        $config = include __DIR__ . "/../../config/config.php";
        date_default_timezone_set($config->application->timezone);
    }

    /**
     * Return last mail
     *
     * @param string $imap_server
     * @param string $imap_login
     * @param string $imap_pass
     * @param integer $uid
     *
     * @return array $message
     */
    public function getLastMail()
    {
        $config = include __DIR__ . "/../../config/config.php";
        $criteria = 'SINCE ' . date("d-M-Y", strtotime('-75 day'));

        $box = array();
        $box['mailbox'] = $config->mailer->mailbox;
        $box['email'] = $config->mailer->email;
        $box['username'] = $config->mailer->username;
        $box['password'] = $config->mailer->password;

        $boxList = array();
        $boxList[] = $box;

        foreach ($boxList as $box) {
            $mbox = imap_open($box['mailbox'], $box['username'], $box['password']) or die("Error: can't connect: " . imap_last_error());
            $msg_uid_list = imap_search($mbox, $criteria, SE_UID);

            $mailMongoArray =  Models\Mail::find(array(
                array(
                    "email" => $box['email'],
                    'uid' => array('$in' => $msg_uid_list),
                ),
                "sort" => array("uid" => -1)
            ));
            $mailExclude = array();
            foreach ($mailMongoArray as $mailMongo) {
                $mailExclude[] = $mailMongo->uid;
            }

            foreach ($msg_uid_list as $msg_uid) {

                if (in_array($msg_uid, $mailExclude)){
                    continue;
                } else {
                    $mailMongo = new Models\Mail();
                }

                $mail = $this->getOneMail($mbox, $msg_uid);
                $mailMongo->uid = (int)$msg_uid;
                $mailMongo->message_id = $mail['message_id'];
                $mailMongo->subject = $mail['subject'];
                $mailMongo->from_name = $mail['from_name'];
                $mailMongo->from_email = $mail['from_email'];
                $mailMongo->to_email = $mail['to_email'];
                $mailMongo->email = $box['email'];
                $mailMongo->date = new \MongoDate(strtotime($mail['date']));
                $mailMongo->body = $mail['body'];

                if ($mail['attachments']) {
                    $mailMongo->attachments = count($mail['attachments']);
                } else {
                    $mailMongo->attachments = 0;
                }
                if (isset($mail['attachments_dir'])) {
                    $mailMongo->attachments_dir = $mail['attachments_dir'];
                }

                $in_charset = mb_detect_encoding($mailMongo->subject);
                $mailMongo->subject = mb_convert_encoding($mailMongo->subject, 'UTF-8', $in_charset);
                $in_charset = mb_detect_encoding($mailMongo->body);
                $mailMongo->body = mb_convert_encoding($mailMongo->body, 'UTF-8', $in_charset);
                $mailMongo->save();
            }
        }
        return true;
    }

    /**
     * Return one mail
     *
     * @param string $imap_server
     * @param string $imap_login
     * @param string $imap_pass
     * @param integer $uid
     *
     * @return array $message
     */
    public function getOneMail ($mbox, $uid)
    {
        $message = array();

        $hText = imap_fetchbody($mbox, $uid, '0', FT_UID);
        $header = imap_rfc822_parse_headers($hText);
        $structure = imap_fetchstructure($mbox, $uid, FT_UID);

        $message['subject'] = $this->text_decode(isset($header->subject) ? $header->subject : '');
        $message['from_name'] = (isset($header->from[0]->personal) ? $this->text_decode($header->from[0]->personal) : '');
        $message['from_email'] = $header->from[0]->mailbox . '@' . $header->from[0]->host;
        $message['to_email'] = $header->to[0]->mailbox . '@' . $header->to[0]->host;
        $message['date'] = $header->date;
        $message['message_id'] = $header->message_id;

        $part_array = $this->create_part_array($structure);

        if ($part_info = $this->get_part_info_by_subtype($part_array, 'HTML')) {
            $message['body'] = $this->mime_encode($this->decode($part_info['encoding'], imap_fetchbody($mbox, $uid, $part_info['part_number'], FT_UID)), $part_info['parameters']);
        } elseif ($part_info = $this->get_part_info_by_subtype($part_array, 'PLAIN')) {
            $message['body'] = $this->mime_encode($this->decode($part_info['encoding'], imap_fetchbody($mbox, $uid, $part_info['part_number'], FT_UID)), $part_info['parameters']);
            $message['body'] = $this->format_text_message($message['body']);
        }

        $message['attachments'] = $this->get_attachments($part_array);

        if ($message['attachments']) {
            $message['attachments_dir'] = $this->insert_attachments_mailbox($mbox, $uid, $message);
        }

        $message = $this->protect_html_text($message);
        return $message;
    }

    /**
     * Combines the functions of imap_open and imap_search
     *
     * @param string $imap_server
     * @param string $imap_login
     * @param string $imap_pass
     * @param string $criteria - from php function imap_search
     *
     * @return array $mailList
     */
    public function getMailListOneBox ($imap_mailbox, $imap_login, $imap_pass, $criteria)
    {
        $mailList = array();

        $mbox = imap_open($imap_mailbox, $imap_login, $imap_pass) or die("Error: can't connect: " . imap_last_error());
        if ($msg_uid_list = imap_search($mbox, $criteria)) {
            foreach ($msg_uid_list as $uid) {
                $message = array();

                $hText = imap_fetchbody($mbox, $uid, '0', FT_UID);
                $header = imap_rfc822_parse_headers($hText);
                $structure = imap_fetchstructure($mbox, $uid, FT_UID);

                $message['subject'] = $this->text_decode(isset($header->subject) ? $header->subject : '');
                $message['from_name'] = (isset($header->from[0]->personal) ? $this->text_decode($header->from[0]->personal) : '');
                $message['from_email'] = $header->from[0]->mailbox . '@' . $header->from[0]->host;
                $message['to_email'] = $header->to[0]->mailbox . '@' . $header->to[0]->host;
                $message['date'] = $header->date;
                $message['message_id'] = $header->message_id;

                $part_array = $this->create_part_array($structure);

                if ($part_info = $this->get_part_info_by_subtype($part_array, 'HTML')) {
                    $message['body'] = $this->mime_encode($this->decode($part_info['encoding'], imap_fetchbody($mbox, $uid, $part_info['part_number'], FT_UID)), $part_info['parameters']);
                } elseif ($part_info = $this->get_part_info_by_subtype($part_array, 'PLAIN')) {
                    $message['body'] = $this->mime_encode($this->decode($part_info['encoding'], imap_fetchbody($mbox, $uid, $part_info['part_number'], FT_UID)), $part_info['parameters']);
                    $message['body'] = $this->format_text_message($message['body']);
                }

                $message['attachments'] = $this->get_attachments($part_array);

                if ($message['attachments']) {
                    $message['attachments_dir'] = $this->insert_attachments_mailbox($mbox, $uid, $message);
                }


                $message = $this->protect_html_text($message);

                $mailList[] = $message;

            }
        }
        return $mailList;
    }

    public function text_decode($str)
    {
        $text = '';
        $charset = null;

        $text_array = imap_mime_header_decode($str);

        foreach ($text_array as $v) {
            $text .= rtrim($v->text, "\t");
            $charset = $v->charset;
        }

        if ($charset == 'default') {
            $charset = 'UTF-8';
        }

        return $this->mime_encode($text, '', $charset);
    }

    public function decode($encoding, $text)
    {
        switch ($encoding) {

            case 1:
                $text = imap_8bit($text);
                break;
            case 2:
                $text = imap_binary($text);
                break;
            case 3:
                $text = imap_base64($text);
                break;
            case 4:
                $text = imap_qprint($text);
                break;
            case 5:
            default:
                $text = $text;
        }

        return $text;
    }

    public function create_part_array($struct)
    {
        if (isset($struct->parts)){
            if (sizeof($struct->parts) > 0) {    // There some sub parts
                foreach ($struct->parts as $count => $part) {
                    $this->add_part_to_array($part, ($count + 1), $part_array);
                }

            } else {    // Email does not have a seperate mime attachment for text
                $part_array[] = array('part_number' => '1', 'part_object' => $struct);
            }
        } else {
            $part_array[] = array('part_number' => '1', 'part_object' => $struct);
        }


        return $part_array;
    }

    public function add_part_to_array($obj, $partno, & $part_array)
    {
        $part_array[] = array('part_number' => $partno, 'part_object' => $obj);
        if ($obj->type == 2) { // Check to see if the part is an attached email message, as in the RFC-822 type

            if (sizeof($obj->parts) > 0) {    // Check to see if the email has parts
                foreach ($obj->parts as $count => $part) {
                    // Iterate here again to compensate for the broken way that imap_fetchbody() handles attachments
                    if (sizeof($part->parts) > 0) {
                        foreach ($part->parts as $count2 => $part2) {
                            $this->add_part_to_array($part2, $partno . "." . ($count2 + 1), $part_array);
                        }
                    } else {    // Attached email does not have a seperate mime attachment for text
                        $part_array[] = array('part_number' => $partno . '.' . ($count + 1), 'part_object' => $obj);
                    }
                }
            } else {    // Not sure if this is possible
                $part_array[] = array('part_number' => $prefix . '.1', 'part_object' => $obj);
            }
        } else {
            if (property_exists($obj, 'parts') && sizeof($obj->parts) > 0) {
                foreach ($obj->parts as $count => $p) {
                    $this->add_part_to_array($p, $partno . "." . ($count + 1), $part_array);
                }
            }
        }
    }

    public function get_part_info_by_subtype($part_array, $subtype)
    {
        foreach ($part_array as $value) {
            if ($value['part_object']->subtype == $subtype) {
                return array('part_number' => $value['part_number'],
                    'encoding' => $value['part_object']->encoding,
                    'parameters' => $value['part_object']->parameters
                );
            }
        }

        return false;
    }

    public function get_attachments($part_array)
    {
        reset($part_array);

        $attachments = array();

        foreach ($part_array as $value) {
            if ($value['part_object']->ifdparameters == '1'
                && $value['part_object']->dparameters[0]->value
            ) {
                $attachments[] = array('part_number' => $value['part_number'],
                    'encoding' => $value['part_object']->encoding,
                    'filename' => $value['part_object']->dparameters[0]->value);
            }

            if (($value['part_object']->subtype == 'PNG'
                    or $value['part_object']->subtype == 'JPEG'
                    or $value['part_object']->subtype == 'GIF'
                    or $value['part_object']->subtype == 'BMP')
                and $value['part_object']->ifdparameters == '0'
            ) {
                $attachments[] = array('part_number' => $value['part_number'],
                    'encoding' => $value['part_object']->encoding,
                    'filename' => $value['part_object']->parameters[0]->value,
                    'id' => str_replace(array('<', '>'), '', $value['part_object']->id)
                );
            }
        }

        if (sizeof($attachments) > 0) {
            return $attachments;
        } else {
            return false;
        }
    }

    public function format_text_message($message)
    {
        $text = '';
        $message_array = explode("\n", $message);

        foreach ($message_array as $v) {
            if (strlen(trim($v)) > 0) {
                $text .= $v . '<br>';
                $setbr = false;
            } else {
                $setbr = true;
                $text .= '<br>';
            }
        }

        return $text;
    }

    public function mime_encode($text, $parameters, $charset = null, $enc = 'utf-8')
    {
        $encodings = array('UTF-8', 'WINDOWS-1251', 'ISO-8859-5', 'ISO-8859-1', 'KOI8-R');

        if (is_array($parameters)) {
            foreach ($parameters as $v) {
                if ($v->attribute == 'charset') {
                    $charset = $v->value;
                }
            }
        }

        if (function_exists("iconv") and $text) {
            if ($charset)
                return quoted_printable_decode(iconv($charset, $enc . '//IGNORE', $text));
            elseif (function_exists("mb_detect_encoding"))
                return quoted_printable_decode(iconv(mb_detect_encoding($text, $encodings), $enc, $text));
        }

        return quoted_printable_decode(utf8_encode($text));
    }

    public function insert_attachments_mailbox($mbox, $msg_number, $message)
    {
        $date = date('Y-m-d H:i:s',strtotime($message['date']));
        $dirName = $date.' '.$message['from_email'];
        $config = include __DIR__ . "/../../../app/config/config.php";
        $attachmentsDir = $config->application->attachmentsDir;
        $dirPath = $attachmentsDir.$dirName;
        if (file_exists($dirPath)) {
            return $dirName;
        } else {
            mkdir($dirPath);
        }
        if (is_array($message['attachments'])) {
            $i = 0;
            foreach ($message['attachments'] as $v) {
                $filename = $v['filename'];
                if (file_exists($dirPath.'/'. $filename)) {
                    $i +=1;
                    $filename = $i . '-' . $filename;
                }
                $file_contnt = $this->decode($v['encoding'], imap_fetchbody($mbox, $msg_number, $v['part_number'], FT_UID));
                file_put_contents($dirPath.'/'. $filename, $file_contnt, FILE_TEXT | FILE_APPEND | LOCK_EX);
            }
        }
        return $dirName;
    }

    public function protect_html_text($message)
    {
        $tags_list = '<applet><button><del><iframe><ins><object>';
        $tags_list .= '<source><video>';
        $tags_list .= '<noscript><script>';
        $tags_list .= '<applet><embed><noembed><object><param>';
        $tags_list .= '<frame><frameset><iframe><noframes>';

        foreach ($message as $k => $v) {
            if ($k == 'subject' or $k == 'from_name' or $k == 'from_email' or $k == 'body')
                $message[$k] = $this->strip_only(trim($v), $tags_list);

        }

        return $message;
    }

    public function strip_only($str, $tags)
    {
        if (!is_array($tags)) {
            $tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
            if (end($tags) == '') array_pop($tags);
        }

        foreach ($tags as $tag) $str = preg_replace('#</?' . $tag . '[^>]*>#is', '', $str);

        $str = preg_replace("'<!--.*?-->'si", '', $str);

        return $str;
    }


// alternative getBody Email
    public function getBody($uid, $imap)
    {
        $body = $this->get_part($imap, $uid, "TEXT/HTML");
        // if HTML body is empty, try getting text body
        if ($body == "") {
            $body = $this->get_part($imap, $uid, "TEXT/PLAIN");
        }
        return $body;
    }

    public function get_part($imap, $uid, $mimetype, $structure = false, $partNumber = false)
    {
        if (!$structure) {
            $structure = imap_fetchstructure($imap, $uid, FT_UID);
        }
        if ($structure) {
            if ($mimetype == $this->get_mime_type($structure)) {
                if (!$partNumber) {
                    $partNumber = 1;
                }
                $text = imap_fetchbody($imap, $uid, $partNumber, FT_UID);
                switch ($structure->encoding) {
                    case 3:
                        return imap_base64($text);
                    case 4:
                        return imap_qprint($text);
                    default:
                        return $text;
                }
            }

            // multipart
            if ($structure->type == 1) {
                foreach ($structure->parts as $index => $subStruct) {
                    $prefix = "";
                    if ($partNumber) {
                        $prefix = $partNumber . ".";
                    }
                    $data = $this->get_part($imap, $uid, $mimetype, $subStruct, $prefix . ($index + 1));
                    if ($data) {
                        return $data;
                    }
                }
            }
        }
        return false;
    }

    public function get_mime_type($structure)
    {
        $primaryMimetype = ["TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER"];

        if ($structure->subtype) {
            return $primaryMimetype[(int)$structure->type] . "/" . $structure->subtype;
        }
        return "TEXT/PLAIN";
    }

} 