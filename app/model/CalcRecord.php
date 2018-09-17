<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class CalcRecord extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  static $belongToOne = [
    'currency' => 'Currency',
    'bank' => 'Bank'
  ];

  // static $belongToMany = [];

  // static $uploaders = [];
}
