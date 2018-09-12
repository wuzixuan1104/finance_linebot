<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `CashRecord` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `currency_id` int(11) unsigned NOT NULL,
        `currency_time_id` int(11) unsigned NOT NULL,
        `bank_id` int(11) unsigned NOT NULL,
        `buy` DOUBLE NOT NULL COMMENT '牌告買進',
        `sell` DOUBLE NOT NULL COMMENT '牌告賣出',
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
  'down' => "DROP TABLE `CashRecord`;",

  'at' => "2018-09-12 14:14:28"
];
