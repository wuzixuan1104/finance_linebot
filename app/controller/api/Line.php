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
    // print_r($events);
    // die;
    foreach( $events as $event ) {
      // print_r($event->getUserId());
      // die;
      $obj = User::create( array('uid' => $event->getUserId() ));

      // MyLineBotMsg::create()->template( '這訊息要用手機的賴才看得到喔！',
      //   MyLineBotMsg::create()->templateImageCarousel([
      //     MyLineBotMsg::create()->templateImageCarouselColumn(
      //       'https://cdn.adpost.com.tw/adpost/production/uploads/adv_details/pic/00/00/00/00/00/00/06/5e/_29753e27ceb64b0f35b77aca7acf9a3e.jpg',
      //       MyLineBotActionMsg::create()->uri("Google","http://www.google.com")
      //     ),
      //     MyLineBotMsg::create()->templateImageCarouselColumn(
      //       'https://cdn.adpost.com.tw/adpost/production/uploads/adv_details/pic/00/00/00/00/00/00/06/5e/_29753e27ceb64b0f35b77aca7acf9a3e.jpg',
      //       MyLineBotActionMsg::create()->uri("Google","http://www.google.com")
      //     ),
      //     MyLineBotMsg::create()->templateImageCarouselColumn(
      //       'https://cdn.adpost.com.tw/adpost/production/uploads/adv_details/pic/00/00/00/00/00/00/06/5e/_29753e27ceb64b0f35b77aca7acf9a3e.jpg',
      //       MyLineBotActionMsg::create()->uri("Google","http://www.google.com")
      //     )
      //   ]))
      // ->reply ($event->getReplyToken());
    }
  }
}
