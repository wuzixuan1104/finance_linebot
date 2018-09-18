<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
   'up' => "CREATE TABLE `RemindRange` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `sourceId` int(11) unsigned NOT NULL,
        `currencyId` int(11) unsigned NOT NULL,
        `bankId` int(11) unsigned NOT NULL,
        `value` DOUBLE NOT NULL COMMENT '設定的標竿',
        `type` enum('more', 'less') NOT NULL DEFAULT 'less' COMMENT '比較值',
        `dailyAt` datetime DEFAULT null COMMENT '今天是否已提醒',
        `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
  'down' => "DROP TABLE `RemindRange`;",

  'at' => "2018-09-18 11:32:06"
];
