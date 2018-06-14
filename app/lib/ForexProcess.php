<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

Load::lib ('MyLineBot.php');

class ForexProcess {
  public function __construct() {
  }

  public static function getBanks($params) {
    if( !isset($params['currency_id']) || empty($params['currency_id']) )
      return false;

    if( !$records = PassbookRecord::find('all', array( 'where' => array( "( bank_id, currency_id, created_at ) in ( select `bank_id`, `currency_id`, max(`created_at`) from `passbook_records` where `currency_id` = ? group by `bank_id` ) ", $params['currency_id']) )) )
      return false;

    $columnArr = [];
    $records = array_chunk( $records, 3 );
    foreach( $records as $key => $record ) {
      if($key > 9) break;
      if(count($record) != 3) break;
      $actionArr = [];
      foreach( $record as $vrecord )
        $actionArr[] = MyLineBotActionMsg::create()->postback( $vrecord->bank->name, array('lib' => 'ForexProcess', 'method' => 'getRecords', 'param' => array('currency_id' => $params['currency_id'], 'bank_id' => $vrecord->bank->id) ), $vrecord->bank->name);
      $columnArr[] = MyLineBotMsg::create()->templateCarouselColumn('請選擇銀行', '查詢外匯', null, $actionArr);
    }

    return MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
      MyLineBotMsg::create()->templateCarousel( $columnArr )
    );
  }

  public static function getRecords($params) {
    if( !isset($params['currency_id']) || empty($params['currency_id']) || !isset($params['bank_id']) || empty($params['bank_id']) )
      return false;

    if( !$currency = Currency::find_by_id($params['currency_id']) )
      return false;
    if( !$bank = Bank::find_by_id($params['bank_id']) )
      return false;

    $msg = "[ " . $currency->name . " / " . $bank->name . " ]\r\n\r\n";
    $conditions = array('where' => array('bank_id = ? and currency_id = ?', $params['bank_id'], $params['currency_id']), 'order' => 'created_at desc', 'limit' => 1 );
    if( $passbooks = PassbookRecord::find('one', $conditions) ) {
      $msg .= "牌告匯率：\r\n => 賣出：" . $passbooks->sell . "\r\n => 買入：" . $passbooks->buy . "\r\n================\r\n";
    }
    if( $cashes = CashRecord::find('one', $conditions) )
      $msg .= "現鈔匯率：\r\n => 賣出：" . $cashes->sell . "\r\n => 買入：" . $cashes->buy;

    $msg .= "\r\n\r\n\r\n回選單首頁請輸入\"hello\"";

    return MyLineBotMsg::create()
              ->text($msg);
  }
}
