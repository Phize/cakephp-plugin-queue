-- phpMyAdmin SQL Dump
-- version 2.11.10
-- http://www.phpmyadmin.net
--
-- ホスト: localhost
-- 生成時間: 2010 年 7 月 27 日 01:06
-- サーバのバージョン: 5.0.91
-- PHP のバージョン: 5.2.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- データベース: `development`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `queue_jobs`
--

CREATE TABLE IF NOT EXISTS `queue_jobs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `queue_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'ジョブ名',
  `type` varchar(255) NOT NULL COMMENT 'ジョブタイプ',
  `priority` tinyint(2) NOT NULL default '50' COMMENT '優先度(0 = 低, 99 = 高)',
  `recursive` tinyint(1) NOT NULL default '0' COMMENT '繰り返し実行',
  `interval` int(10) unsigned NOT NULL default '86400' COMMENT '繰り返し実行時の実行間隔',
  `retry_delay` int(10) unsigned NOT NULL default '60' COMMENT 'リトライの待ち時間',
  `tries` int(10) unsigned NOT NULL default '0' COMMENT '実行回数(リトライ用)',
  `max_tries` int(10) unsigned NOT NULL default '5' COMMENT '最大実行回数(リトライ用)',
  `parameters` text NOT NULL COMMENT 'パラメーター',
  `created` datetime NOT NULL COMMENT '作成日時',
  `scheduled` datetime NOT NULL COMMENT '次回の実行日時',
  `locked` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'ロック日時',
  `tried` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '実行開始日時',
  `completed` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '実行成功日時',
  `polling_delay` int(10) unsigned NOT NULL default '1' COMMENT '次のジョブを実行するまでの待ち時間',
  `status` varchar(255) NOT NULL default 'idle' COMMENT 'ステータス (idle = 実行待ち中, stopped = 停止中, running = 実行中, success = 成功, error = エラー)',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `queue_id_name` (`queue_id`,`name`),
  KEY `queue_id` (`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `queue_logs`
--

CREATE TABLE IF NOT EXISTS `queue_logs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `job_id` int(10) unsigned NOT NULL,
  `status` varchar(255) NOT NULL COMMENT 'ステータス',
  `message` text NOT NULL COMMENT 'メッセージ',
  `created` datetime NOT NULL COMMENT '作成日時',
  PRIMARY KEY  (`id`),
  KEY `job_id` (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- テーブルの構造 `queue_queues`
--

CREATE TABLE IF NOT EXISTS `queue_queues` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT 'キュー名',
  `polling_delay` int(10) unsigned NOT NULL default '1',
  `created` datetime NOT NULL COMMENT '作成日時',
  `status` varchar(255) NOT NULL default 'stopped' COMMENT 'ステータス (stopped = 停止中, running = 稼働中)',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `queue_jobs`
--
ALTER TABLE `queue_jobs`
  ADD CONSTRAINT `queue_id` FOREIGN KEY (`queue_id`) REFERENCES `queue_queues` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
