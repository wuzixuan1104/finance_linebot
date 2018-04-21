<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

return array (
    'up' => "CREATE TABLE `brand_product_details` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `brand_product_id` int(11) unsigned NOT NULL,
        `type` enum('picture', 'youtube', 'video') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'picture' COMMENT '種類',
        `pic` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '圖片',
        `url` text COLLATE utf8mb4_unicode_ci COMMENT '連結',
        `file` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '檔案',
        `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'down' => "DROP TABLE `brand_product_details`;",
    'at' => "2018-04-02 17:14:56",
  );
