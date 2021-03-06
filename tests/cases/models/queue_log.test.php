<?php
class QueueLogTestCase extends CakeTestCase {
	public $fixtures = array('plugin.Queue.queue_queue', 'plugin.Queue.queue_job', 'plugin.Queue.queue_log');
	protected $queueConfig = array();

	public function start() {
		parent::start();

		$this->queueConfig = Configure::read('Queue');
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
		$result = $this->QueueLog->add(900, 'success', 'Success.');
		$this->assertIdentical($result, false);

		$result = $this->QueueLog->add(1, 'success', 'Success.');
		$this->assertIdentical($result, true);
	}
}
