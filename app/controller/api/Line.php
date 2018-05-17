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

      $log = MyLineBotLog::init($source, $speaker, $event)->create();

      switch( $event->getMessageType() ) {
        case 'text':
          // $param = array(
          //   'source_id' => $source->id,
          //   'speaker_id' => $speaker->id,
          //   'reply_token' => $event->getReplyToken(),
          //   'message_id' => $event->getMessageId(),
          //   'text' => $event->getText(),
          //   'timestamp' => $event->getTimestamp() ? $event->getTimestamp() : '',
          // );
          //
          // if( !$obj = Text::create( $param ) )
          //   return false;

          // if( $obj = Text::save($source, $speaker, $event) )
          MyLineBotMsg::create()
            ->text($event->getText())
            ->reply($event->getReplyToken());

          break;
      }
      // $sid = $event->getEventSourceId();
      // if( !$user = $this->checkUserExist( $event->getUserId() ) )
      //   return false;
    }
  }


}
