<?php
// 全体の設定 ****************************************
// Queueプラグインが使用するデータベース接続 (database.phpの接続名)
Configure::write('Queue.database', null);

// キューのデフォルト設定
// 次のジョブを実行するまでの待ち時間
Configure::write('Queue.queue.polling_delay', 1);
// キューのステータス
Configure::write('Queue.queue.status', 'stopped');

// ジョブの設定
// ジョブの最大ロック時間(秒)
Configure::write('Queue.job.lock_time_limit', 60);
// ジョブの最大実行時間(秒)
Configure::write('Queue.job.running_time_limit', 3600);

// ジョブのデフォルト設定
// ジョブの優先度(99が最優先)
Configure::write('Queue.job.priority', 50);
// ジョブを定期的に実行
Configure::write('Queue.job.is_recursive', 0);
// ジョブの定期実行時にジョブを再実行する間隔(秒)
Configure::write('Queue.job.interval', 86400);
// ジョブのエラー時にジョブを再実行するまでの待ち時間(秒)
Configure::write('Queue.job.retry_delay', 60);
// ジョブの最大実行回数(エラー時)
Configure::write('Queue.job.max_tries', 5);
// 次のジョブを実行するまでの待ち時間
Configure::write('Queue.job.polling_delay', 1);
// ジョブのステータス
Configure::write('Queue.job.status', 'idle');
