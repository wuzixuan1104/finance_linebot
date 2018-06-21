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

  /* 目的：開始功能選單
   * 呼叫此function 的模式 Follow, join, 傳入字串'hello'
   */
  public static function begin() {
    if( !$currencies = Currency::find('all', array('where' => array('enable' => Currency::ENABLE_ON ) ) ) )
      return false;

    foreach( array_chunk( $currencies, 3 ) as $key => $currency ) {
      $actionArr = [];
      foreach( $currency as $vcurrency )
        $actionArr[] = MyLineBotActionMsg::create()->postback( $vcurrency->name, array('lib' => 'ForexProcess', 'method' => 'getBanks', 'param' => array('currency_id' => $vcurrency->id) ), $vcurrency->name);

      //檢查是否每項為3個
      if( ($currencySub = 3 - count($currency)) != 0 )
        for( $i = 0; $i < $currencySub; $i++ )
          $actionArr[] = MyLineBotActionMsg::create()->postback( '-', array(), '-');

      $columnArr[] = MyLineBotMsg::create()->templateCarouselColumn('請選擇貨幣', '查詢外匯', null, $actionArr);
    }

    $multiArr = [ MyLineBotMsg::create ()->text ('歡迎使用理財小精靈: )'), MyLineBotMsg::create ()->text ('以下提供查詢各家銀行外匯')];
    $multiArr = array_merge( $multiArr, array_map( function($column) {
      return  MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
        MyLineBotMsg::create()->templateCarousel( $column )
      );
    }, array_chunk($columnArr, 10) ));

    return MyLineBotMsg::create()->multi ($multiArr);
  }

  /* 目的：取得貨幣對應的銀行
   * 方法：postback回傳呼叫
   */
  public static function getBanks($params, $log = '') {
    if( !isset($params['currency_id']) || empty($params['currency_id']) )
      return false;

    $records = [];
    if( $passRecords = PassbookRecord::find('all', array( 'where' => array( "( bank_id, currency_id, created_at ) in ( select `bank_id`, `currency_id`, max(`created_at`) from `passbook_records` where `currency_id` = ? group by `bank_id` ) ", $params['currency_id']) )) )
      array_map( function($passRecord) use (&$records){
        return $records[$passRecord->bank_id] = $passRecord;
      }, $passRecords);

    if( $cashRecords = CashRecord::find('all', array( 'where' => array( "( bank_id, currency_id, created_at ) in ( select `bank_id`, `currency_id`, max(`created_at`) from `cash_records` where `currency_id` = ? group by `bank_id` ) ", $params['currency_id']) )) )
      array_map( function($cashRecord) use (&$records) {
        if( !isset($records[$cashRecord->bank_id]) )
          return $records[$cashRecord->bank_id] = $cashRecord;
      }, $cashRecords);

    if( empty($records) )
      return false;

    foreach( array_chunk( $records, 3 ) as $record ) {
      $actionArr = [];
      foreach( $record as $vrecord )
        $actionArr[] = MyLineBotActionMsg::create()->postback( $vrecord->bank->name, array('lib' => 'ForexProcess', 'method' => 'getRecords', 'param' => array('currency_id' => $params['currency_id'], 'bank_id' => $vrecord->bank->id) ), $vrecord->bank->name);

      //檢查是否每項為3個
      if( ($recordSub = 3 - count($record)) != 0 )
        for( $i = 0; $i < $recordSub; $i++ )
          $actionArr[] = MyLineBotActionMsg::create()->postback( '-', array(), '-');
      $columnArr[] = MyLineBotMsg::create()->templateCarouselColumn('請選擇銀行', '查詢外匯', null, $actionArr);
    }

    $multiArr = array_map( function($column) {
      return  MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
        MyLineBotMsg::create()->templateCarousel( $column )
      );
    }, array_chunk($columnArr, 10) );

    return MyLineBotMsg::create()->multi ($multiArr);
  }

  /* 目的：顯示選擇的貨幣銀行紀錄 及 顯示換算的貨幣方式對話匡
   * 方法：postback回傳呼叫
   */
  public static function getRecords($params, $log = '') {
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
      if( $time = CurrencyTime::find_by_id($cashes->currency_time_id))
        $msg .= "\r\n\r\n(" . $time->datetime . ")" . "\r\n";
    }

    $msg .= "\r\n\r\n回選單首頁請輸入\"hello\"";

    return  MyLineBotMsg::create ()->multi ([
              MyLineBotMsg::create()->text($msg),
              MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
                MyLineBotMsg::create()->templateCarousel([
                  MyLineBotMsg::create()->templateCarouselColumn('歡迎使用匯率試算服務！', 'by chestnuter :)', null, [
                    MyLineBotActionMsg::create()->postback( "台幣 -> " . $currency->name, array('lib' => 'ForexProcess', 'method' => 'getCalcType', 'param' => array('type' => 'calcA', 'currency_id' => $params['currency_id'], 'bank_id' => $params['bank_id'], 'passbook_sell' => ($passbooks) ? $passbooks->sell : null, 'cash_sell' => ($cashes) ? $cashes->sell : null , 'name' => $currency->name ) ), '台幣 -> ' . $currency->name),
                    MyLineBotActionMsg::create()->postback( $currency->name . " -> 台幣", array('lib' => 'ForexProcess', 'method' => 'getCalcType', 'param' => array('type' => 'calcB', 'currency_id' => $params['currency_id'], 'bank_id' => $params['bank_id'], 'passbook_sell' => ($passbooks) ? $passbooks->sell : null, 'cash_sell' => ($cashes) ? $cashes->sell : null, 'name' => $currency->name ) ), $currency->name . ' -> 台幣'),
                  ])]
            ))]);
  }

  /* 目的：匯率換算
   * 方法：postback回傳呼叫
   */
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
    if( !$source->save() )
      return false;
    return  MyLineBotMsg::create ()->text('請輸入金額(元)');
  }

  /* 目的：顯示換算結果
   * 方法：使用者傳入text時偵測是否Source->action有東西且在時效內3min
   */
  public static function getCalcResult($source, $money) {
    if( !isset($source->action) || empty($source->action) )
      return false;

    $result = false;
    $action = json_decode($source->action, true);
    if ( strtotime($action['time']) >= strtotime("now - 3 minutes") ) {
      if( ($money = (int)$money ) == 0)
        return false;

      $msg = '';
      switch($action['data']['type']) {
        case 'calcA': //台幣->xxx
          $msg .= "台幣兌換". $action['data']['name'] ."\r\n=================\r\n";
          if( $action['data']['passbook_sell'] != null )
            $msg .= "牌吿： " . $money . "元台幣可以換" . round($money / $action['data']['passbook_sell'], 4) . "元" . $action['data']['name'] . "\r\n";
          if( $action['data']['cash_sell'] != null )
            $msg .= "現鈔： " . $money . "元台幣可以換" . round($money / $action['data']['cash_sell'], 4) . "元" . $action['data']['name'];
          break;
        case 'calcB': //xxx->台幣
          $msg .= $action['data']['name'] . "兌換台幣" ."\r\n=================\r\n";
          if( $action['data']['passbook_sell'] != null )
            $msg .= "牌吿： " . $money . "元" . $action['data']['name'] . "需要花" . $money * $action['data']['passbook_sell'] . "元台幣\r\n";
          if( $action['data']['cash_sell'] != null )
            $msg .= "現鈔： " . $money . "元" . $action['data']['name'] . "需要花" . $money * $action['data']['cash_sell'] . "元台幣";
          break;
      }

      $result = MyLineBotMsg::create ()
        ->multi ([
          MyLineBotMsg::create ()->text($msg),
          MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
            MyLineBotMsg::create()->templateCarousel([
              MyLineBotMsg::create()->templateCarouselColumn('歡迎使用匯率試算服務！', 'by chestnuter :)', null, [
                MyLineBotActionMsg::create()->postback( "台幣 -> " . $action['data']['name'], array('lib' => 'ForexProcess', 'method' => 'getCalcType', 'param' => array('type' => 'calcA', 'currency_id' => $action['data']['currency_id'], 'bank_id' => $action['data']['bank_id'], 'passbook_sell' => $action['data']['passbook_sell'], 'cash_sell' => $action['data']['cash_sell'], 'name' => $action['data']['name'] ) ), '台幣 -> ' . $action['data']['name']),
                MyLineBotActionMsg::create()->postback( $action['data']['name'] . " -> 台幣", array('lib' => 'ForexProcess', 'method' => 'getCalcType', 'param' => array('type' => 'calcB', 'currency_id' => $action['data']['currency_id'], 'bank_id' => $action['data']['bank_id'], 'passbook_sell' => $action['data']['passbook_sell'], 'cash_sell' => $action['data']['cash_sell'], 'name' => $action['data']['name'] ) ), $action['data']['name'] . ' -> 台幣'),
              ])]
            )),
      ]);
    }
    $source->action = '';
    $source->save();

    return $result;
  }
}
