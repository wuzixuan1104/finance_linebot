<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `CashHistory` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `currencyId` int(11) unsigned NOT NULL,
        `currencyTimeId` int(11) unsigned NOT NULL,
        `bankId` int(11) unsigned NOT NULL,
        `buy` DOUBLE NOT NULL COMMENT '牌告買進',
        `sell` DOUBLE NOT NULL COMMENT '牌告賣出',
        `kind` enum('max', 'min') NOT NULL DEFAULT 'max' COMMENT '最大最小值',
        `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
  'down' => "DROP TABLE `CashHistory`;",

  'at' => "2018-09-12 14:35:40"
];
