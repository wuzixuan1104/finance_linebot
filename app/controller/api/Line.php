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

      switch( $event->getMessageType() ) {
        case 'text':
          // MyLineBotMsg::create()
          //   ->text($event->getText())
          //   ->reply($event->getReplyToken());
          $builder = MyLineBotMsg::create()->template('這訊息要用手機的賴才看的到哦',
              MyLineBotMsg::create()->templateCarousel( [
                MyLineBotMsg::create()->templateCarouselColumn('標題', '哈哈哈哈哈', 'https://cdn.adpost.com.tw/adpost/production/uploads/adv_details/pic/00/00/00/00/00/00/06/5e/_29753e27ceb64b0f35b77aca7acf9a3e.jpg', [
                  MyLineBotActionMsg::create()->datetimePicker('date', date('Y-m-d'), 'date', '', '', ''),
                  MyLineBotActionMsg::create()->uri("Google", "http://www.google.com"),
                  MyLineBotActionMsg::create()->postback('label', 'postback', 'https://chestnut.kerker.tw/api/line'),
                ]),
                MyLineBotMsg::create()->templateCarouselColumn('標題', '哈哈哈哈哈', 'https://cdn.adpost.com.tw/adpost/production/uploads/adv_details/pic/00/00/00/00/00/00/06/5e/_29753e27ceb64b0f35b77aca7acf9a3e.jpg', [
                  MyLineBotActionMsg::create()->datetimePicker('date', date('Y-m-d'), 'date', '', '', ''),
                  MyLineBotActionMsg::create()->message('label', 'test'),
                  MyLineBotActionMsg::create()->postback('label', 'postback', 'https://chestnut.kerker.tw/api/line'),
                ]),
              ])
          )->reply ($event->getReplyToken());

          break;

        case 'image':
          $url = $log->file->url();
          Log::info($url);
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
    }
  }


}
