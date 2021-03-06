<?php
/**
 * ログクラス
 *
 * ログの記録等を行う
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category	Plugin
 * @package		QueuePlugin
 * @author		Phize
 * @copyright	2010 Phize (http://phize.net/)
 * @license		MIT License (http://www.opensource.org/licenses/mit-license.php)
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
			'isDecimalOnCreate' => array(
				'rule' => array('custom', '/^\d+$/'),
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create'
			),
			'existsOnCreate' => array(
				'rule' => array('primaryKeyExists', 'Queue.QueueJob'),
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create'
			),
			'isDecimalOnUpdate' => array(
				'rule' => array('custom', '/^\d+$/'),
				'required' => false,
				'allowEmpty' => false,
				'on' => 'update'
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
	 * @return boolean 処理の成否
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
