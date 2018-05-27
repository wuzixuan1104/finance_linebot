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
    $events = MyLineBot::events();
    foreach( $events as $event ) {

      if( !$source = Source::checkSourceExist($event) )
        continue;

      $speaker = Source::checkSpeakerExist($event);

      // if (!MyLineBotLog::init($source, $speaker, $event)->create())
      //   return false;
      Log::info('=======123');
      switch( $event->getMessageType() ) {
        case 'text':
          MyLineBotMsg::create()
            ->text($event->getText())
            ->reply($event->getReplyToken());
          break;
        case 'image':
          $url = 'https://api.line.me/v2/bot/message/'. $event->getMessageId() .'/content';
          Log::info('url:' . $url);
          $image = file_get_contents($url);
          Log::info('=======');
          Log::info($image);
          // MyLineBotMsg::create()
          //   ->image($url, $url)
          //   ->reply ($event->getReplyToken());
          break;
      }
    }
  }


}
