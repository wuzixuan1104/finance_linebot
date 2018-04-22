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
    $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(config('line', 'channelToken'));
    $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => config('line', 'channelSecret')]);
    if( !isset ($_SERVER["HTTP_" . LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE]) )
      return false;

    $body = file_get_contents ("php://input");
    $events = $bot->parseEventRequest ($body, $_SERVER["HTTP_" . LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE]);
    Log::info('test');
    foreach( $events as $event ) {
      // $response = $bot->replyText($event->getReplyToken(), 'hello master!');
      // Log::info('hi~~');
      $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello master');
      $response = $bot->replyMessage($event->getReplyToken(), $textMessageBuilder);
    }

  }
}
