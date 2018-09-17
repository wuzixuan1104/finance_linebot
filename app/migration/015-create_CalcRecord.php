<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `CalcRecord` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `sourceId` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Source ID',
        `currencyId` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Source ID',
        `bankId` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Source ID',
        `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
        PRIMARY KEY (`id`),
        KEY `sourceId_index` (`sourceId`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

  'down' => "DROP TABLE `CalcRecord`;",

  'at' => "2018-09-17 15:17:19"
];
