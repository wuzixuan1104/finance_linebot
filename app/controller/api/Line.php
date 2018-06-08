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
      $this->initIntro($event);
      // die;
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
          MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
              MyLineBotMsg::create()->templateConfirm( '我問你個問題', [
                MyLineBotActionMsg::create()->message('好', 'true'),
                MyLineBotActionMsg::create()->postback('不', 'bbb=123', 'postback'),
              ])
          )->reply ($event->getReplyToken());
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
          MyLineBotMsg::create()
            ->text('postback message')
            ->reply($event->getReplyToken());
          break;
      }
    }
  }

  public function initIntro($event) {
    // if( !$banks = Bank::find('all', array('where' => array('enable' => Bank::ENABLE_ON ) ) ) )
    //   return false;
    //
    // $actionArr = [];
    // $cnt = 0;
    // foreach( $banks as $bank ) {
    //   $cnt++;
    //   $actionMsg = MyLineBotActionMsg::create()->postback($bank->name, 'bank_id=' . $bank->id, $bank->name);
    //   $actionArr[] = $actionMsg;
    //   break;
    //   if($cnt == 5)
    //     break;
    // }

    $a = MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
        MyLineBotMsg::create()->templateCarousel( [
          MyLineBotMsg::create()->templateCarouselColumn('標題', '哈哈哈哈哈', 'https://cdn.adpost.com.tw/adpost/production/uploads/adv_details/pic/00/00/00/00/00/00/06/5e/_29753e27ceb64b0f35b77aca7acf9a3e.jpg', [
            MyLineBotActionMsg::create()->postback('label', 'postback', 'postback'),
            MyLineBotActionMsg::create()->postback('label', 'postback', 'postback'),
            MyLineBotActionMsg::create()->postback('label', 'postback', 'postback'),
            MyLineBotActionMsg::create()->postback('label', 'postback', 'postback'),
            // MyLineBotActionMsg::create()->postback('label', 'postback', 'postback'),
          ]),
          MyLineBotMsg::create()->templateCarouselColumn('標題', '哈哈哈哈哈', 'https://cdn.adpost.com.tw/adpost/production/uploads/adv_details/pic/00/00/00/00/00/00/06/5e/_29753e27ceb64b0f35b77aca7acf9a3e.jpg', [
            MyLineBotActionMsg::create()->postback('label', 'postback', 'postback'),
            MyLineBotActionMsg::create()->postback('label', 'postback', 'postback'),
            MyLineBotActionMsg::create()->postback('label', 'postback', 'postback'),
            MyLineBotActionMsg::create()->postback('label', 'postback', 'postback'),
            // MyLineBotActionMsg::create()->postback('label', 'postback', 'postback'),
          ]),
        ])
    )->reply ($event->getReplyToken());
    print_R($a);
    // ->reply ($event->getReplyToken());
    die;
    MyLineBotMsg::create ()
      ->multi ([
       MyLineBotMsg::create ()->text ('歡迎使用理財小精靈: )'),
       MyLineBotMsg::create ()->text ('以下提供查詢各家銀行外匯'),
       MyLineBotMsg::create()->template('銀行',
         MyLineBotMsg::create()->templateButton('請選擇銀行', '查詢外匯', 'https://example.com/bot/images/image.jpg', $actionArr)
       )
     ])->reply ($event->getReplyToken());


     print_r($a);
     die;
    Log::info('success');
  }

}
