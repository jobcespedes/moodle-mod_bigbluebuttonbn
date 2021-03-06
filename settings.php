<?php
/**
 * Settings for BigBlueButtonBN
 *
 * @package   mod_bigbluebuttonbn
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2010-2015 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die;

global $BIGBLUEBUTTONBN_CFG;

require_once(dirname(__FILE__).'/locallib.php');

/*---- OpenStack integration ----*/
//Regex values used for validation
$bbb_server_regex= '/^(https?\:\/\/([a-zA-Z0-9\-\.]+\.[a-zA-Z0-9]{2,3})(\/[a-zA-Z0-9\-\.]+)*(\/bigbluebutton\/))$|(^$)/'; //Validates BBB server instance
$heat_url_regex='/^https?\:\/\/([a-zA-Z0-9\-\.]+\.[a-zA-Z0-9]{2,3})(\/[a-zA-Z0-9\-\.]+)*(:[0-9]{1,6}\/v2.0)$/'; //Validates API version
$hash_regex='/^[[:xdigit:]]{0,40}$/';
$default_text_regex= '/^[a-zA-Z0-9-_.[:space:]]{0,40}$/';
$url_regex='/^https?\:\/\/([a-zA-Z0-9\-\.\_]+\.[a-zA-Z0-9]{2,3})(\/[a-zA-Z0-9\-\.\_]+)*$/';
$durations_array_regex='/^\[\d{1,3}(,\d{1,3}){0,10}\]$/';
$days_hours_minutes_regex = '/^\d{1,3}[Dd]-\d{1,2}[Hh]-\d{1,2}[mM]$/';
$max_simultaneous_instances_regex = '/^\d{0,5}$/';
$minutes_regex = '/^\d{0,4}$/';
$attempts_number_regex = '/^\d{0,3}$/';
$csv_regex = '/^$|[0-9a-z\.\-\@\_]+(,[0-9a-z\.\-\@\_]+)*/';

//Create OpenStackIntegration link
$openStacklink= "{$CFG->wwwroot}/mod/bigbluebuttonbn/openstack_interface/openstack_integration_settings.php";
$openStack_integration_description = get_string('openstack_servers_on_demand', 'bigbluebuttonbn');
$openstack_integration = get_string('openstack_integration_modules', 'bigbluebuttonbn');
$openstack_settings_note = get_string('openstack_settings_note', 'bigbluebuttonbn');
$openStacklink_html = <<< EOD
{$openStack_integration_description}<a style="margin-top:.25em" href="{$openStacklink}"> {$openstack_integration}</a><br/>{$openstack_settings_note} 
EOD;


/*---- end of OpenStack integration ----*/

if ($ADMIN->fulltree) {

    if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_server_url) ||
        !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_shared_secret) ) {
        $settings->add( new admin_setting_heading('bigbluebuttonbn_config_general',
            get_string('config_general', 'bigbluebuttonbn'),
            get_string('config_general_description', 'bigbluebuttonbn')));

        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_server_url) ) {
            $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_server_url',
                get_string( 'config_server_url', 'bigbluebuttonbn' ),
                get_string( 'config_server_url_description', 'bigbluebuttonbn' ),
                BIGBLUEBUTTONBN_DEFAULT_SERVER_URL, $bbb_server_regex));
        }
        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_shared_secret) ) {
            $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_shared_secret',
                get_string( 'config_shared_secret', 'bigbluebuttonbn' ),
                get_string( 'config_shared_secret_description', 'bigbluebuttonbn' ),
                BIGBLUEBUTTONBN_DEFAULT_SHARED_SECRET, $hash_regex));
        }
    }

    /*---- OpenStack integration ----*/
    if ( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_integration)){
        $settings->add(new admin_setting_heading('bigbluebutton_manage_os_integration',  get_string('config_openstack_integration', 'bigbluebuttonbn').
            $OUTPUT->help_icon('openstack_integration', 'bigbluebuttonbn'), $openStacklink_html));
        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_integration) ){
            //Enable OpenStack integration
            $settings->add( new admin_setting_configcheckbox( 'bigbluebuttonbn_openstack_integration',
                get_string('config_openstack_integration', 'bigbluebuttonbn'),
                get_string('config_openstack_integration_description','bigbluebuttonbn'),
                0));
        }

    }

    if(bigbluebuttonbn_get_cfg_openstack_integration()){
        ////Configurations for OpenStack authentication
        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_username)||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_password)||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_tenant_id)){
            $settings->add( new admin_setting_heading( 'bigbluebuttonbn_openstack_credentials',
                '',
                get_string( 'config_openstack_credentials_description', 'bigbluebuttonbn' ),
                null));

            if(!isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_username)){
                //OpenStack username
                $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_openstack_username',
                    get_string( 'config_openstack_username', 'bigbluebuttonbn' ),
                    get_string( 'config_openstack_username_description', 'bigbluebuttonbn' ),
                    null, $default_text_regex));
            }
            if(!isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_password)){
                $settings->add( new admin_setting_configpasswordunmask('bigbluebuttonbn_openstack_password',
                    get_string( 'config_openstack_password', 'bigbluebuttonbn' ),
                    get_string( 'config_openstack_password_description', 'bigbluebuttonbn' ),
                    null, $default_text_regex));
            }
            if(!isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_tenant_id)){
                $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_openstack_tenant_id',
                    get_string( 'config_openstack_tenant_id', 'bigbluebuttonbn' ),
                    get_string( 'config_openstack_tenant_id_description', 'bigbluebuttonbn' ),
                    null, $hash_regex));
            }
        }

        //Configurations for stacks and OpenStack API
        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_name_prefix) ||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_heat_url) ||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_heat_region) ||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_json_stack_parameters_url) ||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_meeting_durations) ||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_conference_extra_time) ||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_min_openingtime) ||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_max_openingtime) ||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_yaml_stack_template_url) ||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_error_log_file_enabled)||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_reservation_module_enabled) ||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_max_simultaneous_instances)||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_reservation_user_list_logic) ||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_authorized_reservation_users_list)||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_connection_error_users_list_enabled)||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_connection_error_email_users_list)||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_task_error_users_list_enabled)||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_task_error_email_users_list)||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_resiliency_module_enabled)||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_creation_retries_number)||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_deletion_retries_number)){

            $settings->add( new admin_setting_heading('bigbluebuttonbn_config_cloud',
                '',
                get_string('config_cloud_description', 'bigbluebuttonbn'),
                null));
            if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_heat_url) ){
                //URL of server with Heat endpoint
                $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_heat_url',
                    get_string( 'config_heat_url', 'bigbluebuttonbn' ),
                    get_string( 'config_heat_url_description', 'bigbluebuttonbn' ),
                    null, $heat_url_regex));
            }
            if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_heat_region) ){
                //Region of Heat service
                $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_heat_region',
                    get_string( 'config_heat_region', 'bigbluebuttonbn' ),
                    get_string( 'config_heat_region_description', 'bigbluebuttonbn' ),
                    null, $default_text_regex));
            }
            if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_yaml_stack_template_url)){
                //YAML file with stack template
                $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_yaml_stack_template_url',
                    get_string( 'config_yaml_stack_template_url', 'bigbluebuttonbn' ),
                    get_string( 'config_yaml_stack_template_url_description', 'bigbluebuttonbn' ),
                    null,$url_regex));
            }
            if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_json_stack_parameters_url)){
                //Parameters for stack creation in JSON representation
                $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_json_stack_parameters_url',
                    get_string( 'config_json_stack_parameters_url', 'bigbluebuttonbn' ),
                    get_string( 'config_json_stack_parameters_url_description', 'bigbluebuttonbn' ),
                    null,$url_regex));
            }
            if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_json_meeting_durations)){
                //Meeting durations
                $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_json_meeting_durations',
                    get_string('config_meeting_durations', 'bigbluebuttonbn'),
                    get_string('config_meeting_durations_description','bigbluebuttonbn'),
                    null, $durations_array_regex));
            }

            if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_conference_extra_time)){
                //Extra time for conference
                $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_conference_extra_time',
                    get_string('config_conference_extra_time', 'bigbluebuttonbn'),
                    get_string('config_conference_extra_time_description','bigbluebuttonbn'),
                    null, $minutes_regex));
            }

            if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_min_openingtime)){
                //Describes how soon a meeting can be scheduled
                $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_min_openingtime',
                    get_string('config_min_openingtime', 'bigbluebuttonbn'),
                    get_string('config_min_openingtime_description','bigbluebuttonbn'),
                    null, $days_hours_minutes_regex));
            }
            if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_max_openingtime)){
                //Describes how anticipated a meeting can be scheduled
                $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_max_openingtime',
                    get_string('config_max_openingtime', 'bigbluebuttonbn'),
                    get_string('config_max_openingtime_description','bigbluebuttonbn'),
                    null, $days_hours_minutes_regex));
            }

            if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_error_log_file_enabled)){
                //Describes how anticipated a meeting can be scheduled
                $settings->add( new admin_setting_configcheckbox( 'bigbluebuttonbn_error_log_file_enabled',
                    get_string('config_error_log_file_enabled', 'bigbluebuttonbn'),
                    get_string('config_error_log_file_enabled_description','bigbluebuttonbn'),
                    0));
            }
            if(!isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_name_prefix)){
                //BBB server prefix
                $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_openstack_name_prefix',
                    get_string( 'config_openstack_name_prefix', 'bigbluebuttonbn' ),
                    get_string( 'config_openstack_name_prefix_description', 'bigbluebuttonbn' ),
                    null, $default_text_regex));
            }

            $settings->add( new admin_setting_heading('bigbluebuttonbn_reservation_heading', '', get_string('openstack_reservation_settings', 'bigbluebuttonbn'), null));

            if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_reservation_module_enabled) ){
                //Enable OpenStack reservations module
                $settings->add( new admin_setting_configcheckbox( 'bigbluebuttonbn_reservation_module_enabled',
                    get_string('config_reservation_module_enabled', 'bigbluebuttonbn'),
                    get_string('config_reservation_module_enabled_description','bigbluebuttonbn'),
                    0));
            }

            if (!isset ($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_max_simultaneous_instances)){
                //Describes the number of simultaneous BBB servers on demand at a time
                $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_max_simultaneous_instances',
                    get_string('config_max_simultaneous_instances', 'bigbluebuttonbn'),
                    get_string('config_max_simultaneous_instances_description','bigbluebuttonbn'),
                    null, $max_simultaneous_instances_regex));
            }

            if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_reservation_user_list_logic) ){
                //Defines the logic in reservation user list
                $settings->add( new admin_setting_configcheckbox( 'bigbluebuttonbn_reservation_user_list_logic',
                    get_string('config_reservation_user_list_logic', 'bigbluebuttonbn'),
                    get_string('config_reservation_user_list_logic_description','bigbluebuttonbn'),
                    1));
            }

            if (!isset ($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_authorized_reservation_users_list)){
                //List the users that can make a reservation
                $settings->add( new admin_setting_configtextarea( 'bigbluebuttonbn_authorized_reservation_users_list',
                    get_string('config_authorized_reservation_users_list', 'bigbluebuttonbn'),
                    get_string('config_authorized_reservation_users_list_description','bigbluebuttonbn'),
                    null, $csv_regex));
            }

            $settings->add( new admin_setting_heading('bigbluebuttonbn_time_clarification', '', get_string('openstack_time_description', 'bigbluebuttonbn', bigbluebuttonbn_get_cfg_openstack_destruction_time()), null));

            $settings->add( new admin_setting_heading('bigbluebuttonbn_external_notifications_heading', '', get_string('openstack_external_notifications_settings', 'bigbluebuttonbn'), null));

            if(!isset ($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_connection_error_users_list_enabled)){
                //Enables external user notificationes
                $settings->add ( new admin_setting_configcheckbox('bigbluebuttonbn_connection_error_users_list_enabled',
                    get_string('bigbluebuttonbn_connection_error_users_list_enabled','bigbluebuttonbn'),
                    get_string('bigbluebuttonbn_connection_error_users_list_enabled_description', 'bigbluebuttonbn'),
                    0));
            }

            if(!isset ($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_connection_error_email_users_list)){
                //List with external emails for notifications on openstack connection errors
                $settings->add ( new admin_setting_configtextarea('bigbluebuttonbn_openstack_connection_error_email_users_list',
                    get_string('bigbluebuttonbn_openstack_connection_error_email_users_list','bigbluebuttonbn'),
                    get_string('bigbluebuttonbn_openstack_connection_error_email_users_list_description', 'bigbluebuttonbn'),
                    null, $csv_regex));
            }

            if(!isset ($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_task_error_users_list_enabled)){
                //Enables external user notificationes
                $settings->add ( new admin_setting_configcheckbox('bigbluebuttonbn_task_error_users_list_enabled',
                    get_string('bigbluebuttonbn_task_error_users_list_enabled','bigbluebuttonbn'),
                    get_string('bigbluebuttonbn_task_error_users_list_enabled_description', 'bigbluebuttonbn'),
                    0));
            }

            if(!isset ($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_task_error_email_users_list)){
                //List with external emails for notifications on openstack task errors
                $settings->add ( new admin_setting_configtextarea('bigbluebuttonbn_openstack_task_error_email_users_list',
                    get_string('bigbluebuttonbn_openstack_task_error_email_users_list','bigbluebuttonbn'),
                    get_string('bigbluebuttonbn_openstack_task_error_email_users_list_description', 'bigbluebuttonbn'),
                    null, $csv_regex));
            }

            $settings->add( new admin_setting_heading('bigbluebuttonbn_admin_notification_clarification_heading', '', get_string('openstack_admin_notifications_clarification', 'bigbluebuttonbn', $CFG->supportemail), null));

            $settings->add( new admin_setting_heading('bigbluebuttonbn_resiliency_heading', '', get_string('openstack_resiliency_settings', 'bigbluebuttonbn'), null));

            if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_resiliency_module_enabled)){
                //Enable OpenStack reservations module
                $settings->add( new admin_setting_configcheckbox( 'bigbluebuttonbn_resiliency_module_enabled',
                    get_string('config_resiliency_module_enabled', 'bigbluebuttonbn'),
                    get_string('config_resiliency_module_enabled_description','bigbluebuttonbn'),
                    0));
            }

            if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_creation_retries_number)){
                //Number of creation attempts
                $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_creation_retries_number',
                    get_string('config_creation_retries_number', 'bigbluebuttonbn'),
                    get_string('config_creation_retries_number_description','bigbluebuttonbn'),
                    0, $attempts_number_regex));
            }

            if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_deletion_retries_number)){
                //Extra time for conference
                $settings->add( new admin_setting_configtext( 'bigbluebuttonbn_deletion_retries_number',
                    get_string('config_deletion_retries_number', 'bigbluebuttonbn'),
                    get_string('config_deletion_retries_number_description','bigbluebuttonbn'),
                    0, $attempts_number_regex));
            }
        }

        if (!isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_use_backup_server) ||
            !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_backup_recording)){
            $settings->add(new admin_setting_heading('bigbluebuttonbn_openstack_backup_heading', '', get_string('config_openstack_backup_settings', 'bigbluebuttonbn'), null));
            if (!isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_use_backup_server)) {
                // Use a backup server
                $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_openstack_use_backup_server',
                    get_string('config_openstack_use_backup_server', 'bigbluebuttonbn'),
                    get_string('config_openstack_use_backup_server_description', 'bigbluebuttonbn'),
                    0));
            }
            if (!isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_openstack_backup_recording)) {
                // Use a backup server
                $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_openstack_backup_recording',
                    get_string('config_openstack_backup_recording', 'bigbluebuttonbn'),
                    get_string('config_openstack_backup_recording_description', 'bigbluebuttonbn'),
                    0));
            }
        }
    }


    /*---- end of OpenStack integration ----*/

    //// Configuration for 'recording' feature
    if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_recording_default) ||
        !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_recording_editable) ||
        !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_recording_icons_enabled) ) {
        $settings->add( new admin_setting_heading('bigbluebuttonbn_recording',
            get_string('config_feature_recording', 'bigbluebuttonbn'),
            get_string('config_feature_recording_description', 'bigbluebuttonbn')));

        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_recording_default) ) {
            // default value for 'recording' feature
            $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_recording_default',
                get_string('config_feature_recording_default', 'bigbluebuttonbn'),
                get_string('config_feature_recording_default_description', 'bigbluebuttonbn'),
                1));
        }
        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_recording_editable) ) {
            // UI for 'recording' feature
            $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_recording_editable',
                get_string('config_feature_recording_editable', 'bigbluebuttonbn'),
                get_string('config_feature_recording_editable_description', 'bigbluebuttonbn'),
                1));
        }
        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_recording_icons_enabled) ) {
            // Front panel for 'recording' managment feature
            $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_recording_icons_enabled',
                get_string('config_feature_recording_icons_enabled', 'bigbluebuttonbn'),
                get_string('config_feature_recording_icons_enabled_description', 'bigbluebuttonbn'),
                1));
        }
    }

    //// Configuration for 'recording tagging' feature
    if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_recordingtagging_default) ||
        !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_recordingtagging_editable) ) {
        $settings->add( new admin_setting_heading('bigbluebuttonbn_recordingtagging',
            get_string('config_feature_recordingtagging', 'bigbluebuttonbn'),
            get_string('config_feature_recordingtagging_description', 'bigbluebuttonbn')));

        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_recordingtagging_default) ) {
            // default value for 'recording tagging' feature
            $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_recordingtagging_default',
                get_string('config_feature_recordingtagging_default', 'bigbluebuttonbn'),
                get_string('config_feature_recordingtagging_default_description', 'bigbluebuttonbn'),
                0));
        }
        // UI for 'recording tagging' feature
        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_recordingtagging_editable) ) {
            $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_recordingtagging_editable',
                get_string('config_feature_recordingtagging_editable', 'bigbluebuttonbn'),
                get_string('config_feature_recordingtagging_editable_description', 'bigbluebuttonbn'),
                1));
        }
    }

    //// Configuration for 'import recordings' feature
    if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_importrecordings_enabled) ||
        !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_importrecordings_from_deleted_activities_enabled) ) {
        $settings->add( new admin_setting_heading('bigbluebuttonbn_importrecordings',
            get_string('config_feature_importrecordings', 'bigbluebuttonbn'),
            get_string('config_feature_importrecordings_description', 'bigbluebuttonbn')));

        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_importrecordings_enabled) ) {
            // default value for 'import recordings' feature
            $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_importrecordings_enabled',
                get_string('config_feature_importrecordings_enabled', 'bigbluebuttonbn'),
                get_string('config_feature_importrecordings_enabled_description', 'bigbluebuttonbn'),
                0));
        }
        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_importrecordings_from_deleted_activities_enabled) ) {
            // consider deleted activities for 'import recordings' feature
            $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_importrecordings_from_deleted_activities_enabled',
                get_string('config_feature_importrecordings_from_deleted_activities_enabled', 'bigbluebuttonbn'),
                get_string('config_feature_importrecordings_from_deleted_activities_enabled_description', 'bigbluebuttonbn'),
                0));
        }
    }

    //// Configuration for wait for moderator feature
    if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_waitformoderator_default) ||
        !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_waitformoderator_editable) ||
        !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_waitformoderator_ping_interval) ||
        !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_waitformoderator_cache_ttl) ) {
        $settings->add( new admin_setting_heading('bigbluebuttonbn_feature_waitformoderator',
            get_string('config_feature_waitformoderator', 'bigbluebuttonbn'),
            get_string('config_feature_waitformoderator_description', 'bigbluebuttonbn')));

        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_waitformoderator_default) ) {
            //default value for 'wait for moderator' feature
            $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_waitformoderator_default',
                get_string('config_feature_waitformoderator_default', 'bigbluebuttonbn'),
                get_string('config_feature_waitformoderator_default_description', 'bigbluebuttonbn'),
                0));
        }
        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_waitformoderator_editable) ) {
            // UI for 'wait for moderator' feature
            $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_waitformoderator_editable',
                get_string('config_feature_waitformoderator_editable', 'bigbluebuttonbn'),
                get_string('config_feature_waitformoderator_editable_description', 'bigbluebuttonbn'),
                1));
        }
        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_waitformoderator_ping_interval) ) {
            //ping interval value for 'wait for moderator' feature
            $settings->add(new admin_setting_configtext('bigbluebuttonbn_waitformoderator_ping_interval',
                get_string('config_feature_waitformoderator_ping_interval', 'bigbluebuttonbn'),
                get_string('config_feature_waitformoderator_ping_interval_description', 'bigbluebuttonbn'),
                10, PARAM_INT));
        }
        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_waitformoderator_cache_ttl) ) {
            //cache TTL value for 'wait for moderator' feature
            $settings->add(new admin_setting_configtext('bigbluebuttonbn_waitformoderator_cache_ttl',
                get_string('config_feature_waitformoderator_cache_ttl', 'bigbluebuttonbn'),
                get_string('config_feature_waitformoderator_cache_ttl_description', 'bigbluebuttonbn'),
                60, PARAM_INT));
        }
    }

    //// Configuration for "static voice bridge" feature
    if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_voicebridge_editable) ) {
        $settings->add( new admin_setting_heading('bigbluebuttonbn_feature_voicebridge',
            get_string('config_feature_voicebridge', 'bigbluebuttonbn'),
            get_string('config_feature_voicebridge_description', 'bigbluebuttonbn')));

        // UI for establishing static voicebridge
        $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_voicebridge_editable',
            get_string('config_feature_voicebridge_editable', 'bigbluebuttonbn'),
            get_string('config_feature_voicebridge_editable_description', 'bigbluebuttonbn'),
            0));
    }

    //// Configuration for "preupload presentation" feature
    if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_preuploadpresentation_enabled) ) {
        // This feature only works if curl is installed
        if (extension_loaded('curl')) {
            $settings->add( new admin_setting_heading('bigbluebuttonbn_feature_preuploadpresentation',
                get_string('config_feature_preuploadpresentation', 'bigbluebuttonbn'),
                get_string('config_feature_preuploadpresentation_description', 'bigbluebuttonbn')
            ));

            // UI for 'preupload presentation' feature
            $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_preuploadpresentation_enabled',
                get_string('config_feature_preuploadpresentation_enabled', 'bigbluebuttonbn'),
                get_string('config_feature_preuploadpresentation_enabled_description', 'bigbluebuttonbn'),
                0));
        } else {
            $settings->add( new admin_setting_heading('bigbluebuttonbn_feature_preuploadpresentation',
                get_string('config_feature_preuploadpresentation', 'bigbluebuttonbn'),
                get_string('config_feature_preuploadpresentation_description', 'bigbluebuttonbn').'<br><br>'.
                '<div class="form-defaultinfo">'.get_string('config_warning_curl_not_installed', 'bigbluebuttonbn').'</div><br>'
            ));
        }
    }

    //// Configuration for "user limit" feature
    if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_userlimit_default) ||
        !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_userlimit_editable) ) {
        $settings->add( new admin_setting_heading('config_userlimit',
            get_string('config_feature_userlimit', 'bigbluebuttonbn'),
            get_string('config_feature_userlimit_description', 'bigbluebuttonbn')));

        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_userlimit_default) ) {
            //default value for 'user limit' feature
            $settings->add(new admin_setting_configtext('bigbluebuttonbn_userlimit_default',
                get_string('config_feature_userlimit_default', 'bigbluebuttonbn'),
                get_string('config_feature_userlimit_default_description', 'bigbluebuttonbn'),
                0, PARAM_INT));
        }
        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_userlimit_editable) ) {
            // UI for 'user limit' feature
            $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_userlimit_editable',
                get_string('config_feature_userlimit_editable', 'bigbluebuttonbn'),
                get_string('config_feature_userlimit_editable_description', 'bigbluebuttonbn'),
                0));
        }
    }

    //// Configuration for "scheduled duration" feature
    if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_scheduled_duration_enabled) ) {
        $settings->add( new admin_setting_heading('config_scheduled',
            get_string('config_scheduled', 'bigbluebuttonbn'),
            get_string('config_scheduled_description', 'bigbluebuttonbn')));

        // calculated duration for 'scheduled session' feature
        $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_scheduled_duration_enabled',
            get_string('config_scheduled_duration_enabled', 'bigbluebuttonbn'),
            get_string('config_scheduled_duration_enabled_description', 'bigbluebuttonbn'),
            1));

        // compensatory time for 'scheduled session' feature
        $settings->add(new admin_setting_configtext('bigbluebuttonbn_scheduled_duration_compensation',
            get_string('config_scheduled_duration_compensation', 'bigbluebuttonbn'),
            get_string('config_scheduled_duration_compensation_description', 'bigbluebuttonbn'),
            10, PARAM_INT));

        // pre-opening time for 'scheduled session' feature
        $settings->add(new admin_setting_configtext('bigbluebuttonbn_scheduled_pre_opening',
            get_string('config_scheduled_pre_opening', 'bigbluebuttonbn'),
            get_string('config_scheduled_pre_opening_description', 'bigbluebuttonbn'),
            10, PARAM_INT));
    }

    //// Configuration for defining the default role/user that will be moderator on new activities
    if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_moderator_default) ) {
        $settings->add( new admin_setting_heading('bigbluebuttonbn_permission',
            get_string('config_permission', 'bigbluebuttonbn'),
            get_string('config_permission_description', 'bigbluebuttonbn')));

        // UI for 'permissions' feature
        $roles = bigbluebuttonbn_get_roles('all', 'array');
        $owner = array('owner' => get_string('mod_form_field_participant_list_type_owner', 'bigbluebuttonbn'));
        $settings->add(new admin_setting_configmultiselect('bigbluebuttonbn_moderator_default',
            get_string('config_permission_moderator_default', 'bigbluebuttonbn'),
            get_string('config_permission_moderator_default_description', 'bigbluebuttonbn'),
            array_keys($owner), array_merge($owner, $roles)));
    }

    //// Configuration for "send notifications" feature
    if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_sendnotifications_enabled) ) {
        $settings->add( new admin_setting_heading('bigbluebuttonbn_feature_sendnotifications',
            get_string('config_feature_sendnotifications', 'bigbluebuttonbn'),
            get_string('config_feature_sendnotifications_description', 'bigbluebuttonbn')));

        // UI for 'send notifications' feature
        $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_sendnotifications_enabled',
            get_string('config_feature_sendnotifications_enabled', 'bigbluebuttonbn'),
            get_string('config_feature_sendnotifications_enabled_description', 'bigbluebuttonbn'),
            1));
    }

    //// Configuration for extended BN capabilities
    if( bigbluebuttonbn_server_offers_bn_capabilities() ) {
        //// Configuration for 'notify users when recording ready' feature
        if( !isset($BIGBLUEBUTTONBN_CFG->bigbluebuttonbn_recordingready_enabled) ) {
            $settings->add( new admin_setting_heading('bigbluebuttonbn_extended_capabilities',
                get_string('config_extended_capabilities', 'bigbluebuttonbn'),
                get_string('config_extended_capabilities_description', 'bigbluebuttonbn')));

            // UI for 'notify users when recording ready' feature
            $settings->add(new admin_setting_configcheckbox('bigbluebuttonbn_recordingready_enabled',
                get_string('config_extended_feature_recordingready_enabled', 'bigbluebuttonbn'),
                get_string('config_extended_feature_recordingready_enabled_description', 'bigbluebuttonbn'),
                0));
        }
    }
}
