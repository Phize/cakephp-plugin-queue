<?php
/**
 * キュークラス
 *
 * キューの取得、ジョブデータの更新等を行う
 *
 * @todo 不正終了したジョブのクリーン処理
 * @todo 完了済みジョブの削除
 */
class QueueQueue extends QueueAppModel {
	/**
	 * アソシエーション
	 *
	 * @var array
	 */
	public $hasMany = array(
		'QueueJob' => array(
			'className' => 'Queue.QueueJob',
			'foreignKey' => 'queue_id',
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
		'name' => array(
			'notEmptyOnCreate' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create'
			),
			'isUniqueOnCreate' => array(
				'rule' => 'isUnique',
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
			'isUniqueOnUpdate' => array(
				'rule' => 'isUnique',
				'required' => false,
				'allowEmpty' => false,
				'on' => 'update'
			)
		),
		'polling_delay' => array(
			'rule' => array('custom', '/^\d+$/'),
			'required' => false,
			'allowEmpty' => false
		),
		'status' => array(
			'rule' => array('inList', array('stopped', 'running')),
			'required' => false,
			'allowEmpty' => false
		)
	);

	/**
	 * カレントキューのデータ
	 *
	 * @var array
	 */
	protected $queue = array();



	/**
	 * キューを追加
	 *
	 * @param string $name キュー名
	 * @param array $options オプション
	 * @param boolean $import カレントキューの更新
	 * @return boolean 処理の成否
	 */
	public function add($name, $options = array(), $import = false) {
		$whitelist = array(
			'name',
			'polling_delay',
			'status'
		);

		$defaults = array(
			'polling_delay' => $this->config['queue']['polling_delay'],
			'status' => $this->config['queue']['status']
		);
		$options = array_merge($defaults, $options);

		$data = array(
			$this->alias => array(
				'name' => $name
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
	 * キューを更新
	 *
	 * $dataがnullの場合はカレントキューのデータでキューを更新
	 * $dataがnull以外の場合は$dataでキューを更新
	 * $dataがnull以外の場合で、$isCurrentがtrueの場合はキューとカレントキューを更新
	 * $dataがnull以外の場合で、$isCurrentがfalseの場合はキューのみを更新
	 *
	 * @param array $data キューのデータ
	 * @param boolean $isCurrent カレントキューの更新
	 * @return boolean 処理の成否
	 */
	public function update($data = null, $isCurrent = false) {
		$whitelist = array(
			$this->primaryKey,
			'name',
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
	 * キューIDを取得
	 *
	 * $idがnullの場合で、カレントキューのデータがある場合はカレントキューのキューIDを返す
	 * $idがnullの場合で、カレントキューのデータがない場合はfalseを返す
	 * $idがnull以外の場合は$idをそのまま返す
	 *
	 * @param integer $id キューID
	 * @return integer キューID、またはfalse
	 */
	protected function _getId($id) {
		$current = $this->selected();
		if ($id === null && !empty($current)) $id = $current[$this->primaryKey];
		if ($id === null) return false;

		return (integer) $id;
	}

	/**
	 * キューIDからキューを取得
	 *
	 * $idがnullの場合で、カレントキューのデータがある場合はカレントキューのデータを返す
	 * $idがnull以外の場合で、キューの取得に成功した場合は取得したキューのデータを返す
	 * それ以外の場合はfalseを返す
	 *
	 * @param integer $id ジョブID
	 * @return array キューの取得に成功した場合はキューのデータ、失敗した場合はfalse
	 */
	protected function _select($id = null) {
		$queue = false;

		if ($id === null) {
			$current = $this->selected();
			if (!empty($current)) $queue = $current;
		}
		else {
			$options = array(
				'conditions' => array(
					$this->alias . '.' . $this->primaryKey => $id
				)
			);

			$data = $this->find('first', $options);
			if (!empty($data)) $queue = $data;
		}

		return $queue;
	}

	/**
	 * キュー名からキューを取得
	 *
	 * $nameがnullの場合で、カレントキューのデータがある場合はカレントキューのデータを返す
	 * $nameがnull以外の場合で、キューの取得に成功した場合は取得したキューのデータを返す
	 * それ以外の場合はfalseを返す
	 *
	 * @param string $name ジョブ名
	 * @return array キューの取得に成功した場合はキューのデータ、失敗した場合はfalse
	 */
	protected function _selectByName($name = null) {
		$queue = false;

		if ($name === null) {
			$current = $this->selected();
			if (!empty($current)) $queue = $current;
		}
		else {
			$options = array(
				'conditions' => array(
					$this->alias . '.name' => $name
				)
			);

			$data = $this->find('first', $options);
			if (!empty($data)) $queue = $data;
		}

		return $queue;
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
			$this->queue = isset($field[$this->alias]) ? $field[$this->alias] : $field;
		}
		else {
			$this->queue[$field] = $value;
		}
	}

	/**
	 * キューIDからキューをカレントキューに読み込み
	 *
	 * @param integer $id キューID
	 * @return array キューの読み込みに成功した場合はデータ、失敗した場合はfalse
	 */
	public function select($id) {
		if (!is_numeric($id) || !($queue = $this->_select($id))) return false;

		$this->import($queue);
		return $queue;
	}

	/**
	 * キュー名からキューをカレントキューに読み込み
	 *
	 * @param string $name キュー名
	 * @return array キューの読み込みに成功した場合はデータ、失敗した場合はfalse
	 */
	public function selectByName($name) {
		if (!($queue = $this->_selectByName($name))) return false;

		$this->import($queue);
		return $queue;
	}

	/**
	 * カレントキューを取得
	 *
	 * $nameがnullの場合はカレントキューのデータを返す
	 * $nameがnull以外の場合で、カレントキューにフィールドがある場合はカレントキューのフィールドを返す
	 *
	 * @param string $field フィールド名
	 * @return array フィールド名をキーとした配列
	 */
	public function selected($field = null) {
		if ($field !== null) return isset($this->queue[$field]) ? $this->queue[$field] : null;

		return $this->queue;
	}

	/**
	 * カレントキューを解除
	 */
	public function deselect() {
		$this->import(array());
	}

	/**
	 * キューのステータスが指定したステータスと等しいか検証
	 *
	 * @param string $status ステータス
	 * @param integer|array $id キューID、またはキューのデータ
	 * @return boolean 検証の成否
	 */
	protected function isStatus($status, $id = null) {
		if (is_array($id)) {
			$queue = $id;
		}
		else {
			if (!($queue = $this->_select($id))) return false;
		}

		$queue = isset($queue[$this->alias]) ? $queue[$this->alias] : $queue;
		if (!isset($queue['status'])) return false;

		return $queue['status'] === $status;
	}

	/**
	 * キューのステータスが停止中か検証
	 *
	 * @param integer|array $id キューID、またはキューのデータ
	 * @return boolean 検証の成否
	 */
	public function isStopped($id = null) {
		return $this->isStatus('stopped', $id);
	}

	/**
	 * キューのステータスが実行中か検証
	 *
	 * @param integer|array $id キューID、またはキューのデータ
	 * @return boolean 検証の成否
	 */
	public function isRunning($id = null) {
		return $this->isStatus('running', $id);
	}

	/**
	 * キューのステータスを変更
	 *
	 * @param string $status ステータス
	 * @param integer $id キューID
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

		return $this->update($data, $isCurrent);
	}

	/**
	 * キューを停止
	 *
	 * @param integer $id キューID
	 * @return boolean 処理の成否
	 */
	public function stop($id = null) {
		return ($this->status('stopped', $id));
	}

	/**
	 * キューを稼働
	 *
	 * @param integer $id キューID
	 * @return boolean 処理の成否
	 */
	public function run($id = null) {
		return ($this->status('running', $id));
	}

	/**
	 * キューにジョブを追加
	 *
	 * @param integer $id キューID
	 * @param string $name ジョブ名
	 * @param string $type ジョブタイプ
	 * @param array $options ジョブのオプション
	 * @return boolean 処理の成否
	 */
	public function enqueue($name, $type, $options = array(), $id = null) {
		if (($id = $this->_getId($id)) === false) return false;

		return $this->QueueJob->add($id, $name, $type, $options);
	}

	/**
	 * キューからジョブを取得
	 *
	 * @param array $types ジョブタイプ
	 * @param integer $id キューID
	 * @return array ジョブの取得に成功した場合はデータ、失敗した場合はfalse
	 */
	public function dequeue($types = null, $id = null) {
		if (($id = $this->_getId($id)) === false) return false;

		return $this->QueueJob->next($id, $types);
	}

	/**
	 * 最大実行時間を超えたジョブを修復
	 *
	 * @param integer $id キューID
	 * @return boolean 処理の成否
	 */
	public function fix($id = null) {
		if (($id = $this->_getId($id)) === false) return false;

		return $this->QueueJob->fixAll($id);
	}

	/**
	 * 実行が完了したジョブを削除
	 *
	 * @param integer $id キューID
	 * @return boolean 処理の成否
	 */
	public function clean($id = null) {
		if (($id = $this->_getId($id)) === false) return false;

		return $this->QueueJob->cleanAll($id);
	}
}
