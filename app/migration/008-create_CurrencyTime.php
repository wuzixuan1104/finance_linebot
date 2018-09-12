<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `CurrencyTime` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '外幣更新時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
  'down' => "DROP TABLE `CurrencyTime`;",

  'at' => "2018-09-12 14:13:58"
];
