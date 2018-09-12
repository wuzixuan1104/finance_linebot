<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `Currency` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名稱',
        `iso` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '代號',
        `enable` enum('on', 'off') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off' COMMENT '狀態，on: 啟動 off: 關閉',
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
  'down' => "DROP TABLE `Currency`;",

  'at' => "2018-09-12 14:13:37"
];
