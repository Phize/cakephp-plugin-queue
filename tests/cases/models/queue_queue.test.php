<?php
App::import('Model', 'Queue.QueueQueue');

class TestQueueQueue extends QueueQueue {
	public $name = 'QueueQueue';
	public $alias = 'QueueQueue';

	public function test_getId($id) {
		return $this->_getId($id);
	}

	public function test_select($id = null) {
		return $this->_select($id);
	}

	public function test_selectByName($name = null) {
		return $this->_selectByName($name);
	}

	public function testIsStatus($status, $id = null) {
		return $this->isStatus($status, $id);
	}

	public function testStatus($status, $id = null) {
		return $this->status($status, $id);
	}
}

class QueueQueueTestCase extends CakeTestCase {
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
		$this->QueueQueue =& ClassRegistry::init('Queue.TestQueueQueue');
	}

	public function endTest($method) {
		parent::endTest($method);
		unset($this->QueueQueue);
		ClassRegistry::flush();
	}

	/**
	 * add()のテスト
	 */
	public function testAdd() {
		$result = $this->QueueQueue->add('Queue 1', array('polling_delay' => 1, 'status' => 'running'));
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->add('Queue 900', array('polling_delay' => 1, 'status' => 'running'));
		$this->assertIdentical($result, true);
	}

	/**
	 * update()のテスト
	 */
	public function testUpdate() {
		$this->QueueQueue->deselect();
		$result = $this->QueueQueue->update();
		$this->assertIdentical($result, false);
		$this->assertIdentical($this->QueueQueue->selected(), array());

		$this->QueueQueue->deselect();
		$data = array(
			'name' => 'Queue 1',
			'polling_delay' => 1,
			'status' => 'running'
		);
		$result = $this->QueueQueue->update($data, false);
		$this->assertIdentical($result, false);
		$this->assertIdentical($this->QueueQueue->selected(), array());

		$this->QueueQueue->deselect();
		$data = array(
			'name' => 'Queue 1',
			'polling_delay' => 1,
			'status' => 'running'
		);
		$result = $this->QueueQueue->update($data, true);
		$this->assertIdentical($result, false);
		$this->assertIdentical($this->QueueQueue->selected(), array());

		$this->QueueQueue->deselect();
		$data = array(
			$this->QueueQueue->primaryKey => 1,
			'name' => 'Queue 1',
			'polling_delay' => 1,
			'status' => 'running'
		);
		$this->QueueQueue->import($data);
		$result = $this->QueueQueue->update();
		$this->assertIdentical($result, true);
		$this->assertIdentical($this->QueueQueue->selected(), $data);

		$this->QueueQueue->deselect();
		$data = array(
			$this->QueueQueue->primaryKey => 1,
			'name' => 'Queue 1',
			'polling_delay' => 1,
			'status' => 'running'
		);
		$result = $this->QueueQueue->update($data, false);
		$this->assertIdentical($result, true);
		$this->assertIdentical($this->QueueQueue->selected(), array());

		$this->QueueQueue->deselect();
		$data = array(
			$this->QueueQueue->primaryKey => 1,
			'name' => 'Queue 1',
			'polling_delay' => 1,
			'status' => 'running'
		);
		$result = $this->QueueQueue->update($data, true);
		$this->assertIdentical($result, true);
		$this->assertIdentical($this->QueueQueue->selected(), $data);
	}

	/**
	 * _getId()のテスト
	 */
	public function test_getId() {
		$result = $this->QueueQueue->test_getId(null);
		$this->assertIdentical($result, false);

		$this->QueueQueue->select(1);
		$result = $this->QueueQueue->test_getId(null);
		$this->assertIdentical($result, 1);

		$result = $this->QueueQueue->test_getId(900);
		$this->assertIdentical($result, 900);
	}

	/**
	 * _select()のテスト
	 */
	public function test_select() {
		$result = $this->QueueQueue->test_select();
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->test_select(900);
		$this->assertIdentical($result, false);

		$this->QueueQueue->select(1);
		$result = $this->QueueQueue->test_select();
		$this->assertNotIdentical($result, false);

		$result = $this->QueueQueue->test_select(1);
		$this->assertNotIdentical($result, false);
	}

	/**
	 * _selectByName()のテスト
	 */
	public function test_selectByName() {
		$result = $this->QueueQueue->test_selectByName();
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->test_selectByName('Queue 900');
		$this->assertIdentical($result, false);

		$this->QueueQueue->selectByName('Queue 1');
		$result = $this->QueueQueue->test_selectByName();
		$this->assertNotIdentical($result, false);

		$result = $this->QueueQueue->test_selectByName('Queue 1');
		$this->assertNotIdentical($result, false);
	}

	/**
	 * import()のテスト
	 */
	public function testImport() {
		$data = array(
			'name' => 'Queue 1',
			'polling_delay' => 1,
			'status' => 'running'
		);
		$this->QueueQueue->import($data);
		$result = $this->QueueQueue->selected();
		$this->assertIdentical($result, $data);

		$data = array(
			'name' => 'Queue 1',
			'polling_delay' => 1,
			'status' => 'stopped'
		);
		$this->QueueQueue->import('status', 'stopped');
		$result = $this->QueueQueue->selected();
		$this->assertIdentical($result, $data);
	}

	/**
	 * select()のテスト
	 */
	public function testSelect() {
		$result = $this->QueueQueue->select(900);
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->select(1);
		$this->assertNotIdentical($result, false);
	}

	/**
	 * selecByNamet()のテスト
	 */
	public function testSelectByName() {
		$result = $this->QueueQueue->selectByName('Queue 900');
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->selectByName('Queue 1');
		$this->assertNotIdentical($result, false);
	}

	/**
	 * selected()のテスト
	 */
	public function testSelected() {
		$result = $this->QueueQueue->selected();
		$this->assertIdentical($result, array());

		$this->QueueQueue->select(1);
		$result = $this->QueueQueue->selected();
		$this->assertNotIdentical($result, array());
	}

	/**
	 * deselect()のテスト
	 */
	public function testDeselect() {
		$this->QueueQueue->select(1);
		$this->QueueQueue->deselect();
		$result = $this->QueueQueue->selected();
		$this->assertIdentical($result, array());
	}

	/**
	 * isStatus()のテスト
	 */
	public function testIsStatus() {
		$result = $this->QueueQueue->testIsStatus('running');
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->testIsStatus('running', 1);
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->testIsStatus('running', array());
		$this->assertIdentical($result, false);

		$this->QueueQueue->select(1);
		$result = $this->QueueQueue->testIsStatus('stopped');
		$this->assertIdentical($result, true);

		$result = $this->QueueQueue->testIsStatus('stopped', 1);
		$this->assertIdentical($result, true);

		$job = $this->QueueQueue->select(1);
		$result = $this->QueueQueue->testIsStatus('stopped', $job);
		$this->assertIdentical($result, true);

		$job = $this->QueueQueue->findById(1);
		$result = $this->QueueQueue->testIsStatus('stopped', $job);
		$this->assertIdentical($result, true);
	}

	/**
	 * isStopped()のテスト
	 */
	public function testIsStopped() {
		$result = $this->QueueQueue->isStopped();
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->isStopped(2);
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->isStopped(array());
		$this->assertIdentical($result, false);

		$this->QueueQueue->select(1);
		$result = $this->QueueQueue->isStopped();
		$this->assertIdentical($result, true);

		$result = $this->QueueQueue->isStopped(1);
		$this->assertIdentical($result, true);

		$job = $this->QueueQueue->select(1);
		$result = $this->QueueQueue->isStopped($job);
		$this->assertIdentical($result, true);

		$job = $this->QueueQueue->findById(1);
		$result = $this->QueueQueue->isStopped($job);
		$this->assertIdentical($result, true);
	}

	/**
	 * isRunning()のテスト
	 */
	public function testIsRunning() {
		$result = $this->QueueQueue->isRunning();
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->isRunning(1);
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->isRunning(array());
		$this->assertIdentical($result, false);

		$this->QueueQueue->select(2);
		$result = $this->QueueQueue->isRunning();
		$this->assertIdentical($result, true);

		$result = $this->QueueQueue->isRunning(2);
		$this->assertIdentical($result, true);

		$job = $this->QueueQueue->select(2);
		$result = $this->QueueQueue->isRunning($job);
		$this->assertIdentical($result, true);

		$job = $this->QueueQueue->findById(2);
		$result = $this->QueueQueue->isRunning($job);
		$this->assertIdentical($result, true);
	}

	/**
	 * status()のテスト
	 */
	public function testStatus() {
		$result = $this->QueueQueue->testStatus('stopped');
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->testStatus('stopped', 900);
		$this->assertIdentical($result, false);

		$this->QueueQueue->select(1);
		$result = $this->QueueQueue->testStatus('stopped');
		$this->assertIdentical($result, true);

		$result = $this->QueueQueue->testStatus('stopped', 1);
		$this->assertIdentical($result, true);
	}

	/**
	 * stop()のテスト
	 */
	public function testStop() {
		$result = $this->QueueQueue->stop();
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->stop(900);
		$this->assertIdentical($result, false);

		$this->QueueQueue->select(1);
		$result = $this->QueueQueue->stop();
		$this->assertIdentical($result, true);

		$result = $this->QueueQueue->stop(1);
		$this->assertIdentical($result, true);
	}

	/**
	 * run()のテスト
	 */
	public function testRun() {
		$result = $this->QueueQueue->run();
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->run(900);
		$this->assertIdentical($result, false);

		$this->QueueQueue->select(1);
		$result = $this->QueueQueue->run();
		$this->assertIdentical($result, true);

		$result = $this->QueueQueue->run(1);
		$this->assertIdentical($result, true);
	}

	/**
	 * enqueue()のテスト
	 */
	public function testEnqueue() {
		$result = $this->QueueQueue->enqueue('Job 900', 'job', array());
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->enqueue('Job 900', 'job', array(), 900);
		$this->assertIdentical($result, false);

		$this->QueueQueue->select(1);
		$result = $this->QueueQueue->enqueue('Job 900', 'job');
		$this->assertIdentical($result, true);

		$result = $this->QueueQueue->enqueue('Job 901', 'job', array(), 1);
		$this->assertIdentical($result, true);
	}

	/**
	 * dequeue()のテスト
	 */
	public function testDequeue() {
		$result = $this->QueueQueue->dequeue();
		$this->QueueQueue->QueueJob->select($result['id']);
		$this->QueueQueue->QueueJob->begin();
		$this->QueueQueue->QueueJob->completed();
		$this->assertIdentical($result, false);

		$result = $this->QueueQueue->dequeue(900);
		$this->QueueQueue->QueueJob->select($result['id']);
		$this->QueueQueue->QueueJob->begin();
		$this->QueueQueue->QueueJob->completed();
		$this->assertIdentical($result, false);

		$this->QueueQueue->select(1);
		$result = $this->QueueQueue->dequeue();
		$this->QueueQueue->QueueJob->select($result['id']);
		$this->QueueQueue->QueueJob->begin();
		$this->QueueQueue->QueueJob->completed();
		$this->assertNotIdentical($result, false);

		$result = $this->QueueQueue->dequeue(2);
		$this->QueueQueue->QueueJob->select($result['id']);
		$this->QueueQueue->QueueJob->begin();
		$this->QueueQueue->QueueJob->completed();
		$this->assertNotIdentical($result, false);

		$result = $this->QueueQueue->dequeue(2);
		$this->assertIdentical($result, false);
	}
}
