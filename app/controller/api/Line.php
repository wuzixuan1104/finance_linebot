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
      Log::info( 'class:'  . get_class($log));
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
          Log::info('postback test');

          $data = json_decode( $log->data, true );
          Log::info('lib:' . $data['lib']);
          Log::info('method:' . $data['method']);
          Log::info('param:' . json_encode( $data['param'] ) );

          Log::info('json_encode=============');
          if( isset( $data['lib'], $data['method'] ) ) {
            Log::info('isset==================');
            if( Load::lib( $data['lib'] . '.php') ) {
              Log::info('load lib==================');
              if( method_exists($data['lib'], $data['method']) ) {
                Log::info('method exist');
                if( BankProcess::setBank($data['param']) ) {
                  Log::info('bank process');
                }
                // if( $data['lib']::$data['method']( $data['param'] ) ) {
                //   Log::info('msg');
                // }
              }
            }
          }
          isset( $data['lib'], $data['method'] ) && Load::lib( $data['lib'] . '.php') && method_exists($data['lib'], $data['method']) && $msg = $data['lib']::$data['method']( $data['param'] );


          Log::info('end');
          // MyLineBotMsg::create()
          //   ->text( $this->event->getPostbackParams() )
          //   ->reply($event->getReplyToken());
          //   Log
          break;
      }
    }
  }

  public function initIntro($event) {
    if( !$banks = Bank::find('all', array('where' => array('enable' => Bank::ENABLE_ON ) ) ) )
      return false;

    $columnArr = [];
    $banks = array_chunk( $banks, 3 );
    foreach( $banks as $key => $bank ) {
      if($key > 9) break;
      $actionArr = [];
      foreach( $bank as $vbank )
        $actionArr[] = MyLineBotActionMsg::create()->postback($vbank->name, array('lib' => 'BankProcess', 'method' => 'setBank', 'param' => array('bank_id' => $vbank->id) ), $vbank->name);
      $columnArr[] = MyLineBotMsg::create()->templateCarouselColumn('查詢銀行外匯', '選擇銀行', 'https://cdn.adpost.com.tw/adpost/production/uploads/adv_details/pic/00/00/00/00/00/00/06/5e/_29753e27ceb64b0f35b77aca7acf9a3e.jpg', $actionArr);
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
