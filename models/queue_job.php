<?php
/**
 * ジョブクラス
 *
 * ジョブの取得、ジョブデータの更新等を行う
 */
class QueueJob extends QueueAppModel {
	/**
	 * アソシエーション
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'QueueQueue' => array(
			'className' => 'Queue.QueueQueue',
			'foreignKey' => 'queue_id',
			'conditions' => null,
			'fields' => null,
			'counterCache' => null
		)
	);
	public $hasMany = array(
		'QueueLog' => array(
			'className' => 'Queue.QueueLog',
			'foreignKey' => 'job_id',
			'conditions' => null,
			'fields' => null,
			'order' => null,
			'limit' => null,
			'offset' => null,
			'dependent' => true,
			'exclusive' => null,
			'finderQuery' => null
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
		'queue_id' => array(
			'isDecimalOnCreate' => array(
				'rule' => array('custom', '/^\d+$/'),
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create'
			),
			'primaryKeyExistsOnCreate' => array(
				'rule' => array('primaryKeyExists', 'Queue.QueueQueue'),
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create'
			),
			'isUniqueWithOnCreate' => array(
				'rule' => array('isUniqueWith', 'name'),
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
			'primaryKeyExistsOnUpdate' => array(
				'rule' => array('primaryKeyExists', 'Queue.QueueQueue'),
				'required' => false,
				'allowEmpty' => false,
				'on' => 'update'
			),
			'isUniqueWithOnUpdate' => array(
				'rule' => array('isUniqueWith', 'name'),
				'required' => false,
				'allowEmpty' => false,
				'on' => 'update'
			)
		),
		'name' => array(
			'notEmptyOnCreate' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create'
			),
			'isUniqueWithOnCreate' => array(
				'rule' => array('isUniqueWith', 'queue_id'),
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create'
			),
			'notEmptyOnUpdate' => array(
				'rule' => 'notEmpty',
				'required' => false,
				'allowEmpty' => false,
				'on' => 'update'
			),
			'isUniqueWithOnUpdate' => array(
				'rule' => array('isUniqueWith', 'queue_id'),
				'required' => false,
				'allowEmpty' => false,
				'on' => 'update'
			)
		),
		'type' => array(
			'notEmptyOnCreate' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create'
			),
			'notEmptyOnUpdate' => array(
				'rule' => 'notEmpty',
				'required' => false,
				'allowEmpty' => false,
				'on' => 'update'
			)
		),
		'priority' => array(
			'rule' => array('custom', '/^\d+$/'),
			'required' => false,
			'allowEmpty' => false
		),
		'recursive' => array(
			'rule' => array('custom', '/^0|1$/'),
			'required' => false,
			'allowEmpty' => false
		),
		'interval' => array(
			'rule' => array('custom', '/^\d+$/'),
			'required' => false,
			'allowEmpty' => false
		),
		'retry_delay' => array(
			'rule' => array('custom', '/^\d+$/'),
			'required' => false,
			'allowEmpty' => false
		),
		'tries' => array(
			'rule' => array('custom', '/^\d+$/'),
			'required' => false,
			'allowEmpty' => false
		),
		'max_tries' => array(
			'rule' => array('custom', '/^\d+$/'),
			'required' => false,
			'allowEmpty' => false
		),
		'parameters' => array(
			'rule' => 'notEmpty',
			'required' => false,
			'allowEmpty' => true
		),
		'scheduled' => array(
			'isDatetimeOnCreate' => array(
				'rule' => array('isDatetime'),
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create'
			),
			'isDatetimeOnUpdate' => array(
				'rule' => array('isDatetime'),
				'required' => false,
				'allowEmpty' => false,
				'on' => 'update'
			)
		),
		'tried' => array(
			'rule' => array('isDatetime'),
			'required' => false,
			'allowEmpty' => false
		),
		'completed' => array(
			'rule' => array('isDatetime'),
			'required' => false,
			'allowEmpty' => false
		),
		'polling_delay' => array(
			'rule' => array('custom', '/^\d+$/'),
			'required' => false,
			'allowEmpty' => false
		),
		'status' => array(
			'rule' => array('inList', array('idle', 'stopped', 'running', 'success', 'error')),
			'required' => false,
			'allowEmpty' => false
		)
	);

	/**
	 * カレントジョブのデータ
	 *
	 * @var array
	 */
	protected $job = array();



	/**
	 * ジョブを追加
	 *
	 * @params integer $queueId キューID
	 * @param string $name ジョブ名
	 * @param string $type ジョブタイプ
	 * @param array $options オプション
	 * @param boolean $import カレントジョブの更新
	 * @return boolean 処理の成否
	 */
	public function add($queueId, $name, $type, $options = array(), $import = false) {
		$whitelist = array(
			$this->belongsTo['QueueQueue']['foreignKey'],
			'name',
			'type',
			'priority',
			'recursive',
			'interval',
			'retry_delay',
			'max_tries',
			'parameters',
			'scheduled',
			'polling_delay',
			'status'
		);

		$defaults = array(
			'priority' => $this->config['job']['priority'],
			'recursive' => $this->config['job']['recursive'],
			'interval' => $this->config['job']['interval'],
			'retry_delay' => $this->config['job']['retry_delay'],
			'max_tries' => $this->config['job']['max_tries'],
			'scheduled' => date('Y-m-d H:i:s'),
			'polling_delay' => $this->config['job']['polling_delay'],
			'status' => $this->config['job']['status']
		);
		$options = array_merge($defaults, $options);

		$data = array(
			$this->alias => array(
				$this->belongsTo['QueueQueue']['foreignKey'] => $queueId,
				'name' => $name,
				'type' => $type
			)
		);
		$data = Set::merge(array($this->alias => $options), $data);

		$this->create(null);
		if (!$this->save($data, true, $whitelist)) return false;

		if ($import) {
			$this->deselect();
			$this->select((int) $this->getID());
		}

		return true;
	}

	/**
	 * ジョブのログを記録
	 *
	 * $messageが整数の場合で、かつ$idがnullの場合はCakePHPのObject::log()を呼び出す
	 *
	 * @param string $status ステータス、またはメッセージ
	 * @param string|integer $message メッセージ、またはエラータイプ
	 * @param integer $id ジョブID
	 * @return boolean 処理の成否
	 */
	public function log($status, $message = LOG_ERROR, $id = null) {
		if (is_int($message) && $id === null) {
			return parent::log($status, $message);
		}

		if (($id = $this->_getId($id)) === false) return false;

		return $this->QueueLog->add($id, $status, $message);
	}

	/**
	 * ジョブを更新
	 *
	 * $dataがnullの場合はカレントジョブのデータでジョブを更新
	 * $dataがnull以外の場合は$dataでジョブを更新
	 * $dataがnull以外の場合で、$isCurrentがtrueの場合はジョブとカレントジョブを更新
	 * $dataがnull以外の場合で、$isCurrentがfalseの場合はジョブのみを更新
	 *
	 * @param array $data ジョブのデータ
	 * @param boolean $isCurrent カレントジョブの更新
	 * @return boolean 処理の成否
	 */
	public function update($data = null, $isCurrent = false) {
		$whitelist = array(
			$this->primaryKey,
			$this->belongsTo['QueueQueue']['foreignKey'],
			'name',
			'type',
			'priority',
			'recursive',
			'interval',
			'retry_delay',
			'tries',
			'max_tries',
			'parameters',
			'scheduled',
			'tried',
			'completed',
			'polling_delay',
			'status'
		);

		if ($data === null) {
			$current = $this->selected();
			if (empty($current) || !isset($current[$this->primaryKey])) return false;

			$this->create(null);
			if (!$this->save($current, true, $whitelist)) return false;
		}
		else {
			$data = isset($data[$this->alias]) ? $data : array($this->alias => $data);
			if (!isset($data[$this->alias][$this->primaryKey])) return false;

			$this->create(null);
			if (!$this->save($data, true, $whitelist)) return false;

			if ($isCurrent) {
				$current = $this->selected();
				if (empty($current)) $this->select($data[$this->alias][$this->primaryKey]);
				$this->import(array_merge($current, $data[$this->alias]));
			}
		}

		return true;
	}

	/**
	 * ジョブIDを取得
	 *
	 * $idがnullの場合で、カレントジョブのデータがある場合はカレントジョブのジョブIDを返す
	 * $idがnullの場合で、カレントジョブのデータがない場合はfalseを返す
	 * $idがnull以外の場合は$idをそのまま返す
	 *
	 * @param integer $id ジョブID
	 * @return integer ジョブID、またはfalse
	 */
	protected function _getId($id) {
		$current = $this->selected();
		if ($id === null && !empty($current)) $id = $current[$this->primaryKey];
		if ($id === null) return false;

		return (integer) $id;
	}

	/**
	 * ジョブを取得
	 *
	 * $idがnullの場合で、カレントジョブのデータがある場合はカレントジョブのデータを返す
	 * $idがnull以外の場合で、ジョブの取得に成功した場合は取得したジョブのデータを返す
	 * それ以外の場合はfalseを返す
	 *
	 * @param integer $id ジョブID
	 * @return array ジョブの取得に成功した場合はジョブのデータ、失敗した場合はfalse
	 */
	protected function _select($id = null) {
		$job = false;

		$current = $this->selected();
		if ($id === null) {
			if (!empty($current)) $job = $current;
		}
		else {
			$options = array(
				'conditions' => array(
					$this->alias . '.' . $this->primaryKey => $id
				)
			);

			$data = $this->find('first', $options);
			if (!empty($data)) $job = $data;
		}

		return $job;
	}

	/**
	 * データをカレントキューに読み込み
	 *
	 * $fieldが配列の場合は、カレントキューを$fieldで置き換え
	 * $fieldが配列以外の場合は、カレントキューの$fieldフィールドに$valueの値を設定
	 *
	 * @param array|string $field キューのデータ、またはフィールド名
	 * @param mixed $value フィールドの値
	 */
	public function import($field, $value = null) {
		if (is_array($field)) {
			$this->job = isset($field[$this->alias]) ? $field[$this->alias] : $field;
		}
		else {
			$this->job[$field] = $value;
		}
	}

	/**
	 * ジョブIDからジョブをカレントジョブに読み込み
	 *
	 * ジョブを取得してカレントジョブに設定
	 *
	 * @param integer $id ジョブID
	 * @return array ジョブの選択に成功した場合はデータ、失敗した場合はfalse
	 */
	public function select($id) {
		if (!is_numeric($id) || !($job = $this->_select($id))) return false;

		$this->import($job);
		return $job;
	}

	/**
	 * カレントジョブのデータを取得
	 *
	 * $fieldがnullの場合はカレントジョブのデータを返す
	 * $fieldがnull以外の場合で、カレントジョブにフィールドがある場合はカレントジョブのフィールドを返す
	 *
	 * @param string $field フィールド名
 	 * @return array フィールド名をキーとした配列
	 */
	public function selected($field = null) {
		if ($field !== null) return isset($this->job[$field]) ? $this->job[$field] : null;

		return $this->job;
	}

	/**
	 * カレントキューを解除
	 */
	public function deselect() {
		$this->import(array());
	}

	/**
	 * 次のジョブを取得
	 *
	 * @param integer $queueId キューID
	 * @param array $types ジョブタイプ
	 * @return array ジョブの読み込みに成功した場合はデータ、失敗した場合はfalse
	 */
	public function next($queueId, $types = null) {
		$options = array(
			'conditions' => array(
				$this->alias . '.' . $this->belongsTo['QueueQueue']['foreignKey'] => $queueId,
				$this->alias . '.scheduled <=' => date('Y-m-d H:i:s'),
				$this->alias . '.tries < ' . $this->alias . '.max_tries',
				$this->alias . '.status' => 'idle'
			),
			'order' => array(
				$this->alias . '.priority' => 'desc',
				$this->alias . '.scheduled' => 'asc',
				$this->alias . '.' . $this->primaryKey => 'asc'
			)
		);

		// ジョブタイプの検索条件を生成
		if ($types !== null) {
			if (!is_array($types)) $types = array($types);

			$type_conditions = array();
			foreach ($types as $type) {
				$type_conditions[] = array($this->alias . '.type' => $type);
			}

			$options['conditions']['and'] = array('or' => $type_conditions);
		}

		$data = $this->find('first', $options);
		if (empty($data)) return false;

		$this->import($data[$this->alias]);
		return $data[$this->alias];
	}

	/**
	 * ジョブのジョブタイプが指定したジョブタイプと等しいか検証
	 *
	 * @param string $type ジョブタイプ
	 * @param integer|array $id ジョブID、またはジョブのデータ
	 * @return boolean 検証の成否
	 */
	public function isType($type, $id = null) {
		if (is_array($id)) {
			$job = $id;
		}
		else {
			if (!($job = $this->_select($id))) return false;
		}

		$job = isset($job[$this->alias]) ? $job[$this->alias] : $job;
		if (!isset($job['type'])) return false;

		return $job['type'] === $type;
	}

	/**
	 * ジョブのステータスが指定したステータスと等しいか検証
	 *
	 * @param string $status ステータス
	 * @param integer|array $id ジョブID、またはジョブのデータ
	 * @return boolean 検証の成否
	 */
	protected function isStatus($status, $id = null) {
		if (is_array($id)) {
			$job = $id;
		}
		else {
			if (!($job = $this->_select($id))) return false;
		}

		$job = isset($job[$this->alias]) ? $job[$this->alias] : $job;
		if (!isset($job['status'])) return false;

		return $job['status'] === $status;
	}

	/**
	 * ジョブのステータスが実行待ち中か検証
	 *
	 * @param integer|array $id ジョブID、またはジョブのデータ
	 * @return boolean 検証の成否
	 */
	public function isIdle($id = null) {
		return $this->isStatus('idle', $id);
	}

	/**
	 * ジョブのステータスが停止中か検証
	 *
	 * @param integer|array $id ジョブID、またはジョブのデータ
	 * @return boolean 検証の成否
	 */
	public function isStopped($id = null) {
		return $this->isStatus('stopped', $id);
	}

	/**
	 * ジョブのステータスが実行中か検証
	 *
	 * @param integer|array $id ジョブID、またはジョブのデータ
	 * @return boolean 検証の成否
	 */
	public function isRunning($id = null) {
		return $this->isStatus('running', $id);
	}

	/**
	 * ジョブのステータスが成功済みか検証
	 *
	 * @param integer|array $id ジョブID、またはジョブのデータ
	 * @return boolean 検証の成否
	 */
	public function isSuccess($id = null) {
		return $this->isStatus('success', $id);
	}

	/**
	 * ジョブのステータスがエラーか検証
	 *
	 * @param integer|array $id ジョブID、またはジョブのデータ
	 * @return boolean 検証の成否
	 */
	public function isError($id = null) {
		return $this->isStatus('error', $id);
	}

	/**
	 * ジョブが実行可能か検証
	 *
	 * @param integer|array $id ジョブID、またはジョブのデータ
	 * @return boolean 検証の成否
	 */
	public function isRunnable($id = null) {
		if (is_array($id)) {
			$job = $id;
		}
		else {
			if (!($job = $this->_select($id))) return false;
		}

		$job = isset($job[$this->alias]) ? $job[$this->alias] : $job;
		if (!isset($job['tries']) || !isset($job['max_tries']) || !isset($job['scheduled']) || !isset($job['status'])) return false;

		return ($job['tries'] < $job['max_tries']) && (strtotime($job['scheduled']) < time()) && $job['status'] === 'idle';
	}

	/**
	 * ジョブのステータスを変更
	 *
	 * @param string $status ステータス
	 * @param integer $id ジョブID
	 * @return boolean 処理の成否
	 */
	protected function status($status, $id = null) {
		$isCurrent = ($id === null) ? true : false;
		if (($id = $this->_getId($id)) === false) return false;

		$data = array(
			$this->alias => array(
				$this->primaryKey => $id,
				'status' => $status
			)
		);

		return $this->update($data, true);
	}

	/**
	 * ジョブのステータスを実行待ちに変更
	 *
	 * @param integer $id ジョブID
	 * @return boolean 処理の成否
	 */
	public function idle($id = null) {
		return ($this->status('idle', $id));
	}

	/**
	 * ジョブをステータスを停止に変更
	 *
	 * @param integer $id ジョブID
	 * @return boolean 処理の成否
	 */
	public function stop($id = null) {
		return ($this->status('stopped', $id));
	}

	/**
	 * ジョブのステータスを実行に変更
	 *
	 * @param integer $id ジョブID
	 * @return boolean 処理の成否
	 */
	public function run($id = null) {
		return ($this->status('running', $id));
	}

	/**
	 * ジョブのステータスを成功済みに変更
	 *
	 * @param integer $id ジョブID
	 * @return boolean 処理の成否
	 */
	public function success($id = null) {
		return ($this->status('success', $id));
	}

	/**
	 * ジョブのステータスをエラーに変更
	 *
	 * @param integer $id ジョブID
	 * @return boolean 処理の成否
	 */
	public function error($id = null) {
		return ($this->status('error', $id));
	}

	/**
	 * ジョブの開始処理
	 *
	 * @param integer $id ジョブID
	 * @return boolean 処理の成否
	 */
	public function begin($id = null) {
		$isCurrent = ($id === null) ? true : false;

		if (!($job = $this->_select($id))) return false;
		if (!$this->isRunnable($job)) return false;
		$job = isset($job[$this->alias]) ? $job[$this->alias] : $job;

		$data = array(
			$this->alias => array(
				$this->primaryKey => $job[$this->primaryKey],
				'tries' => $job['tries'] + 1,
				'tried' => date('Y-m-d H:i:s'),
				'status' => 'running'
			)
		);

		return $this->update($data, $isCurrent);
	}

	/**
	 * ジョブの成功処理
	 *
	 * @param integer $id ジョブID
	 * @return boolean 処理の成否
	 */
	public function completed($id = null) {
		$isCurrent = ($id === null) ? true : false;

		if (!($job = $this->_select($id))) return false;
		if (!$this->isRunning($job)) return false;
		$job = isset($job[$this->alias]) ? $job[$this->alias] : $job;

		$data = array();
		if ($job['recursive']) {
			$data = array(
				$this->alias => array(
					$this->primaryKey => $job[$this->primaryKey],
					'tries' => 0,
					'scheduled' => date('Y-m-d H:i:s', time() + $job['interval']),
					'completed' => date('Y-m-d H:i:s'),
					'status' => 'idle'
				)
			);
		}
		else {
			$data = array(
				$this->alias => array(
					$this->primaryKey => $job[$this->primaryKey],
					'tries' => 0,
					'completed' => date('Y-m-d H:i:s'),
					'status' => 'success'
				)
			);
		}

		return $this->update($data, $isCurrent);
	}

	/**
	 * ジョブの失敗処理
	 *
	 * @param integer $id ジョブID
	 * @return boolean 処理の成否
	 */
	public function failed($id = null) {
		$isCurrent = ($id === null) ? true : false;

		if (!($job = $this->_select($id))) return false;
		if (!$this->isRunning($job)) return false;
		$job = isset($job[$this->alias]) ? $job[$this->alias] : $job;

		$data = array();
		if ($job['tries'] + 1 >= $job['max_tries']) {
			$data = array(
				$this->alias => array(
					$this->primaryKey => $job[$this->primaryKey],
					'status' => 'error'
				)
			);
		}
		else {
			$data = array(
				$this->alias => array(
					$this->primaryKey => $job[$this->primaryKey],
					'scheduled' => date('Y-m-d H:i:s', time() + $job['retry_delay']),
					'status' => 'idle'
				)
			);
		}

		return $this->update($data, $isCurrent);
	}

	/**
	 * 最大実行時間を超えたジョブを修復
	 *
	 * @param integer $queueId キューID
	 * @return boolean 処理の成否
	 */
	public function fixAll($queueId = null) {
		$this->beginTransaction();

		$fields = array(
			$this->alias . '.tries' => '`' . $this->alias . '`.`tries` - 1'
		);
		$conditions = array(
			$this->alias . '.status' => 'running',
			$this->alias . '.tried >=' => $this->config['job']['time_limit'],
			$this->alias . '.tries > ' => 0
		);
		if ($queueId !== null) $conditions[$this->belongsTo['QueueQueue']['foreignKey']] = $queueId;

		if (!$this->updateAll($fields, $conditions)) {
			$this->rollbackTransaction();
			return false;
		}

		$fields = array(
			$this->alias . '.status' => '\'idle\'',
		);
		$conditions = array(
			$this->alias . '.status' => 'running',
			$this->alias . '.tried >=' => $this->config['job']['time_limit']
		);
		if ($queueId !== null) $conditions[$this->belongsTo['QueueQueue']['foreignKey']] = $queueId;

		if (!$this->updateAll($fields, $conditions)) {
			$this->rollbackTransaction();
			return false;
		}

		$this->commitTransaction();

		return true;
	}

	/**
	 * 実行が完了したジョブを削除
	 *
	 * @param integer $queueId キューID
	 * @return boolean 処理の成否
	 */
	public function cleanAll($queueId = null) {
		$conditions = array(
			'or' => array(
				array(
					$this->alias . '.status' => 'success'
				),
				array(
					$this->alias . '.status' => 'error'
				)
			)
		);

		if ($queueId !== null) $conditions[$this->belongsTo['QueueQueue']['foreignKey']] = $queueId;

		return ($this->deleteAll($conditions) !== false);
	}
}
