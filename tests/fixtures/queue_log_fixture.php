<?php
class QueueLogFixture extends CakeTestFixture {
	public $name = 'QueueLog';
//	public $import = 'Queue.QueueLog';
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'job_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'status' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'message' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'job_id' => array('column' => 'job_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $records = array(
		array('id' => 1, 'job_id' => 1, 'status' => 'error', 'message' => 'Error!')
	);
}
