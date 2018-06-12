<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

Load::lib ('MyLineBot.php');

class BankProcess {
  public function __construct() {
  }

  public static function searchBank($params) {
    Log::info('==> bank 1');
    if( !isset($params['currency_id']) || empty($params['currency_id']) )
      return false;
    Log::info('==> bank 2');
    Log::info('==> currency_id:' . $params['currency_id']);
    PassbookRecord::first ();

    // if( !$records = PassbookRecord::find('all', array( 'where' => array( "( bank_id, currency_id, created_at ) in ( select `bank_id`, `currency_id`, max(`created_at`) from `passbook_records` where `currency_id` = ? group by `bank_id` ) ", $params['currency_id']) )) )
    //   return false;
    // Log::info( json_encode($records) );
    // Log::info('bank 3');

    $columnArr = [];
    $records = array_chunk( $records, 3 );
    foreach( $records as $key => $record ) {
      if($key > 9) break;
      if(count($record) != 3) break;
      $actionArr = [];
      foreach( $record as $vrecord )
        $actionArr[] = MyLineBotActionMsg::create()->postback( $vrecord->bank->name, array('lib' => 'BankProcess', 'method' => 'searchData', 'param' => array('currency_id' => $params['currency_id'], 'bank_id' => $vrecord->bank->id) ), $vrecord->bank->name);
      $columnArr[] = MyLineBotMsg::create()->templateCarouselColumn('請選擇銀行', '查詢外匯', 'https://cdn.adpost.com.tw/adpost/production/uploads/adv_details/pic/00/00/00/00/00/00/06/5e/_29753e27ceb64b0f35b77aca7acf9a3e.jpg', $actionArr);
    }

    return MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
      MyLineBotMsg::create()->templateCarousel( $columnArr )
    );
  }
}
