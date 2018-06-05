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

      switch( $event->getType() ) {
        case 'postback':
          Log::info('postback');
          MyLineBotMsg::create()->template('抬頭',
            MyLineBotMsg::create()->templateButton('按鈕', '説明', 'https://example.com/bot/images/image.jpg', [
              MyLineBotActionMsg::create()->message('是', 'true'),
              MyLineBotActionMsg::create()->postback('否', 'bbb=123'),
            ])
          )->reply($event->getReplyToken());
          Log::info('end============');
          break;

        case 'message':
          switch( $event->getMessageType() ) {
            case 'text':
              // MyLineBotMsg::create()
              //   ->text($event->getText())
              //   ->reply($event->getReplyToken());

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

            case 'file':
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
