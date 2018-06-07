<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

return array (
    'up' => "CREATE TABLE `currency_times` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '外幣更新時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    'down' => "DROP TABLE `currency_times`;",
    'at' => "2018-06-07 15:57:51",
  );
