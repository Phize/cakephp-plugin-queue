<?php
class QueueJobFixture extends CakeTestFixture {
	public $name = 'QueueJob';
//	public $import = 'Queue.QueueJob';
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'queue_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'type' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'priority' => array('type' => 'integer', 'null' => false, 'default' => '50', 'length' => 2),
		'recursive' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'interval' => array('type' => 'integer', 'null' => false, 'default' => '86400', 'length' => 10),
		'retry_delay' => array('type' => 'integer', 'null' => false, 'default' => '60', 'length' => 10),
		'tries' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'max_tries' => array('type' => 'integer', 'null' => false, 'default' => '5', 'length' => 10),
		'parameters' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'scheduled' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'tried' => array('type' => 'datetime', 'null' => false, 'default' => '0000-00-00 00:00:00'),
		'completed' => array('type' => 'datetime', 'null' => false, 'default' => '0000-00-00 00:00:00'),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'idle'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'queue_id_name' => array('column' => array('queue_id', 'name'), 'unique' => 1), 'queue_id' => array('column' => 'queue_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $records = array();



	public function __construct() {
		$this->records = array(
			array(
				'id' => 1,
				'queue_id' => 1,
				'name' => 'Job 1',
				'type' => 'job',
				'priority' => 50,
				'recursive' => 1,
				'interval' => 86400,
				'retry_delay' => 60,
				'tries' => 0,
				'max_tries' => 5,
				'scheduled' => date('Y-m-d H:i:s'),
				'status' => 'idle'
			),
			array(
				'id' => 2,
				'queue_id' => 2,
				'name' => 'Job 1',
				'type' => 'job',
				'priority' => 50,
				'recursive' => 0,
				'interval' => 86400,
				'retry_delay' => 60,
				'tries' => 4,
				'max_tries' => 5,
				'scheduled' => date('Y-m-d H:i:s'),
				'status' => 'idle'
			),
			array(
				'id' => 3,
				'queue_id' => 1,
				'name' => 'Job 2',
				'type' => 'job',
				'priority' => 50,
				'recursive' => 0,
				'interval' => 86400,
				'retry_delay' => 60,
				'tries' => 0,
				'max_tries' => 5,
				'scheduled' => date('Y-m-d H:i:s'),
				'status' => 'stopped'
			),
			array(
				'id' => 4,
				'queue_id' => 1,
				'name' => 'Job 3',
				'type' => 'job',
				'priority' => 50,
				'recursive' => 0,
				'interval' => 86400,
				'retry_delay' => 60,
				'tries' => 0,
				'max_tries' => 5,
				'scheduled' => date('Y-m-d H:i:s'),
				'status' => 'running'
			),
			array(
				'id' => 5,
				'queue_id' => 1,
				'name' => 'Job 4',
				'type' => 'job',
				'priority' => 50,
				'recursive' => 0,
				'interval' => 86400,
				'retry_delay' => 60,
				'tries' => 0,
				'max_tries' => 5,
				'scheduled' => date('Y-m-d H:i:s'),
				'status' => 'success'
			),
			array(
				'id' => 6,
				'queue_id' => 1,
				'name' => 'Job 5',
				'type' => 'job',
				'priority' => 50,
				'recursive' => 0,
				'interval' => 86400,
				'retry_delay' => 60,
				'tries' => 0,
				'max_tries' => 5,
				'scheduled' => date('Y-m-d H:i:s'),
				'status' => 'error'
			),
			array(
				'id' => 7,
				'queue_id' => 1,
				'name' => 'Job 6',
				'type' => 'job',
				'priority' => 50,
				'recursive' => 0,
				'interval' => 86400,
				'retry_delay' => 60,
				'tries' => 5,
				'max_tries' => 5,
				'scheduled' => date('Y-m-d H:i:s'),
				'status' => 'error'
			),
		);

		parent::__construct();
	}
}
