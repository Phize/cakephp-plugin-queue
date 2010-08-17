<?php
/* SVN FILE: $Id$ */
/* Queue plugin schema generated on: 2010-07-27 01:07:00 : 1280160420*/
class QueuePluginSchema extends CakeSchema {
	var $name = 'QueuePlugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $queue_jobs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'queue_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'type' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'priority' => array('type' => 'integer', 'null' => false, 'default' => '50', 'length' => 2),
		'is_recursive' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'interval' => array('type' => 'integer', 'null' => false, 'default' => '86400', 'length' => 10),
		'retry_delay' => array('type' => 'integer', 'null' => false, 'default' => '60', 'length' => 10),
		'tries' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'max_tries' => array('type' => 'integer', 'null' => false, 'default' => '5', 'length' => 10),
		'parameters' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'scheduled' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'locked' => array('type' => 'datetime', 'null' => false, 'default' => '0000-00-00 00:00:00'),
		'tried' => array('type' => 'datetime', 'null' => false, 'default' => '0000-00-00 00:00:00'),
		'completed' => array('type' => 'datetime', 'null' => false, 'default' => '0000-00-00 00:00:00'),
		'polling_delay' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 10),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'idle'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'queue_id_name' => array('column' => array('queue_id', 'name'), 'unique' => 1), 'queue_id' => array('column' => 'queue_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $queue_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'job_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'status' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'message' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'job_id' => array('column' => 'job_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $queue_queues = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'unique'),
		'polling_delay' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'stopped'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name' => array('column' => 'name', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
}
?>