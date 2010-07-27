<?php
/**
 * 共通コントローラークラス
 *
 * Queueプラグインの共通コントローラー
 * Queueプラグインの全てのコントローラーはこのクラスを継承する
 */
class QueueAppController extends AppController {
	/**
	 * プラグインの設定
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * コンストラクター
	 */
	function __construct() {
		parent::__construct();

		$this->config = Configure::read('Queue');
	}
}
