<?php
/**
 * 共通モデルクラス
 *
 * Queueプラグインの共通モデル
 * Queueプラグインの全てのモデルはこのクラスを継承する
 */
class QueueAppModel extends AppModel {
	/**
	 * ビヘイビア
	 *
	 * @var array
	 */
	public $actsAs = array('containable');

	/**
	 * アソシエーションのレベル
	 *
	 * @var integer
	 */
	public $recursive = -1;

	/**
	 * プラグインの設定
	 *
	 * @var array
	 */
	protected $config = array();



	/**
	 * コンストラクター
	 *
	 * @param mixed $id モデルのID、またはオプション配列
	 * @param string $table 使用するデータベースのテーブル名
	 * @param string $ds データソースの接続名
	 */
    function __construct($id = false, $table = null, $ds = null) {
		if (Configure::read('Queue.database')) {
            $dbAvailable = in_array(Configure::read('Queue.database'), array_keys(ConnectionManager::enumConnectionObjects()));
            $this->useDbConfig = $dbAvailable ? Configure::read('Queue.database') : $this->useDbConfig;
        }

		parent::__construct($id, $table, $ds);

		$this->config = Configure::read('Queue');
	}

	/**
	 * Datetime型の検証
	 *
	 * @param array $data データ
	 * @return boolean 検証の結果
	 */
	public function isDatetime($data) {
		$field = key($data);

		return preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $data[$field]) ? true : false;
	}

	/**
	 * キーが存在するか検証
	 *
	 * @param array $data データ
	 * @param string model モデル名
	 * @return boolean 検証の結果
	 * @todo saveAll()での保存に対応させる
	 */
	public function primaryKeyExists($data, $model) {
		$field = key($data);

		list($plugin, $class) = pluginSplit($model);

		$result = $this->{$class}->find('count',
			array(
				'conditions' => array($this->{$class}->alias . '.' . $this->{$class}->primaryKey => $data),
				'recursive' => -1
			)
		);

		return ($result > 0) ? true : false;
	}

	/**
	 * 複数フィールドの組み合わせがユニークか検証
	 *
	 * 正しい検証を行うには、$fieldsにarray(フィールド名 => 値)と指定するか、
	 * $fieldsにarray(フィールド名)と指定した場合に、フィールド全てのデータが$this->dataに存在している必要がある
     * 指定したフィールドのデータが存在しない場合は、そのフィールド値はnullとして扱われる
	 * 一部のフィールドを省略してデータを更新する場合や、
	 * 一部のフィールドを省略してデータベースのデフォルト値を使用する場合には、正しい検証が行えないため注意が必要
	 * ただし、検証対象のフィールド群に外部キーのフィールドを含む場合で、
	 * かつ関連するテーブルのデータとともにsaveAll()でレコードを作成・更新する場合は、
	 * $this->dataに外部キーのフィールドが自動的に追加されるため、外部キーのフィールドについては省略できる
	 *
	 * @param array $data データ
	 * @param array $fields フィールド名
	 * @return boolean 検証の結果
	 */
	public function isUniqueWith($data, $fields) {
		if (!is_array($fields)) $fields = array($fields);
		$fields = Set::merge($data, $fields);

		return $this->isUnique($fields, false);
	}
}
