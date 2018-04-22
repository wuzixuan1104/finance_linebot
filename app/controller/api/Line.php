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

    foreach( $events as $event ) {
      # text
      // $response = $bot->replyText($event->getReplyToken(), 'hello cherry!');
      // $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello master');
      // $response = $bot->replyMessage($event->getReplyToken(), $textMessageBuilder);

      # image
      // http://wpvoyager.purethe.me/files/2015/06/photo-1437747941115-61870b18ede5-420x400.jpg
      $imageMessageBuilder = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder('http://wpvoyager.purethe.me/files/2015/06/photo-1437747941115-61870b18ede5-420x400.jpg');
      $response = $bot->replyMessage($event->getReplyToken(), $imageMessageBuilder);

    }

  }
}
