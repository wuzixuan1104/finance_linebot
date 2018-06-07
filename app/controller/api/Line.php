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

      Log::info( get_class($log) );
      Log::info('success');
      switch( get_class($log) ) {
        case 'Join':
          $this->initIntro();
          break;
        case 'Leave':
          break;
        case 'Follow':
          $this->initIntro();
          break;
        case 'Unfollow':
          break;
        case 'Text':
          Log::info('text');
          MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
              MyLineBotMsg::create()->templateConfirm( '我問你個問題', [
                MyLineBotActionMsg::create()->message('好', 'true'),
                MyLineBotActionMsg::create()->postback('不', 'bbb=123'),
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

  public function initIntro() {
    MyLineBotMsg::create ()
      ->multi ([
       MyLineBotMsg::create ()->text ('歡迎使用理財小精靈: )'),
       MyLineBotMsg::create ()->text ('以下是我們功能！'),
       MyLineBotMsg::create()->template('理財小精靈',
         MyLineBotMsg::create()->templateButton('外匯查詢', '請選擇種類', 'https://example.com/bot/images/image.jpg', [
           MyLineBotActionMsg::create()->postback('美金', 'cash=ua'),
           MyLineBotActionMsg::create()->postback('日幣', 'cash=japan'),
           MyLineBotActionMsg::create()->postback('澳幣', 'cash=aus'),
           MyLineBotActionMsg::create()->postback('人民幣', 'cash=china'),
         ])
       )
      ])
      ->reply ($event->getReplyToken());
  }

}
