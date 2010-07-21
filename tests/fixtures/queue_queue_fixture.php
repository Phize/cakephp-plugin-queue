<?php
class QueueQueueFixture extends CakeTestFixture {
	public $name = 'QueueQueue';
//	public $import = 'Queue.QueueQueue';
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'unique'),
		'polling_delay' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'stopped'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name' => array('column' => 'name', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $records = array(
		array('id' => 1, 'name' => 'Queue 1', 'polling_delay' => 1, 'status' => 'stopped'),
		array('id' => 2, 'name' => 'Queue 2', 'polling_delay' => 1, 'status' => 'running')
	);
}
