<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Adv extends Model {
  static $table_name = 'advs';

  static $has_one = array (
    array ('d4',  'class_name' => 'AdvDetail'),
  );

  static $has_many = array (
    array ('details', 'class_name' => 'AdvDetail'),
    array ('likes',  'class_name' => 'AdvLike'),
    array ('views',  'class_name' => 'AdvView'),
    array ('messages',  'class_name' => 'AdvMessage'),
  );

  static $belongs_to = array (
    array ('user',  'class_name' => 'User'),
    array ('brand',  'class_name' => 'Brand'),
    array ('product',  'class_name' => 'BrandProduct'),
  );

  const TYPE_PICTURE = 'picture';
  const TYPE_YOUTUBE = 'youtube';
  const TYPE_VIDEO = 'video';

  const REVIEW_PASS = 'pass';
  const REVIEW_FAIL = 'fail';

  static $typeTexts = array (
    self::TYPE_PICTURE  => '圖片',
    self::TYPE_YOUTUBE  => 'Youtube',
    self::TYPE_VIDEO  => '影片',
  );

  static $reviewTexts = array (
    self::REVIEW_PASS  => '已審核',
    self::REVIEW_FAIL  => '未審核',
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
