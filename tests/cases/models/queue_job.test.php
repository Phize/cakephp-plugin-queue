<?php
App::import('Model', 'Queue.QueueJob');

class TestQueueJob extends QueueJob {
	public $name = 'QueueJob';
	public $alias = 'QueeuJob';

	public function test_getId($id) {
		return $this->_getId($id);
	}

	public function test_select($id = null) {
		return $this->_select($id);
	}

	public function testIsStatus($status, $id = null) {
		return $this->isStatus($status, $id);
	}

	public function testStatus($status, $id = null) {
		return $this->status($status, $id);
	}
}

class QueueJobTestCase extends CakeTestCase {
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
		$this->QueueJob =& ClassRegistry::init('Queue.TestQueueJob');
	}

	public function endTest($method) {
		parent::endTest($method);
		unset($this->QueueJob);
		ClassRegistry::flush();
	}

	/**
	 * add()のテスト
	 */
	public function testAdd() {
		$result = $this->QueueJob->add(1, 'Job 1', 'job');
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->add(1, 'Job 900', 'job');
		$this->assertIdentical($result, true);
	}

	/**
	 * log()のテスト
	 */
	public function testLog() {
		$result = $this->QueueJob->log('message');
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->log('message', LOG_ERROR);
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->log('status', 'message');
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->log('status', 'message', 900);
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->log('status', 'message');
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->log('status', 'message', 1);
		$this->assertIdentical($result, true);
	}

	/**
	 * update()のテスト
	 */
	public function testUpdate() {
		$current = $this->QueueJob->select(1);
		$this->QueueJob->deselect();
		$result = $this->QueueJob->update();
		$this->assertIdentical($result, false);
		$this->assertIdentical($this->QueueJob->selected(), array());
		$this->assertIdentical($this->QueueJob->select(1), $current);

		$current = $this->QueueJob->select(1);
		$this->QueueJob->deselect();
		$data = array(
			'queue_id' => '1',
			'name' => 'Job 900',
			'type' => 'job 900',
			'priority' => '99',
			'recursive' => '0',
			'interval' => '60',
			'retry_delay' => '10',
			'scheduled' => date('Y-m-d H:i:s'),
			'status' => 'running'
		);
		$result = $this->QueueJob->update($data, false);
		$this->assertIdentical($result, false);
		$this->assertIdentical($this->QueueJob->selected(), array());
		$this->assertIdentical($this->QueueJob->select(1), $current);

		$current = $this->QueueJob->select(1);
		$this->QueueJob->deselect();
		$data = array(
			'queue_id' => '1',
			'name' => 'Job 900',
			'type' => 'job 900',
			'priority' => '99',
			'recursive' => '0',
			'interval' => '60',
			'retry_delay' => '10',
			'scheduled' => date('Y-m-d H:i:s'),
			'status' => 'running'
		);
		$result = $this->QueueJob->update($data, true);
		$this->assertIdentical($result, false);
		$this->assertIdentical($this->QueueJob->selected(), array());
		$this->assertIdentical($this->QueueJob->select(1), $current);

		$current = $this->QueueJob->select(1);
		$this->QueueJob->deselect();
		$data = array_merge($current, array(
			$this->QueueJob->primaryKey => '1',
			'queue_id' => '1',
			'name' => 'Job 900',
			'type' => 'job 900',
			'priority' => '99',
			'recursive' => '0',
			'interval' => '60',
			'retry_delay' => '10',
			'scheduled' => date('Y-m-d H:i:s'),
			'status' => 'running'
		));
		$this->QueueJob->import($data);
		$result = $this->QueueJob->update();
		$this->assertIdentical($result, true);
		$this->assertIdentical($this->QueueJob->selected(), $data);
		$this->assertIdentical($this->QueueJob->select(1), $data);

		$current = $this->QueueJob->select(1);
		$this->QueueJob->deselect();
		$data = array_merge($current, array(
			$this->QueueJob->primaryKey => '1',
			'queue_id' => '1',
			'name' => 'Job 900',
			'type' => 'job 900',
			'priority' => '99',
			'recursive' => '0',
			'interval' => '60',
			'retry_delay' => '10',
			'scheduled' => date('Y-m-d H:i:s'),
			'status' => 'running'
		));
		$result = $this->QueueJob->update($data, false);
		$this->assertIdentical($result, true);
		$this->assertIdentical($this->QueueJob->selected(), array());
		$this->assertIdentical($this->QueueJob->select(1), $data);

		$current = $this->QueueJob->select(1);
		$this->QueueJob->deselect();
		$data = array_merge($current, array(
			$this->QueueJob->primaryKey => '1',
			'queue_id' => '1',
			'name' => 'Job 900',
			'type' => 'job 900',
			'priority' => '99',
			'recursive' => '0',
			'interval' => '60',
			'retry_delay' => '10',
			'scheduled' => date('Y-m-d H:i:s'),
			'status' => 'running'
		));
		$result = $this->QueueJob->update($data, true);
		$this->assertIdentical($result, true);
		$this->assertIdentical($this->QueueJob->selected(), $data);
		$this->assertIdentical($this->QueueJob->select(1), $data);
	}

	/**
	 * _getId()のテスト
	 */
	public function test_getId() {
		$result = $this->QueueJob->test_getId(null);
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->test_getId(null);
		$this->assertIdentical($result, 1);

		$result = $this->QueueJob->test_getId(900);
		$this->assertIdentical($result, 900);
	}

	/**
	 * _select()のテスト
	 */
	public function test_select() {
		$result = $this->QueueJob->test_select();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->test_select(900);
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->test_select();
		$this->assertNotIdentical($result, false);

		$result = $this->QueueJob->test_select(1);
		$this->assertNotIdentical($result, false);
	}

	/**
	 * import()のテスト
	 */
	public function testImport() {
		$data = array(
				'queue_id' => 1,
				'name' => 'Job 1',
				'type' => 'job',
				'priority' => 50,
				'recursive' => 1,
				'interval' => 86400,
				'retry_delay' => 60,
				'scheduled' => date('Y-m-d H:i:s'),
				'status' => 'idle'
		);
		$this->QueueJob->import($data);
		$result = $this->QueueJob->selected();
		$this->assertIdentical($result, $data);

		$data = array(
				'queue_id' => 1,
				'name' => 'Job 1',
				'type' => 'job',
				'priority' => 50,
				'recursive' => 1,
				'interval' => 86400,
				'retry_delay' => 60,
				'scheduled' => date('Y-m-d H:i:s'),
				'status' => 'stopped'
		);
		$this->QueueJob->import('status', 'stopped');
		$result = $this->QueueJob->selected();
		$this->assertIdentical($result, $data);
	}

	/**
	 * selecByNamet()のテスト
	 */
	public function testSelect() {
		$result = $this->QueueJob->select(900);
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->select(1);
		$this->assertNotIdentical($result, false);
	}

	/**
	 * selected()のテスト
	 */
	public function testSelected() {
		$result = $this->QueueJob->selected();
		$this->assertIdentical($result, array());

		$this->QueueJob->select(1);
		$result = $this->QueueJob->selected();
		$this->assertNotIdentical($result, array());
	}

	/**
	 * next()のテスト
	 */
	public function testNext() {
		$result = $this->QueueJob->next(900);
		$this->QueueJob->select($result['id']);
		$this->QueueJob->begin();
		$this->QueueJob->completed();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->next(1, 'job 900');
		$this->QueueJob->select($result['id']);
		$this->QueueJob->begin();
		$this->QueueJob->completed();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->next(1);
		$this->assertNotIdentical($result, false);

		$result = $this->QueueJob->next(2);
		$this->QueueJob->select($result['id']);
		$this->QueueJob->begin();
		$this->QueueJob->completed();
		$this->assertNotIdentical($result, false);

		$result = $this->QueueJob->next(2);
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->next(1, 'job 1');
		$this->QueueJob->select($result['id']);
		$this->QueueJob->begin();
		$this->QueueJob->completed();
		$this->assertNotIdentical($result, false);

		$result = $this->QueueJob->next(1, 'job 1');
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->next(1, 'job 2');
		$this->QueueJob->select($result['id']);
		$this->QueueJob->begin();
		$this->QueueJob->completed();
		$this->assertNotIdentical($result, false);

		$result = $this->QueueJob->next(1, array('job 1', 'job 2'));
		$this->QueueJob->select($result['id']);
		$this->QueueJob->begin();
		$this->QueueJob->completed();
		$this->assertNotIdentical($result, false);

		$result = $this->QueueJob->next(1, array('job 1', 'job 2'));
		$this->assertIdentical($result, false);
	}

	/**
	 * deselect()のテスト
	 */
	public function testDeselect() {
		$this->QueueJob->select(1);
		$this->QueueJob->deselect();
		$result = $this->QueueJob->selected();
		$this->assertIdentical($result, array());
	}

	/**
	 * isStatus()のテスト
	 */
	public function testIsStatus() {
		$result = $this->QueueJob->testIsStatus('idle');
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->testIsStatus('idle', 3);
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->testIsStatus('idle', array());
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->testIsStatus('idle');
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->testIsStatus('idle', 1);
		$this->assertIdentical($result, true);

		$job = $this->QueueJob->select(1);
		$result = $this->QueueJob->testIsStatus('idle', $job);
		$this->assertIdentical($result, true);

		$job = $this->QueueJob->findById(1);
		$result = $this->QueueJob->testIsStatus('idle', $job);
		$this->assertIdentical($result, true);
	}

	/**
	 * isIdle()のテスト
	 */
	public function testIsIdle() {
		$result = $this->QueueJob->isIdle();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->isIdle(3);
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->isIdle(array());
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->isIdle();
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->isIdle(1);
		$this->assertIdentical($result, true);

		$job =$this->QueueJob->select(1);
		$result = $this->QueueJob->isIdle($job);
		$this->assertIdentical($result, true);

		$job =$this->QueueJob->findById(1);
		$result = $this->QueueJob->isIdle($job);
		$this->assertIdentical($result, true);
	}

	/**
	 * isStopped()のテスト
	 */
	public function testIsStopped() {
		$result = $this->QueueJob->isStopped();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->isStopped(1);
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->isStopped(array());
		$this->assertIdentical($result, false);

		$this->QueueJob->select(3);
		$result = $this->QueueJob->isStopped();
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->isStopped(3);
		$this->assertIdentical($result, true);

		$job = $this->QueueJob->select(3);
		$result = $this->QueueJob->isStopped($job);
		$this->assertIdentical($result, true);

		$job = $this->QueueJob->findById(3);
		$result = $this->QueueJob->isStopped($job);
		$this->assertIdentical($result, true);
	}

	/**
	 * isRunning()のテスト
	 */
	public function testIsRunning() {
		$result = $this->QueueJob->isRunning();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->isRunning(1);
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->isRunning(array());
		$this->assertIdentical($result, false);

		$this->QueueJob->select(4);
		$result = $this->QueueJob->isRunning();
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->isRunning(4);
		$this->assertIdentical($result, true);

		$job = $this->QueueJob->select(4);
		$result = $this->QueueJob->isRunning($job);
		$this->assertIdentical($result, true);

		$job = $this->QueueJob->findById(4);
		$result = $this->QueueJob->isRunning($job);
		$this->assertIdentical($result, true);
	}

	/**
	 * isSuccess()のテスト
	 */
	public function testIsSuccess() {
		$result = $this->QueueJob->isSuccess();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->isSuccess(1);
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->isSuccess(array());
		$this->assertIdentical($result, false);

		$this->QueueJob->select(5);
		$result = $this->QueueJob->isSuccess();
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->isSuccess(5);
		$this->assertIdentical($result, true);

		$job = $this->QueueJob->select(5);
		$result = $this->QueueJob->isSuccess($job);
		$this->assertIdentical($result, true);

		$job = $this->QueueJob->findById(5);
		$result = $this->QueueJob->isSuccess($job);
		$this->assertIdentical($result, true);
	}

	/**
	 * isError()のテスト
	 */
	public function testIsError() {
		$result = $this->QueueJob->isError();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->isError(1);
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->isError(array());
		$this->assertIdentical($result, false);

		$this->QueueJob->select(6);
		$result = $this->QueueJob->isError();
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->isError(6);
		$this->assertIdentical($result, true);

		$job = $this->QueueJob->select(6);
		$result = $this->QueueJob->isError($job);
		$this->assertIdentical($result, true);

		$job = $this->QueueJob->findById(6);
		$result = $this->QueueJob->isError($job);
		$this->assertIdentical($result, true);
	}

	/**
	 * isRunnable()のテスト
	 */
	public function testIsRunnable() {
		$result = $this->QueueJob->isRunnable();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->isRunnable(7);
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->isRunnable(array());
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->isRunnable();
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->isRunnable(1);
		$this->assertIdentical($result, true);

		$job = $this->QueueJob->select(1);
		$result = $this->QueueJob->isRunnable($job);
		$this->assertIdentical($result, true);

		$job = $this->QueueJob->findById(1);
		$result = $this->QueueJob->isRunnable($job);
		$this->assertIdentical($result, true);
	}

	/**
	 * status()のテスト
	 */
	public function testStatus() {
		$result = $this->QueueJob->testStatus('idle');
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->testStatus('idle', 900);
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->testStatus('idle');
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->testStatus('idle', 1);
		$this->assertIdentical($result, true);
	}

	/**
	 * idle()のテスト
	 */
	public function testIdle() {
		$result = $this->QueueJob->idle();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->idle(900);
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->idle();
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->idle(1);
		$this->assertIdentical($result, true);
	}

	/**
	 * stop()のテスト
	 */
	public function testStop() {
		$result = $this->QueueJob->stop();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->stop(900);
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->stop();
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->stop(1);
		$this->assertIdentical($result, true);
	}

	/**
	 * run()のテスト
	 */
	public function testRun() {
		$result = $this->QueueJob->run();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->run(900);
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->run();
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->run(1);
		$this->assertIdentical($result, true);
	}

	/**
	 * success()のテスト
	 */
	public function testSuccess() {
		$result = $this->QueueJob->success();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->success(900);
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->success();
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->success(1);
		$this->assertIdentical($result, true);
	}

	/**
	 * error()のテスト
	 */
	public function testError() {
		$result = $this->QueueJob->error();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->error(900);
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->error();
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->error(1);
		$this->assertIdentical($result, true);
	}

	/**
	 * begin()のテスト
	 */
	public function testBegin() {
		$result = $this->QueueJob->begin();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->begin(7);
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->begin();
		$this->assertIdentical($result, true);

		$result = $this->QueueJob->begin(2);
		$this->assertIdentical($result, true);
	}

	/**
	 * completed()のテスト
	 */
	public function testCompleted() {
		$result = $this->QueueJob->completed();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->completed(1);
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->completed();
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$this->QueueJob->begin();
		$result = $this->QueueJob->completed();
		$this->assertIdentical($result, true);

		$this->QueueJob->begin(2);
		$result = $this->QueueJob->completed(2);
		$this->assertIdentical($result, true);
	}

	/**
	 * failed()のテスト
	 */
	public function testFailed() {
		$result = $this->QueueJob->failed();
		$this->assertIdentical($result, false);

		$result = $this->QueueJob->failed(1);
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$result = $this->QueueJob->failed();
		$this->assertIdentical($result, false);

		$this->QueueJob->select(1);
		$this->QueueJob->begin();
		$result = $this->QueueJob->failed();
		$this->assertIdentical($result, true);

		$this->QueueJob->begin(2);
		$result = $this->QueueJob->failed(2);
		$this->assertIdentical($result, true);
	}
}
