<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

abstract class SiteController extends Controller {
  public $layout, $view, $asset;

  public function __construct () {
    parent::__construct ();

    $flash = Session::getFlashData ('flash');

    $this->asset = Asset::create (1)
                        ->addCSS ('/assets/css/icon-site.css')
                        ->addCSS ('/assets/css/site/layout.css')

                        ->addJS ('/assets/js/res/jquery-1.10.2.min.js')
                        ->addJS ('/assets/js/res/jquery_ujs.js')
                        ->addJS ('/assets/js/res/imgLiquid-min.js')
                        ->addJS ('/assets/js/res/timeago.js')
                        ->addJS ('/assets/js/site/layout.js');


    $this->layout = View::create ('site/layout.php')
                        ->with ('flash', $flash)
                        ->with ('asset', $this->asset);

    get_flash_params ($flash['params']);

    $this->view = View::create ()
                      ->appendTo ($this->layout, 'content')
                      ->with ('flash', $flash)
                      ->with ('asset', $this->asset);
  }
}
