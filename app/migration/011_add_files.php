<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

return array (
    'up' => "CREATE TABLE `files` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `user_id` int(11) unsigned NOT NULL,
      `mid` int(11) unsigned NOT NULL COMMENT '回覆的訊息id',
      `filename` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '檔案名稱',
      `filesize` int(11) unsigned NOT NULL DEFAULT '' COMMENT '檔案大小',
      `file` int(11) unsigned NOT NULL DEFAULT '' COMMENT '檔案',
      `reply_token` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '回覆token',
      `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
      PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'down' => "DROP TABLE `files`;",
    'at' => "2018-04-23 10:27:25",
  );
