<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class PassbookRecord extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  static $belongToOne = [
    'bank' => 'Bank',
    'currency' => 'Currency',
  ];

  // static $belongToMany = [];

  // static $uploaders = [];
}
