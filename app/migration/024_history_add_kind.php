<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

return array (
    'up' => "ALTER TABLE `history_records` ADD `kind` enum('max', 'min') NOT NULL DEFAULT 'max' COMMENT '最大最小值' AFTER `type`;",
    'down' => "ALTER TABLE `history_records` DROP COLUMN `kind`;",
    'at' => "2018-06-22 11:26:20",
  );
