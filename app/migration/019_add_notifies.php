<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

return array (
    'up' => "CREATE TABLE `notifies` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `brand_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '品牌ID',
        `user_id` int(11) unsigned NOT NULL COMMENT '會員ID',
        `send_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '發送者ID',
        `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '內容',
        `read` enum('yes', 'no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no' COMMENT '已讀',
        `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'down' => "DROP TABLE `notifies`;",
    'at' => "2018-04-02 17:16:19",
  );
