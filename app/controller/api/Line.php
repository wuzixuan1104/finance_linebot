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


          // MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
          //     MyLineBotMsg::create()->templateConfirm( '我問你個問題', [
          //       MyLineBotActionMsg::create()->message('好', 'true'),
          //       MyLineBotActionMsg::create()->postback('不', 'bbb=123', 'postback'),
          //     ])
          // )->reply ($event->getReplyToken());
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
          isset( $data['lib'], $data['method'] ) && Load::lib( $data['lib'] . '.php') && method_exists($lib = $data['lib'], $method = $data['method']) && $msg = $lib::$method( $data['param'] );
          Log::info('postback test~~~~~~~~~~~~~');
          $msg->reply ($log);
          Log::info('postback end~~~~~~~~~~~~');
          // MyLineBotMsg::create()
          //   ->text( $this->event->getPostbackParams() )
          //   ->reply($event->getReplyToken());
          //   Log
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
      if($key > 9) break;
      if(count($currency) != 3) break;

      $actionArr = [];
      foreach( $currency as $vcurrency )
        $actionArr[] = MyLineBotActionMsg::create()->postback( $vcurrency->name, array('lib' => 'BankProcess', 'method' => 'searchBank', 'param' => array('currency_id' => $vcurrency->id) ), $vcurrency->name);
      $columnArr[] = MyLineBotMsg::create()->templateCarouselColumn('請選擇貨幣', '查詢外匯', 'https://cdn.adpost.com.tw/adpost/production/uploads/adv_details/pic/00/00/00/00/00/00/06/5e/_29753e27ceb64b0f35b77aca7acf9a3e.jpg', $actionArr);
    }
    MyLineBotMsg::create ()
      ->multi ([
        MyLineBotMsg::create ()->text ('歡迎使用理財小精靈: )'),
        MyLineBotMsg::create ()->text ('以下提供查詢各家銀行外匯'),
        MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
          MyLineBotMsg::create()->templateCarousel( $columnArr )
        )
    ])->reply ($event->getReplyToken());


    die;
  }

}
