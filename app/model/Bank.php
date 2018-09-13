<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class Bank extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];

  const ENABLE_OFF  = 'off';
  const ENABLE_ON = 'on';
  const ENABLE = [
    self::ENABLE_OFF  => '下架', 
    self::ENABLE_ON => '上架',
  ];
}
