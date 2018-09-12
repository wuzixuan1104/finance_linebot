<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class Log {
  const EXT = '.log';
  const DATE_FORMAT = 'H:i:s';
  const PERMISSIONS = 0777;

  private static $type = null;
  private static $fopens = [];

  public static function msg($text, $prefix) {
    if (!is_dir(PATH_LOG) || !isReallyWritable(PATH_LOG))
      return false;

    $path = PATH_LOG . $prefix . DIRECTORY_SEPARATOR;
    is_dir($path) || umaskMkdir($path, 0777);

    if (!is_dir($path) || !isReallyWritable($path))
      return false;

    $path .= date('Y-m-d') . Log::EXT;
    $newfile = !file_exists($path);

    if (!isset(self::$fopens[$path]))
      if (!$fopen = @fopen($path, 'ab'))
        return false;
      else
        self::$fopens[$path] = $fopen;

    for($written = 0, $length = charsetStrlen($text); $written < $length; $written += $result)
      if (($result = fwrite(self::$fopens[$path], charsetSubstr($text, $written))) === false)
        break;

    $newfile && @chmod($path, Log::PERMISSIONS);

    return is_int($result);
  }

  private static function logFormat($args) {
    $args = implode("\n" . cc('', 'N'), array_map(function($arg) { return cc('➜ ', 'G') . dump($arg); }, $args));
    return cc('※ ', 'R') . date(Log::DATE_FORMAT) . "\n" . cc(str_repeat('─', 40), 'N') . "\n" . $args . "\n\n\n";
  }

  public static function info($msg) {
    return self::msg(self::logFormat(func_get_args()), 'info');
  }

  public static function error($msg) {
    return self::msg(self::logFormat(func_get_args()), 'error');
  }

  public static function warning($msg) {
    return self::msg(self::logFormat(func_get_args()), 'warning');
  }

  public static function model($msg) {
    return self::msg(self::logFormat(func_get_args()), 'model');
  }

  public static function uploader($msg) {
    return self::msg(self::logFormat(func_get_args()), 'uploader');
  }

  public static function saveTool($msg) {
    return self::msg(self::logFormat(func_get_args()), 'saveTool');
  }

  public static function thumbnail($msg) {
    return self::msg(self::logFormat(func_get_args()), 'thumbnail');
  }

  public static function benchmark($msg) {
    return self::msg(self::logFormat(func_get_args()), 'benchmark');
  }

  public static function closeAll() {
    foreach(self::$fopens as $fopen)
      fclose($fopen);
    return true;
  }

  private static function queryFormat($args) {
    $valid = $args[0];
    $time = $args[1];
    $sql = $args[2];
    $vals = $args[3];

    $new = '';

    if (!self::$type) {
      $new = "\n" . cc(str_repeat('─', 80), 'N') . "\n";
      self::$type = ENVIRONMENT !== 'cmd' ? isCli() ? cc('cli', 'c') . cc(' ➜ ', 'N') . cc(implode('/', Url::segments()), 'C') : cc('web', 'p') . cc(' ➜ ', 'N') . cc(implode('/', Url::segments()), 'P') : cc('cmd', 'y') . cc(' ➜ ', 'N') . cc(CMD, 'Y');
    }
    return $new . self::$type . cc('│', 'N') . cc(date(Log::DATE_FORMAT), 'w') . cc(' ➜ ', 'N') . cc($time, $time < 999 ? $time < 99 ? $time < 9 ? 'w' : 'W' : 'Y' : 'R') . '' . cc('ms', $time < 999 ? $time < 99 ? $time < 9 ? 'N' : 'w' : 'y' : 'r') . cc('│', 'N') . ($valid ? cc('OK', 'g') : cc('GG', 'r')) . cc(' ➜ ', 'N') . call_user_func_array('sprintf', array_merge(array(preg_replace_callback('/\?/', function($matches) { return cc('%s', 'W'); }, $sql)), $vals)) . "\n";
  }
  public static function query($valid, $time, $sql, $vals) {
    @self::msg(self::queryFormat(func_get_args()), 'query');
    return true;
  }

}
