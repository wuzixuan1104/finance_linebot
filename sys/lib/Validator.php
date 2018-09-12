<?php defined('MAPLE') || exit('此檔案不允許讀取！');

Load::sysFunc('format.php');

class Validator {
  private $params, $key, $name, $isNeed;

  public function __construct(&$params, $key, $name, $isNeed = true, $d4 = null) {
    $this->params =& $params;
    $this->key = $key;
    $this->name = $name;
    $this->isNeed = $isNeed;

    if ($this->isNeed) {
      array_key_exists($this->key, $this->params) || self::error('「' . $this->name . '」需必填！');
      if (is_array($this->params[$this->key]))
        $this->params[$this->key] || self::error('「' . $this->name . '」需必填！');
      if (is_string($this->params[$this->key]))
        $this->params[$this->key] !== '' || self::error('「' . $this->name . '」需必填！');
    } else {
      if ($d4 !== null)
        array_key_exists($this->key, $this->params) || $this->params[$this->key] = $d4;
    }
  }

  public function params() {
    return $this->params;
  }
  public function isInt() {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->isNum()->trim()->stripTags();
    is_int(0 + $this->params[$this->key]) || self::error('「' . $this->name . '」必須是整數！');
    return $this;
  }

  public function isNumber() {
    return $this->isNum();
  }

  public function isNum() {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    is_numeric($this->params[$this->key]) || self::error('「' . $this->name . '」必須是數字！');
    return $this;
  }

  public function isString() {
    return $this->isStr();
  }

  public function isVarchar($maxLength) {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    return $this->isStr()->trim()->stripTags()->maxLength($maxLength);
  }

  public function isText() {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    return $this->isStr()->trim()->stripTags();
  }

  public function isUrl() {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->isText();

    if ($this->isNeed || $this->params[$this->key])
      isUrl($this->params[$this->key]) || self::error('「' . $this->name . '」格式錯誤！');

    return $this;
  }

  public function isPassword() {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    return $this->isStr()->trim();
  }

  public function isStr() {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    is_string($this->params[$this->key]) || self::error('「' . $this->name . '」必須是字串！');
    return $this;
  }

  public function isArray() {
    return $this->isArr();
  }

  public function isArr() {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    is_array($this->params[$this->key]) || self::error('「' . $this->name . '」必須是陣列！');
    return $this;
  }

  public function filter($enum = []) {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->isArr();

    $this->params[$this->key] = array_filter($this->params[$this->key], function($item) use ($enum) { return in_array($item, $enum); });
    
    if ($this->isNeed)
      $this->params[$this->key] || self::error('「' . $this->name . '」錯誤！');

    return $this;
  }

  public function isTaxNum() {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->isNum()->trim()->stripTags();
    isTaxNum($this->params[$this->key]) || self::error('「' . $this->name . '」格式錯誤！');
    return $this;
  }

  public function isId($model = null, Where $where = null) {
    if (!array_key_exists($this->key, $this->params))
      return $this;
    
    $this->isNum()->trim()->stripTags();

    if ($this->isNeed) $this->greater(0);
    else $this->greaterEqual(0);

    if ($model && $this->params[$this->key] > 0)
      $model::one(['select' => 'id', 'where' => Where::create('id = ?', $this->params[$this->key])->and($where)]) || self::error('資料不存在，「' . $this->name . '」錯誤！');

    return $this;
  }

  public function filterUploadFiles($config = null) {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->isArr();

    $this->params[$this->key] = array_filter($this->params[$this->key], function($param) use ($config) {
      if (!isUploadFile($param))
        return null;

      if (!empty($config['formats']) && !uploadFileInFormats($param, $config['formats']))
          return null;

      if (!empty($config['maxSize']) && $param['size'] > $config['maxSize'])
          return null;

      return $param['size'] > 0;
    });

    return $this;
  }

  public function isUploadFile($config = null) {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->isArr();

    isUploadFile($this->params[$this->key]) || self::error('「' . $this->name . '」格式錯誤！');
    empty($config['formats']) || uploadFileInFormats($this->params[$this->key], $config['formats']) || self::error('「' . $this->name . '」格式錯誤！');
    empty($config['maxSize']) || $this->params[$this->key]['size'] <= $config['maxSize'] || self::error('「' . $this->name . '」檔案大小錯誤！');
    $this->params[$this->key]['size'] > 0 || self::error('「' . $this->name . '」檔案大小錯誤！');

    return $this;
  }

  public function trim($mask = " \t\n\r\0\x0B") {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->params[$this->key] = trim($this->params[$this->key], $mask);
    
    if ($this->isNeed)
      $this->params[$this->key] !== '' || self::error('「' . $this->name . '」需必填！');

    return $this;
  }
  
  public function stripTags($allowableTags = null) {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->params[$this->key] = $allowableTags ? strip_tags($this->params[$this->key], $allowableTags) : strip_tags($this->params[$this->key]);

    if ($this->isNeed)
      $this->params[$this->key] !== '' || self::error('「' . $this->name . '」需必填！');

    return $this;
  }
  
  public function inEnum($enumConst) {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->isStr()->trim()->stripTags();
    in_array($this->params[$this->key], array_keys($enumConst), true) || self::error('「' . $this->name . '」格式錯誤！');
    return $this;
  }

  public function isLatLng() {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->isNum()->trim()->stripTags();
    return $this;
  }

  public function isDate() {
    if (!array_key_exists($this->key, $this->params))
      return $this;
    
    if ($this->isNeed || $this->params[$this->key]) {
      $this->isStr()->trim()->stripTags();
      isDate($this->params[$this->key]) || self::error('「' . $this->name . '」格式錯誤！');
    }
    return $this;
  }

  public function isDatetime() {
    if (!array_key_exists($this->key, $this->params))
      return $this;
    if ($this->isNeed || $this->params[$this->key]) {
      $this->isStr()->trim()->stripTags();
      isDatetime($this->params[$this->key]) || self::error('「' . $this->name . '」格式錯誤！');
    }
    return $this;
  }

  public function isEmail() {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->isStr()->trim()->maxLength(190);
    isEmail($this->params[$this->key]) || self::error('「' . $this->name . '」必須是 E-Mail 格式！');
    return $this;
  }

  public function length($min = null, $max = null) {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $min === null || mb_strlen($this->params[$this->key]) >= $min || self::error('「' . $this->name . '」長度最短需要 ' . $min . ' 個字！');
    $max === null || mb_strlen($this->params[$this->key]) <= $max || self::error('「' . $this->name . '」長度最長只能 ' . $max . ' 個字！');
    return $this;
  }

  public function maxLength($lenght) {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    mb_strlen($this->params[$this->key]) <= $lenght || self::error('「' . $this->name . '」長度最長只能 ' . $lenght . ' 個字！');
    return $this;
  }

  public function minLength($lenght) {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    mb_strlen($this->params[$this->key]) >= $lenght || self::error('「' . $this->name . '」長度最短需要 ' . $lenght . ' 個字！');
    return $this;
  }

  public function range($min = null, $max = null) {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $min === null || $this->params[$this->key] >= $min || self::error('「' . $this->name . '」需要大於等於 ' . $min . '！');
    $max === null || $this->params[$this->key] <= $max || self::error('「' . $this->name . '」需要小於等於 ' . $max . '！');
    return $this;
  }

  // <
  public function less($num) {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->params[$this->key] < $num || self::error('「' . $this->name . '」需要小於 ' . $num . '！');
    return $this;
  }

  // <=
  public function lessEqual($num) {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->params[$this->key] <= $num || self::error('「' . $this->name . '」需要小於等於 ' . $num . '！');
    return $this;
  }

  // >
  public function greater($num) {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->params[$this->key] > $num || self::error('「' . $this->name . '」需要大於 ' . $num . '！');
    return $this;
  }

  // >=
  public function greaterEqual($num) {
    if (!array_key_exists($this->key, $this->params))
      return $this;

    $this->params[$this->key] >= $num || self::error('「' . $this->name . '」需要大於等於 ' . $num . '！');
    return $this;
  }

  public static function need(&$params, $key, $name) {
    return new static($params, $key, $name, true);
  }

  public static function maybe(&$params, $key, $name, $d4 = null) {
    return new static($params, $key, $name, false, $d4);
  }

  public static function error($msg) {
    // throw new ControllerException([$msg]);
    return call_user_func_array('error', [$msg]);
  }
}
