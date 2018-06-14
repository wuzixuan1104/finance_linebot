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

  public static function getBanks($params, $log) {
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

  public static function getRecords($params, $log) {
    if( !isset($params['currency_id']) || empty($params['currency_id']) || !isset($params['bank_id']) || empty($params['bank_id']) )
      return false;

    if( !$currency = Currency::find_by_id($params['currency_id']) )
      return false;
    if( !$bank = Bank::find_by_id($params['bank_id']) )
      return false;

    $msg = "[ " . $currency->name . " / " . $bank->name . " ]\r\n\r\n";
    $conditions = array('where' => array('bank_id = ? and currency_id = ?', $params['bank_id'], $params['currency_id']), 'order' => 'created_at desc', 'limit' => 1 );
    if( $passbooks = PassbookRecord::find('one', $conditions) ) {
      $msg .= "牌告匯率：\r\n => 賣出：" . $passbooks->sell . "\r\n => 買入：" . $passbooks->buy;
      if( $time = CurrencyTime::find_by_id($passbooks->currency_time_id))
        $msg .= "\r\n\r\n(" . $time->datetime . ")" . "\r\n================\r\n";
    }
    if( $cashes = CashRecord::find('one', $conditions) ) {
      $msg .= "現鈔匯率：\r\n => 賣出：" . $cashes->sell . "\r\n => 買入：" . $cashes->buy;
      if( $time = CurrencyTime::find_by_id($passbooks->currency_time_id))
        $msg .= "\r\n\r\n(" . $time->datetime . ")" . "\r\n";
    }

    $msg .= "\r\n\r\n回選單首頁請輸入\"hello\"";

    return  MyLineBotMsg::create ()->multi ([
              MyLineBotMsg::create()->text($msg),
              MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
                MyLineBotMsg::create()->templateCarousel([
                  MyLineBotMsg::create()->templateCarouselColumn('歡迎使用匯率試算服務！', 'by chestnuter :)', null, [
                    MyLineBotActionMsg::create()->postback( "台幣 -> " . $currency->name, array('lib' => 'ForexProcess', 'method' => 'getCalcType', 'param' => array('type' => 'calcA', 'currency_id' => $params['currency_id'], 'bank_id' => $params['bank_id'], 'passbook_buy' => $passbooks->buy, 'cash_buy' => $cashes->buy, 'name' => $currency->name ) ), '台幣 -> ' . $currency->name),
                    MyLineBotActionMsg::create()->postback( $currency->name . " -> 台幣", array('lib' => 'ForexProcess', 'method' => 'getCalcType', 'param' => array('type' => 'calcB', 'currency_id' => $params['currency_id'], 'bank_id' => $params['bank_id'], 'passbook_buy' => $passbooks->buy, 'cash_buy' => $cashes->buy, 'name' => $currency->name ) ), $currency->name . ' -> 台幣'),
                  ])]
                ))]);
  }

  public static function getCalcType($params, $log) {
    if( !isset($params['type']) || empty($params['type']) || !isset($params['currency_id']) || empty($params['currency_id']) || !isset($params['bank_id']) || empty($params['bank_id']) )
      return false;
    if( !$source = Source::find_by_id($log->speaker_id) )
      return false;

    $source->action = json_encode(array(
      'time' => date('Y-m-d H:i:s'),
      'func' => __FUNCTION__,
      'data' => $params,
    ));
    Log::info('hehehe: '.$source->action);
    $source->save();
    return  MyLineBotMsg::create ()->text('請輸入金額(元)');
  }
}
