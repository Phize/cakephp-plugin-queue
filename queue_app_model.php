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
	public $actsAs = array('containable', 'validation', 'transaction');

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
	protected $queueConfig = array();



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

		parent::__construct($id, $table, $this->useDbConfig);

		$this->queueConfig = Configure::read('Queue');
	}
}
