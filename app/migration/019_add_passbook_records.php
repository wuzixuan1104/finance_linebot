<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

return array (
    'up' => "CREATE TABLE `passbook_records` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `currency_id` int(11) unsigned NOT NULL,
        `currency_time_id` int(11) unsigned NOT NULL,
        `bank_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '銀行名稱',
        `buy` DOUBLE NOT NULL COMMENT '牌告買進',
        `sell` DOUBLE NOT NULL COMMENT '牌告賣出',
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    'down' => "DROP TABLE `passbook_records`;",
    'at' => "2018-06-07 10:50:03",
  );
