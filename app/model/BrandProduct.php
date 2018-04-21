<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class BrandProduct extends Model {
  static $table_name = 'brand_products';

  static $has_one = array (
    array ('d4',  'class_name' => 'BrandProductDetail'),
  );

  static $has_many = array (
    array ('advs',  'class_name' => 'Adv'),
    array ('details',  'class_name' => 'BrandProductDetail'),
  );

  static $belongs_to = array (
    array ('brand',  'class_name' => 'Brand'),
  );

  public function __construct ($attrs = array (), $guardAttrs = true, $instantiatingViafind = false, $newRecord = true) {
    parent::__construct ($attrs, $guardAttrs, $instantiatingViafind, $newRecord);
  }

  public function destroy () {
    if (!isset ($this->id))
      return false;

    return $this->delete ();
  }
}
