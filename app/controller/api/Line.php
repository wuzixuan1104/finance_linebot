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

  // public function index() {
  //   $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(config('line', 'channelToken'));
  //   $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => config('line', 'channelSecret')]);
  //   if( !isset ($_SERVER["HTTP_" . LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE]) )
  //     return false;
  //
  //   // $body = file_get_contents ("php://input");
  //   // $events = $bot->parseEventRequest ($body, $_SERVER["HTTP_" . LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE]);
  //   $receive = json_decode(file_get_contents("php://input"));
  //
  //   foreach( $receive->events as $event ) {
  //     // $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello');
  //     // $response = $bot->replyMessage($event->getReplyToken(), $textMessageBuilder);
  //
  //     # text
  //     Log::info($event->message->type);
  //     switch($event->message->type) {
  //       case 'text':
  //         // Log::info($event->message->text);
  //         $text = $event->message->text;
  //         Log::info($text);
  //         $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text);
  //         $response = $bot->replyMessage($event->getReplyToken(), $textMessageBuilder);
  //         break;
  //       case 'image':
  //         $img_url = "https://cdn.shopify.com/s/files/1/0379/7669/products/sampleset2_1024x1024.JPG?v=1458740363";
  //         $outputText = new LINE\LINEBot\MessageBuilder\ImageMessageBuilder($img_url, $img_url);
  //         $response = $bot->replyMessage($event->getReplyToken(), $outputText);
  //         break;
  //     }
  //   }
  //
  // }

  public function index() {
    $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(config('line', 'channelToken'));
    $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => config('line', 'channelSecret')]);
    if( !isset ($_SERVER["HTTP_" . LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE]) )
      return false;

  	$this->receive = json_decode(file_get_contents("php://input"));
  	$text = $this->receive->events[0]->message->text;
  	$type = $this->receive->events[0]->source->type;

  	// 由於新版的Messaging Api可以讓Bot帳號加入多人聊天和群組當中
  	// 所以在這裡先判斷訊息的來源
  	if ($type == "room")
  	{
  		// 多人聊天 讀取房間id
  		$this->from = $this->receive->events[0]->source->roomId;
  	}
  	else if ($type == "group")
  	{
  		// 群組 讀取群組id
  		$this->from = $this->receive->events[0]->source->groupId;
  	}
  	else
  	{
  		// 一對一聊天 讀取使用者id
  		$this->from = $this->receive->events[0]->source->userId;
  	}

  	// 讀取訊息的型態 [Text, Image, Video, Audio, Location, Sticker]
  	$content_type = $this->receive->events[0]->message->type;

  	// 準備Post回Line伺服器的資料
  	$this->header = ["Content-Type: application/json", "Authorization: Bearer {" . config('line', 'channelToken') . "}"];

  	// 回覆訊息
  	$this->reply($content_type, $text);
  }

  public function reply($content_type, $message) {

		$url = "https://chestnut.kerker.tw/api/line";

		$data = ["to" => $this->from, "messages" => array(["type" => "text", "text" => $message])];

		switch($content_type) {

			case "text" :
				$content_type = "文字訊息";
				$data = ["to" => $this->from, "messages" => array(["type" => "text", "text" => $message])];
				break;

			// case "image" :
			// 	$content_type = "圖片訊息";
			// 	$message = getObjContent("jpeg");   // 讀取圖片內容
			// 	$data = ["to" => $from, "messages" => array(["type" => "image", "originalContentUrl" => $message, "previewImageUrl" => $message])];
			// 	break;
      //
			// case "video" :
			// 	$content_type = "影片訊息";
			// 	$message = getObjContent("mp4");   // 讀取影片內容
			// 	$data = ["to" => $from, "messages" => array(["type" => "video", "originalContentUrl" => $message, "previewImageUrl" => $message])];
			// 	break;
      //
			// case "audio" :
			// 	$content_type = "語音訊息";
			// 	$message = getObjContent("mp3");   // 讀取聲音內容
			// 	$data = ["to" => $from, "messages" => array(["type" => "audio", "originalContentUrl" => $message[0], "duration" => $message[1]])];
			// 	break;
      //
			// case "location" :
			// 	$content_type = "位置訊息";
			// 	$title = $this->receive->events[0]->message->title;
			// 	$address = $receive->events[0]->message->address;
			// 	$latitude = $receive->events[0]->message->latitude;
			// 	$longitude = $receive->events[0]->message->longitude;
			// 	$data = ["to" => $from, "messages" => array(["type" => "location", "title" => $title, "address" => $address, "latitude" => $latitude, "longitude" => $longitude])];
			// 	break;
      //
			// case "sticker" :
			// 	$content_type = "貼圖訊息";
			// 	$packageId = $receive->events[0]->message->packageId;
			// 	$stickerId = $receive->events[0]->message->stickerId;
			// 	$data = ["to" => $from, "messages" => array(["type" => "sticker", "packageId" => $packageId, "stickerId" => $stickerId])];
			// 	break;

			default:
				$content_type = "未知訊息";
				break;
	   	}

		$context = stream_context_create(array(
		"http" => array("method" => "POST", "header" => implode(PHP_EOL, $this->header), "content" => json_encode($data), "ignore_errors" => true)
		));
		file_get_contents($url, false, $context);
	}
}
