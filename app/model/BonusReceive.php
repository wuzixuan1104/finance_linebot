<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class BonusReceive extends Model {
  static $table_name = 'bonus_receives';

  static $has_one = array (
  );

  static $has_many = array (
    array('details', 'class_name' => 'BonusReceiveDetail'),
  );

  static $belongs_to = array (
    array('user', 'class_name' => 'User'),
    array('account', 'class_name' => 'Account'),
  );

  const TYPE_ATM = 'atm';
  const TYPE_CASH = 'cash';

  static $typeTexts = array (
    self::TYPE_ATM  => '匯款',
    self::TYPE_CASH  => '付現',
  );

  const RECEIVE_YES = 'yes';
  const RECEIVE_NO  = 'no';

  static $receiveTexts = array (
    self::RECEIVE_YES  => '已匯款',
    self::RECEIVE_NO   => '未匯款',
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
