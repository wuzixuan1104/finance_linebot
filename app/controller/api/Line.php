<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Line extends ApiController {

  public $header, $from, $receive;
  public function __construct() {
    parent::__construct();
  }

  public function index() {
    $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(config('line', 'channelToken'));
    $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => config('line', 'channelSecret')]);
    if( !isset ($_SERVER["HTTP_" . LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE]) )
      return false;

    $events = $bot->parseEventRequest (file_get_contents ("php://input"), $_SERVER["HTTP_" . LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE]);

    foreach( $events as $event ) {
      if ( $event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage ) {
        // switch($event->getMessageType()) {
        //   case "text":
        //     $outputText = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($event->getText());
        //     break;
        //   case "image":
        //     $url = 'https://example.com/image_preview.jpg';
        //     $outputText = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($url, $url);
        // }
        Log::info(1);
        Log::info($event->getMessageType());
        Log::info(2);
        $url = 'https://example.com/image_preview.jpg';
        $outputText = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($url, $url);
        $response = $bot->replyMessage($event->getReplyToken(), $outputText);

      }

    }

  }

}
