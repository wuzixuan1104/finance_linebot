<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `Source` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `sid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '來源 ID',
        `title` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
        `action` TEXT COLLATE utf8mb4_unicode_ci COMMENT '動作',
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
        PRIMARY KEY (`id`),
        KEY `sid_index` (`sid`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
  'down' => "DROP TABLE `Source`;",

  'at' => "2018-09-12 14:13:20"
];
