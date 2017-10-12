<?php
/**
 * Error messages from OpenStack
 *
 * @package mod_bigbluebuttonbn
 * @author  Carlos Mata Guzman (carlos.mataguzman [at] ucr.ac.cr)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
namespace mod_bigbluebuttonbn\openstack;
defined('MOODLE_INTERNAL') || die();
require_once dirname(dirname(__FILE__)) . '/interfaces/error_communicator.php';


class openstack_error_communicator implements error_communicator
{

    public function build_message($input, $type){
        $msg_data = new \stdClass();
        $message = "";

        switch ($type){
            case 'connection_error':
                $msg_data->log_id = $input['log_id'];
                $msg_data->error_message = $input['error_message'];
                $message = get_string('openstack_error_conection_message', 'bigbluebuttonbn', $msg_data);
                break;

            case 'creation_request_error':
            case 'first_creation_request_error':
                $msg_data->log_id = $input['log_id'];
                $msg_data->error_message = $input['error_message'];
                $msg_data->meetingid;
                $msg_data->courseid;
                $msg_data->openingtime;
                $message = get_string('openstack_error_creation_request_message', 'bigbluebuttonbn', $msg_data);
                break;

            case 'deletion_request_error':
                $msg_data->log_id = $input['log_id'];
                $msg_data->error_message = $input['error_message'];
                $msg_data->meeting_id;
                $msg_data->stack_name;
                $message = get_string('openstack_error_deletion_request_message', 'bigbluebuttonbn', $msg_data);
                break;
        }
        return $message;
    }


    public function communicate_error($message, $type){
        global $CFG;

        // Pre 2.9 does not have \core\message\message()
        if ($CFG->branch >= 29) {
            $data = new \core\message\message();
        } else {
            $data = new \stdClass();
        }

        switch ($type){
            case 'connection_error':
                $data = $this->communicate_connection_error($data, $message);
                break;
            case 'first_creation_request_error':
                $data = $this->communicate_creation_error($data, $message, true);
                break;
            case 'creation_request_error':
                $data = $this->communicate_creation_error($data, $message, false);
                break;
            case 'deletion_request_error':
                $data = $this->communicate_deletion_error($data,$message);
        }
        message_send($data);
    }

    private function communicate_connection_error($data, $message){

        $data->component         = 'mod_bigbluebuttonbn';
        $data->name              = 'openstack_conection_error'; // This is the message name from messages.php.
        $data->userfrom          = \core_user::get_noreply_user();
        $data->userto            = 22;
        $data->subject           = get_string('openstack_error_conection_subject', 'bigbluebuttonbn');
        $data->fullmessage       = $message;
        $data->fullmessageformat = FORMAT_HTML;
        $data->fullmessagehtml   = $message;
        $data->smallmessage      = '';
        $data->notification      = 1; // This is only set to 0 for personal messages between users.

        return $data;
    }

    private function communicate_creation_error($data, $message, $first_attempt){

        $subject = "";
        if($first_attempt){
            $subject = get_string('openstack_error_creation_first_request_subject', 'bigbluebuttonbn');
        }else{
            $subject = get_string('openstack_error_creation_request_subject', 'bigbluebuttonbn');
        }

        $data->component         = 'mod_bigbluebuttonbn';
        $data->name              = 'openstack_task_error'; // This is the message name from messages.php.
        $data->userfrom          = \core_user::get_noreply_user();
        $data->userto            = 22;
        $data->subject           = $subject;
        $data->fullmessage       = $message;
        $data->fullmessageformat = FORMAT_HTML;
        $data->fullmessagehtml   = $message;
        $data->smallmessage      = '';
        $data->notification      = 1; // This is only set to 0 for personal messages between users.

        return $data;

    }

    private function communicate_deletion_error($data, $message){
        $data->component         = 'mod_bigbluebuttonbn';
        $data->name              = 'openstack_conection_error'; // This is the message name from messages.php.
        $data->userfrom          = \core_user::get_noreply_user();
        $data->userto            = 22; //Cambiar este usuario por el o los correctos.
        $data->subject           = get_string('openstack_error_conection_subject', 'bigbluebuttonbn');;
        $data->fullmessage       = $message;
        $data->fullmessageformat = FORMAT_HTML;
        $data->fullmessagehtml   = $message;
        $data->smallmessage      = '';
        $data->notification      = 1; // This is only set to 0 for personal messages between users.

        return $data;
    }

}