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
      switch($event->getMessageType()) {
        case "text":
          // $a = MyLineBotMsg::create()->text($event->getText());

          // $msg = MyLineBotMsg::create()->text($event->getText());
          // $msg = MyLineBotMultiMsg::create()->add( MyLineBotMsg::create()->text($event->getText()) )->add( MyLineBotMsg::create()->text($event->getText()) );
          // $msg = new LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
          // $builder = MyLineBotMultiMsg::create()->add( MyLineBotMsg::create()->text($event->getText()));
          // print_r($msg);
          // die;

          // $msg = MyLineBotMsg();
          // $msg->text ('asdasd');
          // $msg->reply ($event->getReplyToken());
          //

          $msg = MyLineBotMsg::create ()
                      ->multi ([
                        MyLineBotMsg::create ()->text ($event->getText()),
                        MyLineBotMsg::create ()->text ('hello')
                      ])
                      ->reply ($event->getReplyToken());

          // print_r($msg);
          // die;
          break;
        case "image":

          $msg = MyLineBotMsg();
          $msg->image ('url1', 'url2');
          $msg->reply ($event->getReplyToken());


          // $url = 'https://example.com/image_preview.jpg';
          // $builder = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($url, $url);
          break;
      }
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
