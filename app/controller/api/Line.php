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
        Log::info($event['message']['type']);
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
  // public function index() {
  //   $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(config('line', 'channelToken'));
  //   $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => config('line', 'channelSecret')]);
  //   if( !isset ($_SERVER["HTTP_" . LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE]) )
  //     return false;
  //
  // 	$this->receive = json_decode(file_get_contents("php://input"));
  // 	$text = $this->receive->events[0]->message->text;
  // 	$type = $this->receive->events[0]->source->type;
  //
  // 	// 由於新版的Messaging Api可以讓Bot帳號加入多人聊天和群組當中
  // 	// 所以在這裡先判斷訊息的來源
  // 	if ($type == "room")
  // 	{
  // 		// 多人聊天 讀取房間id
  // 		$this->from = $this->receive->events[0]->source->roomId;
  // 	}
  // 	else if ($type == "group")
  // 	{
  // 		// 群組 讀取群組id
  // 		$this->from = $this->receive->events[0]->source->groupId;
  // 	}
  // 	else
  // 	{
  // 		// 一對一聊天 讀取使用者id
  // 		$this->from = $this->receive->events[0]->source->userId;
  // 	}
  //
  // 	// 讀取訊息的型態 [Text, Image, Video, Audio, Location, Sticker]
  // 	$content_type = $this->receive->events[0]->message->type;
  //
  // 	// 準備Post回Line伺服器的資料
  // 	$this->header = ["Content-Type: application/json", "Authorization: Bearer {" . config('line', 'channelToken') . "}"];
  //   Log::info($this->from);
  // 	// 回覆訊息
  // 	$this->reply($content_type, $text);
  // }


}
