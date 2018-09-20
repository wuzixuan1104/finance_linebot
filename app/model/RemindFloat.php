<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class RemindFloat extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  static $belongToOne = [
    'currency' => 'Currency',
    'bank' => 'Bank',
  ];

  // static $belongToMany = [];

  // static $uploaders = [];

  const KIND_CASH  = 'cash';
  const KIND_PASSBOOK = 'passbook';
  const KIND = [
    self::KIND_CASH  => '現鈔', 
    self::KIND_PASSBOOK => '牌告',
  ];
}
