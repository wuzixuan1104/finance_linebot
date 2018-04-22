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
        $type = strtolower(trim($event->getType()));
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
      // Log::info($event->message->type);
      // switch($event->message->type) {
      //   case 'text':
      //     // Log::info($event->message->text);
      //     $text = $event->message->text;
      //     Log::info($text);
      //     $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text);
      //     $response = $bot->replyMessage($event->getReplyToken(), $textMessageBuilder);
      //     break;
      //   case 'image':
      //     $img_url = "https://cdn.shopify.com/s/files/1/0379/7669/products/sampleset2_1024x1024.JPG?v=1458740363";
      //     $outputText = new LINE\LINEBot\MessageBuilder\ImageMessageBuilder($img_url, $img_url);
      //     $response = $bot->replyMessage($event->getReplyToken(), $outputText);
      //     break;
      // }
    }

  }

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
