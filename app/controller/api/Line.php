<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Line extends ApiController {
  public function __construct() {
    parent::__construct();

  }

  public function index() {
    Load::lib ('MyLineBot.php');
    Load::sysFunc('file.php');

    $events = MyLineBot::events();
    foreach( $events as $event ) {
      if( !$source = Source::checkSourceExist($event) )
        continue;
      $speaker = Source::checkSpeakerExist($event);

      if (!$log = MyLineBotLog::init($source, $speaker, $event)->create())
        return false;

      switch( get_class($log) ) {
        case 'Join':
          $this->initIntro($event);
          break;
        case 'Leave':
          break;
        case 'Follow':
          $this->initIntro($event);
          break;
        case 'Unfollow':
          break;
        case 'Text':
          $pattern = 'hello';
          $pattern = !preg_match ('/\(\?P<k>.+\)/', $pattern) ? '/(?P<k>(' . $pattern . '))/i' : ('/(' . $pattern . ')/i');
          preg_match_all ($pattern, $log->text, $result);

          if ($result['k'])
            $this->initIntro($event);

          if( !empty($source->action) ) {
            $action = json_decode($source->action, true);

            if ( strtotime($action['time']) >= strtotime("now - 3 minutes") ) {
              $money = (int)$event->getText();
              if($money == 0) {
                return;
              }
              $msg = '';
              switch($action['data']['type']) {
                case 'calcA': //台幣->xxx
                  $msg .= "台幣兌換". $action['data']['name'] ."\r\n=================\r\n";
                  $msg .= "牌照： " . $money . "元台幣可以換" . round($money / $action['data']['passbook_buy'], 4) . "元" . $action['data']['name'] . "\r\n";
                  $msg .= "現鈔： " . $money . "元台幣可以換" . round($money / $action['data']['cash_buy'], 4) . "元" . $action['data']['name'];
                  break;
                case 'calcB': //xxx->台幣
                  $msg .= $action['data']['name'] . "兌換台幣" ."\r\n=================\r\n";
                  $msg .= "牌照： " . $money . "元" . $action['data']['name'] . "需要花" . $money * $action['data']['passbook_buy'] . "元台幣\r\n";
                  $msg .= "現鈔： " . $money . "元" . $action['data']['name'] . "需要花" . $money * $action['data']['cash_buy'] . "元台幣";
                  break;
              }

              MyLineBotMsg::create ()
                ->multi ([
                  MyLineBotMsg::create ()->text($msg),
                  MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
                    MyLineBotMsg::create()->templateCarousel([
                      MyLineBotMsg::create()->templateCarouselColumn('歡迎使用匯率試算服務！', 'by chestnuter :)', null, [
                        MyLineBotActionMsg::create()->postback( "台幣 -> " . $action['data']['name'], array('lib' => 'ForexProcess', 'method' => 'getCalcType', 'param' => array('type' => 'calcA', 'currency_id' => $action['data']['currency_id'], 'bank_id' => $action['data']['bank_id'], 'passbook_buy' => $action['data']['passbook_buy'], 'cash_buy' => $action['data']['cash_buy'], 'name' => $action['data']['name'] ) ), '台幣 -> ' . $action['data']['name']),
                        MyLineBotActionMsg::create()->postback( $action['data']['name'] . " -> 台幣", array('lib' => 'ForexProcess', 'method' => 'getCalcType', 'param' => array('type' => 'calcB', 'currency_id' => $action['data']['currency_id'], 'bank_id' => $action['data']['bank_id'], 'passbook_buy' => $action['data']['passbook_buy'], 'cash_buy' => $action['data']['cash_buy'], 'name' => $action['data']['name'] ) ), $action['data']['name'] . ' -> 台幣'),
                      ])]
                    )),
              ])->reply ($event->getReplyToken());
            }
            $source->action = null;
            $source->save();
          }

          break;
        case 'Image':
          $url = $log->file->url();
          MyLineBotMsg::create()
            ->image($url, $url)
            ->reply ($event->getReplyToken());
          break;

        case 'Video':
          $url = $log->file->url();
          MyLineBotMsg::create()
            ->video($url, $url)
            ->reply ($event->getReplyToken());
          break;

        case 'Audio':
          $url = $log->file->url();
          MyLineBotMsg::create()
            ->audio($url, 60000)
            ->reply ($event->getReplyToken());
          break;

        case 'Location':
          MyLineBotMsg::create()
            ->location($log->title, $log->address, $log->latitude, $log->longitude)
            ->reply ($event->getReplyToken());
          break;

        case 'Postback':
          $data = json_decode( $log->data, true );
          if ( !(isset( $data['lib'], $data['method'] ) && Load::lib( $data['lib'] . '.php') && method_exists($lib = $data['lib'], $method = $data['method']) && $msg = $lib::$method( $data['param'], $log ) ))
            return false;
          $msg->reply ($event->getReplyToken());
          break;
      }
    }
  }

  public function initIntro($event) {
    if( !$currencies = Currency::find('all', array('where' => array('enable' => Currency::ENABLE_ON ) ) ) )
      return false;

    $columnArr = [];
    $currencies = array_chunk( $currencies, 3 );
    foreach( $currencies as $key => $currency ) {
      // if($key > 9) break;

      $actionArr = [];
      foreach( $currency as $vcurrency )
        $actionArr[] = MyLineBotActionMsg::create()->postback( $vcurrency->name, array('lib' => 'ForexProcess', 'method' => 'getBanks', 'param' => array('currency_id' => $vcurrency->id) ), $vcurrency->name);

      if( ($currencySub = 3 - count($currency)) != 0)
        for($i = 0; $i < $currencySub; $i++ ) {
          $actionArr[] = MyLineBotActionMsg::create()->postback( '', array('lib' => 'ForexProcess', 'method' => 'getBanks', 'param' => array('currency_id' => $vcurrency->id) ), '');
        }

      $columnArr[] = MyLineBotMsg::create()->templateCarouselColumn('請選擇貨幣', '查詢外匯', null, $actionArr);
    }


    // $template = implode(',', array_map( function($columnArr) {
    //   return MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
    //     MyLineBotMsg::create()->templateCarousel( $columnArr )
    //   );
    // }, $columnArrs));

    MyLineBotMsg::create ()
      ->multi ([
        MyLineBotMsg::create ()->text ('歡迎使用理財小精靈: )'),
        MyLineBotMsg::create ()->text ('以下提供查詢各家銀行外匯'),
        MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
          MyLineBotMsg::create()->templateCarousel( $columnArr )
        ),
    ])->reply ($event->getReplyToken());


    die;
  }

}
