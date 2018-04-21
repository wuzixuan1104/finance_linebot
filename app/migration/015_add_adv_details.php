<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

return array (
    'up' => "CREATE TABLE `adv_details` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `adv_id` int(11) unsigned NOT NULL COMMENT '廣告ID',
      `type` enum('picture', 'youtube', 'video') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'youtube' COMMENT '廣告類型(picture/youtube)',
      `link` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '連結',
      `pic` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '圖片',
      `file` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '檔案',
      `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'down' => "DROP TABLE `adv_details`;",
    'at' => "2018-04-02 17:15:28",
  );
