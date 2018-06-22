<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class HistoryRecord extends Model {
  static $table_name = 'history_records';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  const TYPE_PASSBOOK = 'passbook';
  const TYPE_CASH = 'cash';

  const KIND_MAX = 'max';
  const KIND_MIN = 'min';

  static $typeTexts = array(
    self::TYPE_PASSBOOK => '牌告',
    self::TYPE_CASH => '現鈔',
  );

  static $kindTexts = array(
    self::KIND_MAX => '最大',
    self::KIND_MIN => '最小',
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
