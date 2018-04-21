<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

return array (
    'up' => "CREATE TABLE `advs` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL,
        `brand_id` int(11) unsigned NOT NULL,
        `brand_product_id` int(11) unsigned NOT NULL DEFAULT 0,
        `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '廣告標題',
        `type` enum('picture', 'youtube', 'video') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'picture' COMMENT '種類',
        `review` enum('pass', 'fail') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fail' COMMENT '審核',
        `description` text COLLATE utf8mb4_unicode_ci COMMENT '簡述',
        `content` text COLLATE utf8mb4_unicode_ci COMMENT '內容',
        `cnt_like` int(11) unsigned NOT NULL,
        `cnt_message` int(11) unsigned NOT NULL,
        `cnt_view` int(11) unsigned NOT NULL,
        `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'down' => "DROP TABLE `advs`;",
    'at' => "2018-04-02 17:15:15",
  );
