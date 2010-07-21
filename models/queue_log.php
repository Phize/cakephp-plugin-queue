<?php
/**
 * ログクラス
 *
 * ログの記録等を行う
 */
class QueueLog extends QueueAppModel {
	/**
	 * アソシエーション
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'QueueJob' => array(
			'className' => 'Queue.QueueJob',
			'foreignKey' => 'job_id',
			'conditions' => null,
			'fields' => null,
			'counterCache' => null
		)
	);

	/**
	 * バリデーションルール
	 *
	 * @var array
	 */
	public $validate = array(
		'id' => array(
			'emptyOnCreate' => array(
				'rule' => array('custom', '/^$/'),
				'required' => false,
				'allowEmpty' => false,
				'on' => 'create'
			)
		),
		'job_id' => array(
			'existsOnCreate' => array(
				'rule' => array('primaryKeyExists', 'Queue.QueueJob'),
				'required' => false,
				'allowEmpty' => false,
				'on' => 'create'
			),
			'existsOnUpdate' => array(
				'rule' => array('primaryKeyExists', 'Queue.QueueJob'),
				'required' => false,
				'allowEmpty' => false,
				'on' => 'update'
			)
		),
		'status' => array(
			'NotEmptyOnCreate' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create'
			),
			'NotEmptyOnUpdate' => array(
				'rule' => 'notEmpty',
				'required' => false,
				'allowEmpty' => false,
				'on' => 'update'
			)
		),
		'message' => array(
			'NotEmptyOnCreate' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create'
			),
			'NotEmptyOnUpdate' => array(
				'rule' => 'notEmpty',
				'required' => false,
				'allowEmpty' => false,
				'on' => 'update'
			)
		)
	);



	/**
	 * ログを追加
	 * 
	 * @param integer $jobId ジョブID
	 * @param string $status ステータス
	 * @param string $message メッセージ
	 * @return boolean 記録の成否
	 */
	public function add($jobId, $status, $message) {
		$this->log('[Queue plugin] Job ID = ' . $jobId . ', Status = ' . $status . ', Message = ' . $message);

		$data = array(
			$this->alias => array(
				$this->belongsTo['QueueJob']['foreignKey'] => $jobId,
				'status' => $status,
				'message' => $message
			)
		);

		$this->create($data);
		return ($this->save() !== false);
	}
}
