<?php
require FCPATH . 'vendor/autoload.php';
use Facebook\FacebookApp;
use Facebook\SignedRequest;

defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Fb {

  private static $fb = null;

  public function __construct ($config = array ()) {
  }

  public static function faceBook () {
    if (self::$fb !== null)
      return self::$fb;

    return self::$fb = new Facebook\Facebook ([
      'app_id' => config ('facebook', 'appId'),
      'app_secret' => config ('facebook', 'secret'),
      'default_graph_version' => config ('facebook', 'version')
    ]);
  }

  public static function loginUrl () {
    $helper = self::faceBook ()->getRedirectLoginHelper ();
    $permissions = config ('facebook', 'scope');
    return htmlspecialchars ($helper->getLoginUrl (URL::base (func_get_args ()), $permissions));
  }

  public static function me () {
    $helper = self::faceBook ()->getRedirectLoginHelper ();
    isset ($_GET['state']) && $helper->getPersistentDataHandler ()->set('state', $_GET['state']);

    try {
      $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
      return null;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      return null;
    }

    $get_fields = implode (',', config ('facebook', 'get_fields'));
    self::faceBook ()->setDefaultAccessToken ($accessToken);
    return self::faceBook ()->get ('/me' . ($get_fields ? '?fields=' . $get_fields : ''))->getGraphUser ();
  }
}