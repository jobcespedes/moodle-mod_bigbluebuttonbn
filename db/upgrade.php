<?php

/**
 * Upgrade logic.
 *
 * @package   mod_bigbluebuttonbn
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2010-2015 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

function xmldb_bigbluebuttonbn_upgrade($oldversion=0) {

    global $CFG, $THEME, $DB;
    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    $result = true;

    if ($result && $oldversion < 2012040200) {
        // Define field intro to be droped from bigbluebuttonbn
        $table = new xmldb_table('bigbluebuttonbn');
        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, 'medium', null, null, null, null,'name');

        // Drop field intro
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field, $continue=true, $feedback=true);
        }

        // Define field introformat to be droped from bigbluebuttonbn
        $table = new xmldb_table('bigbluebuttonbn');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

        // Drop field introformat
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field, $continue=true, $feedback=true);
        }

        // Once we reach this point, we can store the new version and consider the module
        // upgraded to the version 2012040200 so the next time this block is skipped
        upgrade_mod_savepoint(true, 2012040200, 'bigbluebuttonbn');
    }

    if ($result && $oldversion < 2012062705) {

        // Define table bigbluebuttonbn_log to be created
        $table = new xmldb_table('bigbluebuttonbn_log');

        // Adding fields to table bigbluebuttonbn_log
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('meetingid', XMLDB_TYPE_CHAR, '256', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('bigbluebuttonbnid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('record', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('event', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table bigbluebuttonbn_log
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for bigbluebuttonbn_log
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // bigbluebuttonbn savepoint reached
        upgrade_mod_savepoint(true, 2012062705, 'bigbluebuttonbn');
    }

    if ($result && $oldversion < 2012100100) {

        $table = new xmldb_table('bigbluebuttonbn');
        $field = new xmldb_field('welcome');
        $field->set_attributes(XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, null, null, 'type');

        $dbman->change_field_type($table, $field, $continue=true, $feedback=true);

        upgrade_mod_savepoint(true, 2012100100, 'bigbluebuttonbn');
    }

    if ($result && $oldversion < 2014050100) {

        $table = new xmldb_table('bigbluebuttonbn');
        $field = new xmldb_field('allmoderators');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field, $continue=true, $feedback=true);
        }

        upgrade_mod_savepoint(true, 2014050100, 'bigbluebuttonbn');
    }

    if ($result && $oldversion < 2014070420) {

        $table = new xmldb_table('bigbluebuttonbn');
        $field = new xmldb_field('participants', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field, $continue=true, $feedback=true);
        }

        upgrade_mod_savepoint(true, 2014070420, 'bigbluebuttonbn');
    }

    if ($result && $oldversion < 2014101004) {

        $table = new xmldb_table('bigbluebuttonbn');
        $field = new xmldb_field('participants');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null);

        $dbman->change_field_type($table, $field, $continue=true, $feedback=true);

        upgrade_mod_savepoint(true, 2014101004, 'bigbluebuttonbn');
    }

    if ($result && $oldversion < 2015063000) {
        // Update the bigbluebuttonbn table
        $table = new xmldb_table('bigbluebuttonbn');
        //// Drop field timeduration
        $field = new xmldb_field('timeduration');
        if( $dbman->field_exists($table, $field) ) {
            $dbman->drop_field($table, $field, $continue=true, $feedback=true);
        }
        //// Drop field allmoderators
        $field = new xmldb_field('allmoderators');
        if( $dbman->field_exists($table, $field) ) {
            $dbman->drop_field($table, $field, $continue=true, $feedback=true);
        }
        //// Add field intro
        $field = new xmldb_field('intro');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'name');
        if( !$dbman->field_exists($table, $field) ) {
            $dbman->add_field($table, $field, $continue=true, $feedback=true);
        }
        //// Add field introformat
        $field = new xmldb_field('introformat');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 1, 'intro');
        if( !$dbman->field_exists($table, $field) ) {
            $dbman->add_field($table, $field, $continue=true, $feedback=true);
        }
        //// Add field tagging
        $field = new xmldb_field('tagging');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'record');
        if( !$dbman->field_exists($table, $field) ) {
            $dbman->add_field($table, $field, $continue=true, $feedback=true);
        }
        //// Add field presentation
        $field = new xmldb_field('presentation');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'timemodified');
        if( !$dbman->field_exists($table, $field) ) {
            $dbman->add_field($table, $field, $continue=true, $feedback=true);
        }
        //// Add field type
        $field = new xmldb_field('type');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'course');
        if( !$dbman->field_exists($table, $field) ) {
            $dbman->add_field($table, $field, $continue=true, $feedback=true);
        }
        //// Rename field timeavailable
        $field = new xmldb_field('timeavailable');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        if( $dbman->field_exists($table, $field) ) {
            $dbman->rename_field($table, $field, 'openingtime', $continue=true, $feedback=true);
        }
        //// Rename field timedue
        $field = new xmldb_field('timedue');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        if( $dbman->field_exists($table, $field) ) {
            $dbman->rename_field($table, $field, 'closingtime', $continue=true, $feedback=true);
        }
        //// Add field timecreated
        $field = new xmldb_field('timecreated');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'closingtime');
        if( !$dbman->field_exists($table, $field) ) {
            $dbman->add_field($table, $field, $continue=true, $feedback=true);
        }
        //// Add field userlimit
        $field = new xmldb_field('userlimit');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field, $continue=true, $feedback=true);
        }

        // Update the bigbluebuttonbn_log table
        $table = new xmldb_table('bigbluebuttonbn_log');
        //// Add field userid
        $field = new xmldb_field('userid');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'bigbluebuttonbnid');
        if( !$dbman->field_exists($table, $field) ) {
            $dbman->add_field($table, $field, $continue=true, $feedback=true);
        }
        //// Add field meta
        $field = new xmldb_field('meta');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'event');
        if( !$dbman->field_exists($table, $field) ) {
            $dbman->add_field($table, $field, $continue=true, $feedback=true);
        }
        //// Drop field recording
        $field = new xmldb_field('record');
        if( $dbman->field_exists($table, $field) ) {
            //// Migrate data in field recording to new format in meta
            $meta = new \stdClass();

            // Record => true.
            $meta->record = true;
            $DB->set_field('bigbluebuttonbn_log', 'meta', json_encode($meta), array('event' => 'Create', 'record' => 1));

            // Record => false.
            $meta->record = false;
            $DB->set_field('bigbluebuttonbn_log', 'meta', json_encode($meta), array('event' => 'Create', 'record' => 0));

            // Drop field recording
            $dbman->drop_field($table, $field, $continue=true, $feedback=true);
        }

        upgrade_mod_savepoint(true, 2015063000, 'bigbluebuttonbn');
    }

    if ($result && $oldversion < 2015080605) {
        // Update the bigbluebuttonbn table
        $table = new xmldb_table('bigbluebuttonbn');
        //// Drop field description
        $field = new xmldb_field('description');
        if( $dbman->field_exists($table, $field) ) {
            $dbman->drop_field($table, $field, $continue=true, $feedback=true);
        }
        //// Change welcome, allow null
        $field = new xmldb_field('welcome');
        $field->set_attributes(XMLDB_TYPE_TEXT, null, null, null, null, null, null, null, 'type');
        if( $dbman->field_exists($table, $field) ) {
            $dbman->change_field_notnull($table, $field, $continue=true, $feedback=true);
        }

        // Update the bigbluebuttonbn_log table
        $table = new xmldb_table('bigbluebuttonbn_log');
        //// Change welcome, allow null
        $field = new xmldb_field('userid');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, 'bigbluebuttonbnid');
        if( $dbman->field_exists($table, $field) ) {
            $dbman->change_field_notnull($table, $field, $continue=true, $feedback=true);
        }

        upgrade_mod_savepoint(true, 2015080605, 'bigbluebuttonbn');
    }

    if ( $result && $oldversion < 2016011305 ) {
        // Update the bigbluebuttonbn table
        $table = new xmldb_table('bigbluebuttonbn');

        // Define field type to be droped from bigbluebuttonbn
        $field = new xmldb_field('type');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'course');
        if ( $dbman->field_exists($table, $field) ) {
            $dbman->drop_field($table, $field, $continue=true, $feedback=true);
        }

        // Define field 'event' to be renamed
        $field = new xmldb_field('event');
        $field->set_attributes(XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);

        // Make sure bigbluebuttonbn_logs table exists
        $table = new xmldb_table('bigbluebuttonbn_log');
        // Conditionally rename the table
        if ($dbman->table_exists($table)) {
            // Update the bigbluebuttonbn_log table
            if ( $dbman->field_exists($table, $field) ) {
                $dbman->rename_field($table, $field, 'log', $continue=true, $feedback=true);
            }
            // Rename bigbluebuttonbn_log table to bigbluebuttonbn_logs
            $dbman->rename_table($table, 'bigbluebuttonbn_logs', $continue=true, $feedback=true);
        } else {
            // It was already renamed, select it only
            $table = new xmldb_table('bigbluebuttonbn_logs');
            // Update the bigbluebuttonbn_logs table
            if ( $dbman->field_exists($table, $field) ) {
                $dbman->rename_field($table, $field, 'log', $continue=true, $feedback=true);
            }
        }

        upgrade_mod_savepoint(true, 2016011305, 'bigbluebuttonbn');
    }


    if ($result && $oldversion < 2016051910) {
        // Update the bigbluebuttonbn table
        $table = new xmldb_table('bigbluebuttonbn');
        //// Drop field newwindow
        $field = new xmldb_field('newwindow');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field, $continue=true, $feedback=true);
        }

        upgrade_mod_savepoint(true, 2016051910, 'bigbluebuttonbn');
    }


    /*---- OpenStack integration ----*/

    if ($oldversion < 2016102600) {

        // Define field bbb_meeting_duration to be added to bigbluebuttonbn.
        $table = new xmldb_table('bigbluebuttonbn');
        $field = new xmldb_field('bbb_meeting_duration', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'userlimit');

        // Conditionally launch add field bbb_meeting_duration.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2016102600, 'bigbluebuttonbn');
    }

    if($oldversion < 2017042003){

        //Define table bigbluebuttonbn_openstack to be created
        $table = new xmldb_table('bigbluebuttonbn_openstack');

        //Add fields to bigbluebuttonbn_openstack.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('stack_name', XMLDB_TYPE_CHAR, '255', null, null, null, null, null);
        $table->add_field('meetingid', XMLDB_TYPE_CHAR, '256', null, null, null, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', null);
        $table->add_field('bbb_server_status', XMLDB_TYPE_CHAR, '256', null, null, null, null, null);
        $table->add_field('bbb_server_url', XMLDB_TYPE_CHAR, '256', null, null, null, null, null);
        $table->add_field('bbb_server_shared_secret', XMLDB_TYPE_CHAR, '256', null, null, null, null, null);
        $table->add_field('meeting_duration', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null);
        $table->add_field('openingtime', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', null);
        $table->add_field('creationtime', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', null);
        $table->add_field('deletiontime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null);

        // Adding keys to table bigbluebuttonbn_log
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for bigbluebuttonbn_openstack
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2017042003, 'bigbluebuttonbn');
    }


    if($oldversion < 2017042004) {
        //Define table bigbluebuttonbn_openstack to be created
        $table = new xmldb_table('bigbluebuttonbn_os_logs');

        //Add fields to bigbluebuttonbn_openstack.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('event_time', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null);
        $table->add_field('stack_name', XMLDB_TYPE_CHAR, '255', null, null, null, null, null);
        $table->add_field('meetingid', XMLDB_TYPE_CHAR, '256', null, null, null, null, null);
        $table->add_field('log_level', XMLDB_TYPE_CHAR, '256', null, null, null, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '256', null, null, null, null, null);
        $table->add_field('event', XMLDB_TYPE_TEXT, null, null, null, null, null, null);
        $table->add_field('event_details', XMLDB_TYPE_TEXT, null, null, null, null, null, null);

        // Adding keys to table bigbluebuttonbn_log
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('foreign', XMLDB_KEY_FOREIGN, array('stack_name'), 'bigbluebuttonbn_openstack', array('stack_name'));

        // Conditionally launch create table for bigbluebuttonbn_openstack
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2017042004, 'bigbluebuttonbn');
    }


    if($oldversion < 2017071701) {
        //Define table bigbluebuttonbn_openstack to be created
        $table = new xmldb_table('bigbluebuttonbn_reservations');

        //Add fields to bigbluebuttonbn_openstack.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('start_time', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null);
        $table->add_field('finish_time', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null);
        $table->add_field('begin_date', XMLDB_TYPE_CHAR, '256', null, null, null, null, null);
        $table->add_field('end_date', XMLDB_TYPE_CHAR, '256', null, null, null, null, null);
        $table->add_field('user_info', XMLDB_TYPE_CHAR, '256', null, null, null, null, null);
        $table->add_field('course_info', XMLDB_TYPE_CHAR, '256', null, null, null, null, null);
        $table->add_field('meetingid', XMLDB_TYPE_CHAR, '256', null, null, null, null, null);

        // Adding keys to table bigbluebuttonbn_log
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for bigbluebuttonbn_openstack
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2017071701, 'bigbluebuttonbn');
    }

    if($oldversion < 2017100604){

        //Define table bigbluebuttonbn_openstack to be edited
        $table = new xmldb_table('bigbluebuttonbn_openstack');

        //Add fields to bigbluebuttonbn_openstack.
        $field = new xmldb_field('creation_attempts', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'deletiontime');

        // Conditionally launch add field creation_retries.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('deletion_attempts', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'creation_retries');

        // Conditionally launch add field creation_retries.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2017100604, 'bigbluebuttonbn');
    }

    if($oldversion < 2017102000){

        //Define table bigbluebuttonbn_openstack to be edited
        $table = new xmldb_table('bigbluebuttonbn_openstack');

        //Add fields to bigbluebuttonbn_openstack.
        $field = new xmldb_field('conference_name', XMLDB_TYPE_CHAR, '256', null, null, null, null, 'deletion_attempts');

        // Conditionally launch add field c
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        //Add fields to bigbluebuttonbn_openstack.
        $field = new xmldb_field('user_name', XMLDB_TYPE_CHAR, '256', null, null, null, null, 'conference_name');

        // Conditionally launch add field c
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        //Add fields to bigbluebuttonbn_openstack.
        $field = new xmldb_field('course_name', XMLDB_TYPE_CHAR, '256', null, null, null, null, 'user_name');

        // Conditionally launch add field c
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        //Drop creationtime field
        $field = new xmldb_field('creationtime');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2017102000, 'bigbluebuttonbn');
    }

    if($oldversion < 2017102001){

        //Define table bigbluebuttonbn_openstack to be edited
        $table = new xmldb_table('bigbluebuttonbn_os_logs');

        //Add fields to bigbluebuttonbn_openstack.
        $field = new xmldb_field('conference_name', XMLDB_TYPE_CHAR, '256', null, null, null, null, 'meetingid');

        // Conditionally launch add field c
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        //Add fields to bigbluebuttonbn_openstack.
        $field = new xmldb_field('user_name', XMLDB_TYPE_CHAR, '256', null, null, null, null, 'conference_name');

        // Conditionally launch add field c
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        //Add fields to bigbluebuttonbn_openstack.
        $field = new xmldb_field('course_name', XMLDB_TYPE_CHAR, '256', null, null, null, null, 'user_name');

        // Conditionally launch add field c
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2017102001, 'bigbluebuttonbn');
    }

    /*---- end of OpenStack integration ----*/

    return $result;
}