<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class User extends Model {
  static $table_name = 'users';

  static $has_one = array (
    array ('account',  'class_name' => 'Account'),
  );

  static $has_many = array (
    array ('bonuses',  'class_name' => 'Bonus'),
    array ('adv_like',  'class_name' => 'AdvLike'),
  );

  static $belongs_to = array (
  );

  const ACTIVE_ON = 'on';
  const ACTIVE_OFF = 'off';

  static $activeTexts = array (
    self::ACTIVE_ON  => '已驗證',
    self::ACTIVE_OFF => '未驗證',
  );

  public function __construct ($attrs = array (), $guardAttrs = true, $instantiatingViafind = false, $newRecord = true) {
    parent::__construct ($attrs, $guardAttrs, $instantiatingViafind, $newRecord);

    // 設定圖片上傳器
    Uploader::bind ('avatar', 'UserAvatarImageUploader');
  }

  public function hasLike($adv) {
    if( !$adv_ids = array_orm_column($this->adv_like, 'adv_id') )
      return false;
    return in_array($adv->id, $adv_ids);
  }

  public function receive ($account, $price, $type) {
    if ($price <= 0) {
      Log::error ('價格不足，$price=' . $price . '，$type=' . $type . '，$account->id=' . $account->id . '，user_id=' . $this->id);
      return false;
    }

    if ($account->user_id != $this->id) {
      Log::error ('帳號錯誤，$price=' . $price . '，$type=' . $type . '，$account->id=' . $account->id . '，user_id=' . $this->id);
      return false;
    }

    if (!$this->checkReceive ($price)) {
      Log::error ('金額不足，$price=' . $price . '，$type=' . $type . '，$account->id=' . $account->id . '，user_id=' . $this->id);
      return false;
    }

    if (!isset (BonusReceive::$typeTexts[$type])) {
      Log::error ('領錢方式錯誤，$price=' . $price . '，$type=' . $type . '，$account->id=' . $account->id . '，user_id=' . $this->id);
      return false;
    }

    if (!$bonuses = Bonus::find ('all', array ('order' => 'id ASC', 'where' => array ('user_id = ? AND remain_price > ?', $this->id, 0)))) {
      Log::error ('沒有 Bonus，$price=' . $price . '，$type=' . $type . '，$account->id=' . $account->id . '，user_id=' . $this->id);
      return false;
    }

    $data = array ('user_id' => $this->id, 'account_id' => $account->id, 'type' => $type, 'price' => $price, 'is_receive' => BonusReceive::RECEIVE_NO);

    $transaction = function (&$receive, &$bonuses, $data) {
      if (!$receive = BonusReceive::create ($data))
        return false;

      $price = $receive->price;

      $details = array ();
      foreach ($bonuses as $bonus) {

        if ($bonus->remain_price > $price) {
          array_push ($details, array ('bonus_receive_id' => $receive->id, 'bonus_id' => $bonus->id, 'price' => $price));
          $bonus->remain_price = $bonus->remain_price - $price;
          $price = 0;
          $bonus->save ();
          break;

        } else if ($bonus->remain_price == $price) {
          array_push ($details, array ('bonus_receive_id' => $receive->id, 'bonus_id' => $bonus->id, 'price' => $price));
          $price = 0;
          $bonus->remain_price = 0;
          $bonus->save ();
          break;

        } else {
          array_push ($details, array ('bonus_receive_id' => $receive->id, 'bonus_id' => $bonus->id, 'price' => $bonus->remain_price));
          $price = $price - $bonus->remain_price;
          $bonus->remain_price = 0;
          $bonus->save ();
        }
      }

      if (!$details) {
        Log::error ('沒有 Details，$price=' . $price . '，$type=' . $type . '，$account->id=' . $account->id . '，user_id=' . $this->id);
        return false;
      }

      foreach ($details as $detail)
        if (!$receiveDetail = BonusReceiveDetail::create ($detail)) {
          Log::error ('Create BonusReceiveDetail 失敗，$price=' . $price . '，$type=' . $type . '，$account->id=' . $account->id . '，user_id=' . $this->id);
          return false;
        }

      return true;
    };

    if ($error = Bonus::getTransactionError ($transaction, $receive, $bonuses, $data)) {
      Log::error ('資料庫處理錯誤，$price=' . $price . '，$type=' . $type . '，$account->id=' . $account->id . '，user_id=' . $this->id);
      return false;
    }

    return $receive;
  }
  public function checkReceive ($price) {
    $quota = array_sum (array_orm_column ($this->bonuses, 'remain_price'));
    return $quota > $price;
  }
  public function destroy () {
    if (!isset ($this->id))
      return false;

    return $this->delete ();
  }

  public function putFiles ($files) {
    foreach ($files as $key => $file)
      if (isset ($files[$key]) && $files[$key] && isset ($this->$key) && $this->$key instanceof Uploader && !$this->$key->put ($files[$key]))
        return false;
    return true;
  }
}

/* -- 圖片上傳器物件 ------------------------------------------------------------------ */
class UserAvatarImageUploader extends ImageUploader {
  public function getVersions () {
    return array (
        '' => array (),
        'w100' => array ('resize', 100, 100, 'width'),
        'c1200x630' => array ('adaptiveResizeQuadrant', 1200, 630, 't'),
      );
  }
}
