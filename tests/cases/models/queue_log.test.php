<?php
class QueueLogTestCase extends CakeTestCase {
	public $fixtures = array('plugin.Queue.queue_queue', 'plugin.Queue.queue_job', 'plugin.Queue.queue_log');
	protected $config = array();

	public function start() {
		parent::start();

		$this->config = Configure::read('Queue');
	}

	public function end() {
		parent::end();
	}

	public function startCase() {
		parent::startCase();
	}

	public function endCase() {
		parent::endCase();
	}

	public function startTest($method) {
		parent::startTest($method);
		$this->QueueLog =& ClassRegistry::init('Queue.QueueLog');
	}

	public function endTest($method) {
		parent::endTest($method);
		unset($this->QueueLog);
		ClassRegistry::flush();
	}

	/**
	 * add()のテスト
	 */
	public function testAdd() {
		$result = $this->QueueLog->add(1, 'success', 'Success.');
		$this->assertIdentical($result, true);
	}
}
