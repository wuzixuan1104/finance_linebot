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
        Log::info(1);
        Log::info($event->getSource());
        $type = strtolower(trim( $event->getType() ));
        Log::info(2);
        Log::info($type);
        Log::info($event->getText());
        switch($type) {
          case "text":
            Log::info(3);
            $outputText = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($event->getText());
            break;
        }
        $response = $bot->replyMessage($event->getReplyToken(), $outputText);
      }

    }

  }
    // public function index() {
    //   $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(config('line', 'channelToken'));
    //   $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => config('line', 'channelSecret')]);
    //   if( !isset ($_SERVER["HTTP_" . LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE]) )
    //     return false;
    //
    //   $access_token = config('line', 'channelToken');
    //
    //
    //   $json_string = file_get_contents('php://input');
    //   $json_obj = json_decode($json_string);
    //
    //   $event = $json_obj->{"events"}[0];
    //   $type  = $event->{"message"}->{"type"};
    //   $message = $event->{"message"};
    //   $reply_token = $event->{"replyToken"};
    //
    //   $post_data = [
    //     "replyToken" => $reply_token,
    //     "messages" => [
    //       [
    //         "type" => "text",
    //         "text" => $message->{"text"}
    //       ]
    //     ]
    //   ];
    //
    //   $ch = curl_init("https://api.line.me/v2/bot/message/reply");
    //   curl_setopt($ch, CURLOPT_POST, true);
    //   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    //   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //       'Content-Type: application/json',
    //       'Authorization: Bearer '.$access_token
    //       //'Authorization: Bearer '. TOKEN
    //   ));
    //   $result = curl_exec($ch);
    //   curl_close($ch);
    // }


}
