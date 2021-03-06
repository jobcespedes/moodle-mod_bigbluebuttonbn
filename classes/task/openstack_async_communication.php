<?php
namespace mod_bigbluebuttonbn\task;

require_once dirname(dirname(dirname(__FILE__))) . "/lib.php";
require_once dirname(dirname(__FILE__)) . "/openstack/moodle_bbb_openstack_stacks_management_tasks.php";
// Exception handler
require_once dirname(dirname(__FILE__)) . "/openstack/exception_handlers/archive_log_exception_handler.php";
// Message provider
require_once dirname(dirname(__FILE__)) . "/openstack/error_communicators/openstack_error_communicator.php";


use mod_bigbluebuttonbn\openstack;
class openstack_async_communication extends \core\task\scheduled_task {
  public function get_name() {
    return get_string('task_openstack_async_communication', 'mod_bigbluebuttonbn');
  }
  public function execute() {

    if(bigbluebuttonbn_get_cfg_openstack_integration()){
      $async_tasks = new openstack\moodle_bbb_openstack_stacks_management_tasks(
        new openstack\archive_log_exception_handler(),
        new openstack\openstack_error_communicator()
      );
      $async_tasks->do_tasks();
    }
  }
}
