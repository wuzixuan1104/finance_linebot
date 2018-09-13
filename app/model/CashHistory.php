<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class CashHistory extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];

  const KIND_MAX  = 'max';
  const KIND_MIN = 'min';
  const KIND = [
    self::KIND_MAX  => '最大值', 
    self::KIND_MIN => '最小值',
  ];
}
