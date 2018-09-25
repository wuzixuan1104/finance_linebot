<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class RemindRange extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  static $belongToOne = [
    'currency' => 'Currency',
    'bank' => 'Bank',
    'source' => 'Source'
  ];

  // static $belongToMany = [];

  // static $uploaders = [];

  const KIND_CASH  = 'cash';
  const KIND_PASSBOOK = 'passbook';
  const KIND = [
    self::KIND_CASH  => '現鈔', 
    self::KIND_PASSBOOK => '牌告',
  ];

  const TYPE_MORE  = 'more';
  const TYPE_LESS = 'less';
  const TYPE = [
    self::TYPE_MORE  => '大於等於', 
    self::TYPE_LESS => '小於等於',
  ];
}
