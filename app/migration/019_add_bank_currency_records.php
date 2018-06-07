<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

return array (
    'up' => "CREATE TABLE `bank_currency_records` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `bank_id` int(11) unsigned NOT NULL,
        `currency_id` int(11) unsigned NOT NULL,
        `bank_buy` DOUBLE NOT NULL COMMENT '銀行買進',
        `bank_sell` DOUBLE NOT NULL COMMENT '銀行賣出',
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    'down' => "DROP TABLE `bank_currency_records`;",
    'at' => "2018-06-07 10:50:03",
  );