<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

Load::lib ('PHPMailer-5.2.21/class.phpmailer.php');
Load::lib ('PHPMailer-5.2.21/class.smtp.php');

class Mail extends PHPMailer {
  public function __construct () {
    parent::__construct ();

    $config = config ('mail');

    if (isset ($config['host']) && isset ($config['port']) && isset ($config['user']) && isset ($config['password']) && isset ($config['from']) && isset ($config['from_name']) && $config['host'] && $config['port'] && $config['user'] && $config['password'] && $config['from'] && $config['from_name']) {
      $this->isSMTP ();
      $this->SMTPAuth = true;
      $this->Host = $config['host'];
      $this->Port = $config['port'];
      $this->Username = $config['user'];
      $this->Password = $config['password'];
      
      $this->From = $config['from'];
      $this->FromName = $config['from_name'];
    }

    $this->CharSet = $config['charset'];
    $this->Encoding = $config['encoding'];
    $this->isHTML (true);
    $this->WordWrap = 50;

    if (!empty($config['secure'])) {
      $this->SMTPSecure = $config['secure'];
    } else {
      $this->smtpConnect (array (
        'ssl' => array (
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true
      )));
    }
  }
  public function addTo ($address, $name = '') {
    $this->addAddress ($address, $name);
    return $this;
  }
  public function addCC ($address, $name = '') {
    parent::addCC ($address, $name);
    return $this;
  }
  public function addBCC ($address, $name = '') {
    parent::addBCC ($address, $name);
    return $this;
  }
  public function addFile ($path, $name = '') {
    $this->addAttachment ($path, $name);
    return $this;
  }
  public function setSubject ($subject) {
    $this->Subject = $subject;
    return $this;
  }
  
  public function setBody ($body) {
    $this->Body = $body;
    return $this;
  }

  public function setFrom ($address, $name = '', $auto = true) {
    parent::setFrom ($address, $name, $auto);
    return $this;
  }

  public static function create () {
    return new self ();
  }
}