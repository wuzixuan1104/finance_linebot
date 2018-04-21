<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

return array (
    'up' => "CREATE TABLE `brand_products` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `brand_id` int(11) unsigned NOT NULL,
        `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名稱',
        `rule` text COLLATE utf8mb4_unicode_ci COMMENT '規則',
        `description` text COLLATE utf8mb4_unicode_ci COMMENT '敘述',
        `cnt_shoot` int(11) unsigned NOT NULL COMMENT '拍攝人數',
        `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'down' => "DROP TABLE `brand_products`;",
    'at' => "2018-04-02 17:14:31",
  );
