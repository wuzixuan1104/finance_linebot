<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `Join` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `sourceId` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Source ID',
        `replyToken` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '回覆 Token',
        `timestamp` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '時間',
        `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

  'down' => "DROP TABLE `Join`;",

  'at' => "2018-09-12 14:12:27"
];
