<?php
/**
 * Config all BigBlueButtonBN instances in this course.
 *
 * @package   mod_bigbluebuttonbn
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2010-2015 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_bigbluebuttonbn_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $DB, $USER, $BIGBLUEBUTTONBN_CFG;

        $course_id = optional_param('course', 0, PARAM_INT); // course ID, or
        $course_module_id = optional_param('update', 0, PARAM_INT); // course_module ID, or
        $bigbluebuttonbn = null;
        if ($course_id) {
            $course = $DB->get_record('course', array('id' => $course_id), '*', MUST_EXIST);
        } else if ($course_module_id) {
            $cm = get_coursemodule_from_id('bigbluebuttonbn', $course_module_id, 0, false, MUST_EXIST);
            $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
            $bigbluebuttonbn = $DB->get_record('bigbluebuttonbn', array('id' => $cm->instance), '*', MUST_EXIST);
        }

        $context = bigbluebuttonbn_get_context_course($course->id);

        //BigBlueButton server data
        $endpoint = bigbluebuttonbn_get_cfg_server_url();

        //UI configuration options
        $voicebridge_editable = bigbluebuttonbn_get_cfg_voicebridge_editable();
        $recording_default = bigbluebuttonbn_get_cfg_recording_default();
        $recording_editable = bigbluebuttonbn_get_cfg_recording_editable();
        $recording_tagging_default = bigbluebuttonbn_get_cfg_recording_tagging_default();
        $recording_tagging_editable = bigbluebuttonbn_get_cfg_recording_tagging_editable();
        $waitformoderator_default = bigbluebuttonbn_get_cfg_waitformoderator_default();
        $waitformoderator_editable = bigbluebuttonbn_get_cfg_waitformoderator_editable();
        $userlimit_default = bigbluebuttonbn_get_cfg_userlimit_default();
        $userlimit_editable = bigbluebuttonbn_get_cfg_userlimit_editable();
        $preuploadpresentation_enabled = bigbluebuttonbn_get_cfg_preuploadpresentation_enabled();
        $sendnotifications_enabled = bigbluebuttonbn_get_cfg_sendnotifications_enabled();



        /*---- OpenStack integration ----*/
        if (bigbluebuttonbn_get_cfg_openstack_integration()){
            //Validates if the BigBlueButton server is running
            $serverVersion = 1.0;
        }else{
            $serverVersion = bigbluebuttonbn_getServerVersion($endpoint);
            if ( !isset($serverVersion) ) {
                print_error( 'general_error_unable_connect', 'bigbluebuttonbn', $CFG->wwwroot.'/admin/settings.php?section=modsettingbigbluebuttonbn' );
            }
        }
        /*---- end of OpenStack integration ----*/

        $mform =& $this->_form;
        $current_activity =& $this->current;

        //-------------------------------------------------------------------------------
        // First block starts here
        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('mod_form_block_general', 'bigbluebuttonbn'));

        $mform->addElement('text', 'name', get_string('mod_form_field_name','bigbluebuttonbn'), 'maxlength="64" size="32"');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', null, 'maxlength', 30, 'client');

        $version_major = bigbluebuttonbn_get_moodle_version_major();
        if ( $version_major < '2015051100' ) {
            //This is valid before v2.9
            $this->add_intro_editor(false, get_string('mod_form_field_intro', 'bigbluebuttonbn'));
        } else {
            //This is valid after v2.9
            $this->standard_intro_elements(get_string('mod_form_field_intro', 'bigbluebuttonbn'));
        }
        $mform->setAdvanced('introeditor');
        $mform->setAdvanced('showdescription');

        $mform->addElement('textarea', 'welcome', get_string('mod_form_field_welcome','bigbluebuttonbn'), 'wrap="virtual" rows="5" cols="60"');
        $mform->addHelpButton('welcome', 'mod_form_field_welcome', 'bigbluebuttonbn');
        $mform->setType('welcome', PARAM_TEXT);
        $mform->setAdvanced('welcome');

        if ( $voicebridge_editable ) {
            $mform->addElement('text', 'voicebridge', get_string('mod_form_field_voicebridge','bigbluebuttonbn'), array('maxlength'=>4, 'size'=>6));
            $mform->addRule('voicebridge', get_string('mod_form_field_voicebridge_format_error', 'bigbluebuttonbn'), 'numeric', '####', 'server');
            $mform->setDefault( 'voicebridge', 0 );
            $mform->addHelpButton('voicebridge', 'mod_form_field_voicebridge', 'bigbluebuttonbn');
            $mform->setAdvanced('voicebridge');
        }
        $mform->setType('voicebridge', PARAM_INT);

        if ( $waitformoderator_editable ) {
            $mform->addElement('checkbox', 'wait', get_string('mod_form_field_wait', 'bigbluebuttonbn'));
            $mform->addHelpButton('wait', 'mod_form_field_wait', 'bigbluebuttonbn');
            $mform->setDefault( 'wait', $waitformoderator_default );
            $mform->setAdvanced('wait');
        } else {
            $mform->addElement('hidden', 'wait', $waitformoderator_default );
        }
        $mform->setType('wait', PARAM_INT);

        if ( $userlimit_editable ) {
            $mform->addElement('text', 'userlimit', get_string('mod_form_field_userlimit','bigbluebuttonbn'), 'maxlength="3" size="5"' );
            $mform->addHelpButton('userlimit', 'mod_form_field_userlimit', 'bigbluebuttonbn');
            $mform->setDefault( 'userlimit', $userlimit_default );
        } else {
            $mform->addElement('hidden', 'userlimit', $userlimit_default );
        }
        $mform->setType('userlimit', PARAM_TEXT);

        if ( floatval($serverVersion) >= 0.8 ) {
            if ( $recording_editable ) {
                $mform->addElement('checkbox', 'record', get_string('mod_form_field_record', 'bigbluebuttonbn'));
                $mform->setDefault( 'record', $recording_default );
                $mform->setAdvanced('record');
            } else {
                $mform->addElement('hidden', 'record', $recording_default);
            }
            $mform->setType('record', PARAM_INT);

            if ( $recording_tagging_editable ) {
                $mform->addElement('checkbox', 'tagging', get_string('mod_form_field_recordingtagging', 'bigbluebuttonbn'));
                $mform->setDefault('tagging', $recording_tagging_default);
                $mform->setAdvanced('tagging');
            } else {
                $mform->addElement('hidden', 'tagging', $recording_tagging_default );
            }
            $mform->setType('tagging', PARAM_INT);
        }

        if ( $sendnotifications_enabled ) {
            $mform->addElement('checkbox', 'notification', get_string('mod_form_field_notification', 'bigbluebuttonbn'));
            if ($this->current->instance) {
                $mform->addHelpButton('notification', 'mod_form_field_notification', 'bigbluebuttonbn');
            } else {
                $mform->addHelpButton('notification', 'mod_form_field_notification', 'bigbluebuttonbn');
            }
            $mform->setDefault('notification', 0);
        }
        $mform->setType('notification', PARAM_INT);
        //-------------------------------------------------------------------------------
        // First block ends here
        //-------------------------------------------------------------------------------


        //-------------------------------------------------------------------------------
        // Second block starts here
        //-------------------------------------------------------------------------------
        if ( $preuploadpresentation_enabled ) {
            $mform->addElement('header', 'preupload', get_string('mod_form_block_presentation', 'bigbluebuttonbn'));
            $mform->setExpanded('preupload');

            $filemanager_options = array();
            $filemanager_options['accepted_types'] = '*';
            $filemanager_options['maxbytes'] = 0;
            $filemanager_options['subdirs'] = 0;
            $filemanager_options['maxfiles'] = 1;
            $filemanager_options['mainfile'] = true;

            $mform->addElement('filemanager', 'presentation', get_string('selectfiles'), null, $filemanager_options);
        }
        //-------------------------------------------------------------------------------
        // Second block ends here
        //-------------------------------------------------------------------------------


        //-------------------------------------------------------------------------------
        // Third block starts here
        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'permission', get_string('mod_form_block_participants', 'bigbluebuttonbn'));

        // Data required for "Add participant" and initial "Participant list" setup
        $roles = bigbluebuttonbn_get_roles();
        $users = bigbluebuttonbn_get_users($context);

        $participant_list = bigbluebuttonbn_get_participant_list($bigbluebuttonbn, $context);
        $mform->addElement('hidden', 'participants', json_encode($participant_list));
        $mform->setType('participants', PARAM_TEXT);

        $html_participant_selection = ''.
            '<div id="fitem_bigbluebuttonbn_participant_selection" class="fitem fitem_fselect">'."\n".
            '  <div class="fitemtitle">'."\n".
            '    <label for="bigbluebuttonbn_participant_selectiontype">'.get_string('mod_form_field_participant_add', 'bigbluebuttonbn').' </label>'."\n".
            '  </div>'."\n".
            '  <div class="felement fselect">'."\n".
            '    <select id="bigbluebuttonbn_participant_selection_type" onchange="bigbluebuttonbn_participant_selection_set(); return 0;">'."\n".
            '      <option value="all" selected="selected">'.get_string('mod_form_field_participant_list_type_all', 'bigbluebuttonbn').'</option>'."\n".
            '      <option value="role">'.get_string('mod_form_field_participant_list_type_role', 'bigbluebuttonbn').'</option>'."\n".
            '      <option value="user">'.get_string('mod_form_field_participant_list_type_user', 'bigbluebuttonbn').'</option>'."\n".
            '    </select>'."\n".
            '    &nbsp;&nbsp;'."\n".
            '    <select id="bigbluebuttonbn_participant_selection" disabled="disabled">'."\n".
            '      <option value="all" selected="selected">---------------</option>'."\n".
            '    </select>'."\n".
            '    &nbsp;&nbsp;'."\n".
            '    <input value="'.get_string('mod_form_field_participant_list_action_add', 'bigbluebuttonbn').'" type="button" id="id_addselectionid" onclick="bigbluebuttonbn_participant_add(); return 0;" />'."\n".
            '  </div>'."\n".
            '</div>'."\n".
            '<div id="fitem_bigbluebuttonbn_participant_list" class="fitem">'."\n".
            '  <div class="fitemtitle">'."\n".
            '    <label for="bigbluebuttonbn_participant_list">'.get_string('mod_form_field_participant_list', 'bigbluebuttonbn').' </label>'."\n".
            '  </div>'."\n".
            '  <div class="felement fselect">'."\n".
            '    <table id="participant_list_table">'."\n";

        // Add participant list
        foreach($participant_list as $participant){
            $participant_selectionid = '';
            $participant_selectiontype = $participant['selectiontype'];
            if( $participant_selectiontype == 'all') {
                $participant_selectiontype = '<b><i>'.get_string('mod_form_field_participant_list_type_'.$participant_selectiontype, 'bigbluebuttonbn').'</i></b>';
            } else {
                if ( $participant_selectiontype == 'role') {
                    $participant_selectionid = bigbluebuttonbn_get_role_name($participant['selectionid']);
                } else {
                    foreach($users as $user){
                        if( $user->id == $participant['selectionid']) {
                            $participant_selectionid = $user->firstname.' '.$user->lastname;
                            break;
                        }
                    }
                }
                $participant_selectiontype = '<b><i>'.get_string('mod_form_field_participant_list_type_'.$participant_selectiontype, 'bigbluebuttonbn').':</i></b>&nbsp;';
            }

            $html_participant_selection .= ''.
                '      <tr id="participant_list_tr_'.$participant['selectiontype'].'-'.$participant['selectionid'].'">'."\n".
                '        <td width="20px"><a onclick="bigbluebuttonbn_participant_remove(\''.$participant['selectiontype'].'\', \''.$participant['selectionid'].'\'); return 0;" title="'.get_string('mod_form_field_participant_list_action_remove', 'bigbluebuttonbn').'">x</a></td>'."\n".
                '        <td width="125px">'.$participant_selectiontype.'</td>'."\n".
                '        <td>'.$participant_selectionid.'</td>'."\n".
                '        <td><i>&nbsp;'.get_string('mod_form_field_participant_list_text_as', 'bigbluebuttonbn').'&nbsp;</i>'."\n".
                '          <select id="participant_list_role_'.$participant['selectiontype'].'-'.$participant['selectionid'].'" onchange="bigbluebuttonbn_participant_list_role_update(\''.$participant['selectiontype'].'\', \''.$participant['selectionid'].'\'); return 0;">'."\n".
                '            <option value="'.BIGBLUEBUTTONBN_ROLE_VIEWER.'" '.($participant['role'] == BIGBLUEBUTTONBN_ROLE_VIEWER? 'selected="selected" ': '').'>'.get_string('mod_form_field_participant_bbb_role_'.BIGBLUEBUTTONBN_ROLE_VIEWER, 'bigbluebuttonbn').'</option>'."\n".
                '            <option value="'.BIGBLUEBUTTONBN_ROLE_MODERATOR.'" '.($participant['role'] == BIGBLUEBUTTONBN_ROLE_MODERATOR? 'selected="selected" ': '').'>'.get_string('mod_form_field_participant_bbb_role_'.BIGBLUEBUTTONBN_ROLE_MODERATOR, 'bigbluebuttonbn').'</option><select>'."\n".
                '        </td>'."\n".
                '      </tr>'."\n";
        }

        $html_participant_selection .= ''.
            '    </table>'."\n".
            '  </div>'."\n".
            '</div>'."\n".
            '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/bigbluebuttonbn/mod_form.js">'."\n".
            '</script>'."\n";

        $mform->addElement('html', $html_participant_selection);

        // Add data
        $mform->addElement('html', '<script type="text/javascript">var bigbluebuttonbn_participant_selection = {"all": [], "role": '.json_encode($roles).', "user": '.bigbluebuttonbn_get_users_json($users).'}; </script>');
        $mform->addElement('html', '<script type="text/javascript">var bigbluebuttonbn_participant_list = '.json_encode($participant_list).'; </script>');
        $bigbluebuttonbn_strings = Array( "as" => get_string('mod_form_field_participant_list_text_as', 'bigbluebuttonbn'),
            "viewer" => get_string('mod_form_field_participant_bbb_role_viewer', 'bigbluebuttonbn'),
            "moderator" => get_string('mod_form_field_participant_bbb_role_moderator', 'bigbluebuttonbn'),
            "remove" => get_string('mod_form_field_participant_list_action_remove', 'bigbluebuttonbn'),
        );
        $mform->addElement('html', '<script type="text/javascript">var bigbluebuttonbn_strings = '.json_encode($bigbluebuttonbn_strings).'; </script>');
        //-------------------------------------------------------------------------------
        // Third block ends here
        //-------------------------------------------------------------------------------


        //-------------------------------------------------------------------------------
        // Fourth block starts here
        //-------------------------------------------------------------------------------

        //Add explanation of openingtime and closingtime


        /*---- OpenStack integration ----*/
        $time_scheduling_options = ( bigbluebuttonbn_get_cfg_openstack_integration() )? array('enable'=>true) : array('optional'=>true) ;
        /*---- end of OpenStack integration ----*/

        $mform->addElement('header', 'schedule', get_string('mod_form_block_schedule', 'bigbluebuttonbn'));
        if( isset($current_activity->openingtime) && $current_activity->openingtime != 0 || isset($current_activity->closingtime) && $current_activity->closingtime != 0 )
            $mform->setExpanded('schedule');

        $mform->addElement('date_time_selector', 'openingtime', get_string('mod_form_field_openingtime', 'bigbluebuttonbn'), $time_scheduling_options);
        $mform->setDefault('openingtime', 0);
        $mform->addHelpButton('openingtime', 'mod_form_field_openingtime', 'bigbluebuttonbn');
        $mform->addElement('date_time_selector', 'closingtime', get_string('mod_form_field_closingtime', 'bigbluebuttonbn'), $time_scheduling_options);
        $mform->setDefault('closingtime', 0);
        $mform->addHelpButton('closingtime', 'mod_form_field_closingtime', 'bigbluebuttonbn');

        /*---- OpenStack integration ----*/
        if(bigbluebuttonbn_get_cfg_openstack_integration()){
            $mform->addRule('openingtime', null, 'required', null, 'client');
            $mform->addRule('closingtime', null, 'required', null, 'client');
            $durations = json_decode(bigbluebuttonbn_get_cfg_json_meeting_durations());
            $durations = array_combine($durations, $durations);
            $mform->addElement('select', 'bbb_meeting_duration', get_string('mod_form_field_meeting_duration', 'bigbluebuttonbn'), $durations);
            $mform->addHelpButton('bbb_meeting_duration', 'mod_form_field_meeting_duration', 'bigbluebuttonbn');
            $mform->addElement('hidden','reservation_id',null);
        }
        /*---- end of OpenStack integration ----*/

        //-------------------------------------------------------------------------------
        // Fourth block ends here
        //-------------------------------------------------------------------------------


        //-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values) {
        if ($this->current->instance) {
            // Editing existing instance - copy existing files into draft area.
            try {
                $draftitemid = file_get_submitted_draft_itemid('presentation');
                file_prepare_draft_area($draftitemid, $this->context->id, 'mod_bigbluebuttonbn', 'presentation', 0, array('subdirs'=>0, 'maxbytes' => 0, 'maxfiles' => 1, 'mainfile' => true));
                $default_values['presentation'] = $draftitemid;
            } catch (Exception $e){
                error_log("Presentation could not be loaded: ".$e->getMessage());
                return NULL;
            }
        }
    }

    function validation($data, $files) {

        $errors = parent::validation($data, $files);

        if ( isset($data['openingtime']) && isset($data['closingtime']) ) {
            if ( $data['openingtime'] != 0 && $data['closingtime'] != 0 && $data['closingtime'] <= $data['openingtime']) {
                $errors['closingtime'] = get_string('bbbduetimeoverstartingtime', 'bigbluebuttonbn');
            }
        }

        if ( isset($data['voicebridge']) ) {
            if ( !bigbluebuttonbn_voicebridge_unique($data['voicebridge'], $data['instance'])) {
                $errors['voicebridge'] = get_string('mod_form_field_voicebridge_notunique_error', 'bigbluebuttonbn');
            }
        }

        /*---- OpenStack integration ----*/
        if(bigbluebuttonbn_get_cfg_openstack_integration()){
            global $USER;

            $course_module_id = optional_param('update', 0, PARAM_INT); //Checks if course is being updated
            $conference_duplicated = bigbluebuttonbn_meeting_is_duplicated($this->current->meetingid);

            //Prevents creation of meetings to soon or to anticipated
            if ($course_module_id == 0 or $conference_duplicated){
                if ( $data['openingtime'] < bigbluebuttonbn_get_min_openingtime()) {
                    $errors['openingtime'] = get_string('bbbconferencetoosoon', 'bigbluebuttonbn');
                }
            }

            if ( $data['openingtime'] > bigbluebuttonbn_get_max_openingtime() ) { // Too anticipated
                $errors['openingtime'] = get_string('bbbconferencetoolate', 'bigbluebuttonbn');
            }

            if($course_module_id and !$conference_duplicated){//Prevents editing conferences specific settings near creation of machines
                if(bigbluebuttonbn_get_previous_setting($this->current->id, 'openingtime') < bigbluebuttonbn_get_min_openingtime()){

                    if(bigbluebuttonbn_get_previous_setting($this->current->id, 'openingtime') != $data['openingtime']){
                        $errors['openingtime'] = get_string('bbbconferenceopeningsoon', 'bigbluebuttonbn');
                    }
                    if(bigbluebuttonbn_get_previous_setting($this->current->id, 'bbb_meeting_duration') != $data['bbb_meeting_duration']){
                        $errors['bbb_meeting_duration'] = get_string('bbbconferenceopeningsoon', 'bigbluebuttonbn');
                    }
                }else{
                    if ( $data['openingtime'] < bigbluebuttonbn_get_min_openingtime()) {
                        $errors['openingtime'] = get_string('bbbconferencetoosoon', 'bigbluebuttonbn');
                    }
                }
            }

            //---Duplicated meeting
            if (empty($errors) and $conference_duplicated){
                bigbluebuttonbn_change_duplication($this->current);
            }

            //----Reservations
            $reservations_module_on = bigbluebuttonbn_get_cfg_reservation_module_enabled();

            if( $reservations_module_on and !bigbluebuttonbn_allow_user_reservation($USER->username, bigbluebuttonbn_get_cfg_reservation_users_list_logic()) ){
                $errors['openingtime'] = get_string('bbb_reservation_disable', 'bigbluebuttonbn');
            }

            if( $reservations_module_on and empty($errors) ){
                // Get an instance of the currently configured lock_factory. The argument is the locktype.
                $lockfactory = \core\lock\lock_config::get_lock_factory('mod_bigbluebuttonbn_add_or_update_reservations');
                // Lock request timeout
                $timeout=10;

                //Calculates start time and total duration
                $start_time = $data['openingtime'];
                $total_duration_in_minutes = bigbluebuttonbn_get_meeting_total_duration($data['bbb_meeting_duration']);
                $finish_time = $start_time + $total_duration_in_minutes * 60;

                // Get a new lock for the resource, wait for it if needed. Arguments are resource name and timeout.
                if ( $lock = $lockfactory->get_lock('reservations_table', $timeout) ){

                    $update = $data['update']!=0;
                    //Check for availability
                    if(bigbluebuttonbn_bbb_servers_availability($start_time, $finish_time, $update) ){
                        //Reserve conference
                        $reservation_data = (object)[
                            'begin_date'=> date('d/m/Y h:i:s a', $start_time),
                            'end_date'=> date('d/m/Y h:i:s a', $finish_time),
                            'start_time'=>$start_time,
                            'finish_time'=> $finish_time,
                            'user_info'=> 'UserID: '.$USER->id.' UserEmail:'.$USER->email,
                            'course_info'=> 'CourseID:'.$this->_course->id.' CourseName:'.$this->_course->fullname,
                            'meetingid'=> $this->current->meetingid
                        ];
                        $this->_form->_submitValues['reservation_id'] = bigbluebuttonbn_create_or_update_bbb_servers_reservation($reservation_data);
                    }else{ // Show error
                        $errors['openingtime'] = get_string("unsuficient_availability", 'bigbluebuttonbn');
                    }
                    // Release the lock once finished.
                    $lock->release();

                } else {
                    // We did not get access to the resource in time, give up.
                    $errors['openingtime']=get_string('reservation_system_busy', 'bigbluebuttonbn');
                }
            }

        }
        /*---- end of OpenStack integration ----*/

        return $errors;
    }
}
