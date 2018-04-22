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
    $msg = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();

    foreach( $events as $event ) {
      // switch($event->getMessageType()) {
      //   case "text":
      //     $outputText = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($event->getText());
      //     $msg->add($outputText);
      //     break;
      //   case "image":
      //     $url = 'https://example.com/image_preview.jpg';
      //     $outputText = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($url, $url);
      //     break;
      // }
      $actions = array(
        //一般訊息型 action
        new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("按鈕1","文字1"),
        //網址型 action
        new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder("Google","http://www.google.com"),
        //下列兩筆均為互動型action
        new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("下一頁", "page=3"),
        new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("上一頁", "page=1")
      );

      $img_url = "圖片網址，必需為 https (圖片非必填欄位)";
      $button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder("按鈕文字","說明", $img_url, $actions);
      $msg = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("這訊息要用手機的賴才看的到哦", $button);
      $bot->replyMessage($replyToken,$msg);
      // $response = $bot->replyMessage($event->getReplyToken(), $msg);

    }

  }

}
