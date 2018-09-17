<?php defined('MAPLE') || exit('此檔案不允許讀取！');

abstract class Controller {
  public function __construct() {
  }
}

class ControllerException extends Exception {
  private $messages = [];

  public function __construct($messages) {
    parent::__construct('');
    Router::setStatus(400);
    $this->messages = $messages;
  }

  public function getMessages() {
    return $this->messages;
  }
}

if (!function_exists('error')) {
  function error() {
    $args = func_get_args();

    if (!GG::$isApi)
      foreach (debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT) as $obj) {
        if (isset($obj['function'], $obj['args']) && $obj['args'] && $obj['function'] == 'validator' && $obj['args'][0] instanceof Closure) {
          $func = new ReflectionFunction($obj['args'][0]);
          $vars = $func->getStaticVariables();
          if (array_key_exists('params', $vars)) {
            $args = array_merge($args, [$vars['params']]);
            break;
          }
          
          break;
        }
        // 姿萱 sup up up
        if (isset($obj['function'], $obj['args']) && $obj['args'] && $obj['function'] == 'transaction' && $obj['args'][0] instanceof Closure) {
          $func = new ReflectionFunction($obj['args'][0]);
          $vars = $func->getStaticVariables();
          if (array_key_exists('params', $vars)) {
            $args = array_merge($args, [$vars['params']]);
            break;
          }
        }
      }

    throw new ControllerException($args);
  }
}

spl_autoload_register(function($className) {
  if (!preg_match("/Controller$/", $className))
    return false;

  Load::core($className . '.php') && class_exists($className) || gg('找不到名稱為「' . $className . '」的 Controller 物件！');
  return true;
});

