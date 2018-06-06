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
      Log::info(get_class($log));
      switch( get_class($log) ) {
        case 'Join':
          break;
        case 'Leave':
          break;
        case 'Follow':
          MyLineBotMsg::create ()
            ->multi ([
             MyLineBotMsg::create ()->text ('歡迎使用理財小精靈:)'),
             MyLineBotMsg::create ()->text ('hello')
            ])
            ->reply ($event->getReplyToken());
          break;
        case 'Unfollow':
          break;
        case 'Text':
          break;
        case 'Image':
          break;
        case 'Postback':
          break;
      }
      die;
      switch( $event->getType() ) {
        case 'postback':
          MyLineBotMsg::create()->template('抬頭',
            MyLineBotMsg::create()->templateButton('外匯查詢', '請選擇種類', 'https://example.com/bot/images/image.jpg', [
              MyLineBotActionMsg::create()->postback('美金', 'cash=ua'),
              MyLineBotActionMsg::create()->postback('日幣', 'cash=japan'),
              MyLineBotActionMsg::create()->postback('澳幣', 'cash=aus'),
              MyLineBotActionMsg::create()->postback('人民幣', 'cash=china'),
            ])
          )->reply($event->getReplyToken());
          break;

        case 'message':
          switch( $event->getMessageType() ) {
            case 'text':
              // MyLineBotMsg::create()
              //   ->text($event->getText())
              //   ->reply($event->getReplyToken());
              Log::info('bbb=123');
              $builder = MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
                  MyLineBotMsg::create()->templateConfirm( '你是女生？', [
                    MyLineBotActionMsg::create()->message('是', 'true'),
                    MyLineBotActionMsg::create()->postback('否', 'bbb=123'),
                  ])
              )->reply ($event->getReplyToken());
              break;

            case 'image':
              $url = $log->file->url();
              MyLineBotMsg::create()
                ->image($url, $url)
                ->reply ($event->getReplyToken());
              break;

            case 'video':
              $url = $log->file->url();
              MyLineBotMsg::create()
                ->video($url, $url)
                ->reply ($event->getReplyToken());
              break;

            case 'audio':
              $url = $log->file->url();
              MyLineBotMsg::create()
                ->audio($url, 60000)
                ->reply ($event->getReplyToken());
              break;

            case 'location':
              MyLineBotMsg::create()
                ->location($log->title, $log->address, $log->latitude, $log->longitude)
                ->reply ($event->getReplyToken());
              break;
          }
          break;
      }
    }
  }


}
