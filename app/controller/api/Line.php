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
      // switch($event->getMessageType()) {
      //   case "text":
      //     $msg = MyLineBotMsg::create ()
      //                 ->multi ([
      //                   MyLineBotMsg::create ()->text ($event->getText()),
      //                   MyLineBotMsg::create ()->text ('hello')
      //                 ])
      //                 ->reply ($event->getReplyToken());
      //     break;
      //   case "image":
          $url = 'https://cdn.adpost.com.tw/adpost/production/uploads/adv_details/pic/00/00/00/00/00/00/06/5e/_29753e27ceb64b0f35b77aca7acf9a3e.jpg';
          $msg = MyLineBotMsg::create ()
                      ->multi([
                        MyLineBotMsg::create ()->image($url, $url),
                      ])
                      ->reply ($event->getReplyToken());
      
          // break;
      // }
      // print_R($msg);
      // die;
      // $response = MyLineBot::bot()->replyMessage($event->getReplyToken(), $builder);

      // $actions = array(
      //   //一般訊息型 action
      //   new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("按鈕1","文字1"),
      //   //網址型 action
      //   new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder("Google","http://www.google.com"),
      //   //下列兩筆均為互動型action
      //   new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("下一頁", "page=3"),
      //   new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("上一頁", "page=1")
      // );
      // Log::info(1);
      // $img_url = "https://example.com/image_preview.jpg";
      // $button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder("按鈕文字","說明", $img_url, $actions);
      // Log::info(2);
      // $msg = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("這訊息要用手機的賴才看的到哦", $button);
      // Log::info(3);
      // $bot->replyMessage($event->getReplyToken(),$msg);

    }

  }

}
