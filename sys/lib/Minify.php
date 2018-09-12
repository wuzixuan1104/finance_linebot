<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class Minify {
  public static function css($css) {
    $comment = '/\*[^*]*\*+(?:[^/*][^*]*\*+)*/';
    $dq = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';
    $sq = "'[^'\\\\]*(?:\\\\.[^'\\\\]*)*'";
    $css = preg_replace("<($dq|$sq)|$comment>Ss", "$1", $css);
    $css = preg_replace_callback('<' . '\s*([@{};,])\s*' . '| \s+([\)])' . '| ([\(:])\s+' . '>xS', function($m) {
      unset($m[0]);
      return current(array_filter($m));
    }, $css);

    return trim($css);
  }

  public static function html($html) {
    return trim(preg_replace(['/\>[^\S ]+/su', '/[^\S ]+\</su', '/(\s)+/su'], ['>', '<', '\\1'], $html));
  }

  public static function js($js) {
    try {
      return JSMin::minify($js);
    } catch (Exception $exception) {
      return $js;;
    }
  }

  public static function json($json) {
    return trim(JSONMin::minify($json));
  }
}

/**
 * jsmin.php - PHP implementation of Douglas Crockford's JSMin.
 *
 * This is pretty much a direct port of jsmin.c to PHP with just a few
 * PHP-specific performance tweaks. Also, whereas jsmin.c reads from stdin and
 * outputs to stdout, this library accepts a string as input and returns another
 * string as output.
 *
 * PHP 5 or higher is required.
 *
 * Permission is hereby granted to use this version of the library under the
 * same terms as jsmin.c, which has the following license:
 *
 * --
 * Copyright (c) 2002 Douglas Crockford  (www.crockford.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * The Software shall be used for Good, not Evil.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * --
 *
 * @package JSMin
 * @author Ryan Grove <ryan@wonko.com>
 * @copyright 2002 Douglas Crockford <douglas@crockford.com> (jsmin.c)
 * @copyright 2008 Ryan Grove <ryan@wonko.com> (PHP port)
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @version 1.1.1 (2008-03-02)
 * @link https://github.com/rgrove/jsmin-php/
 */

class JSMin {
  const ORD_LF    = 10;
  const ORD_SPACE = 32;

  protected $a           = '';
  protected $b           = '';
  protected $input       = '';
  protected $inputIndex  = 0;
  protected $inputLength = 0;
  protected $lookAhead   = null;
  protected $output      = '';

  // -- Public Static Methods --------------------------------------------------

  public static function minify($js) {
    $jsmin = new JSMin($js);
    return trim($jsmin->min());
  }

  // -- Public Instance Methods ------------------------------------------------

  public function __construct($input) {
    $this->input       = str_replace("\r\n", "\n", $input);
    $this->inputLength = strlen($this->input);
  }

  // -- Protected Instance Methods ---------------------------------------------



  /* action -- do something! What you do is determined by the argument:
          1   Output A. Copy B to A. Get the next B.
          2   Copy B to A. Get the next B. (Delete A).
          3   Get the next B. (Delete B).
     action treats a string as a single character. Wow!
     action recognizes a regular expression if it is preceded by ( or , or =.
  */
  protected function action($d) {
    switch($d) {
      case 1:
        $this->output .= $this->a;

      case 2:
        $this->a = $this->b;

        if ($this->a === "'" || $this->a === '"') {
          for (;;) {
            $this->output .= $this->a;
            $this->a       = $this->get();

            if ($this->a === $this->b) {
              break;
            }

            if (ord($this->a) <= self::ORD_LF) {
              throw new JSMinException('Unterminated string literal.');
            }

            if ($this->a === '\\') {
              $this->output .= $this->a;
              $this->a       = $this->get();
            }
          }
        }

      case 3:
        $this->b = $this->next();

        if ($this->b === '/' && (
            $this->a === '(' || $this->a === ',' || $this->a === '=' ||
            $this->a === ':' || $this->a === '[' || $this->a === '!' ||
            $this->a === '&' || $this->a === '|' || $this->a === '?' ||
            $this->a === '{' || $this->a === '}' || $this->a === ';' ||
            $this->a === "\n" )) {

          $this->output .= $this->a . $this->b;

          for (;;) {
            $this->a = $this->get();

            if ($this->a === '[') {
              /*
                inside a regex [...] set, which MAY contain a '/' itself. Example: mootools Form.Validator near line 460:
                  return Form.Validator.getValidator('IsEmpty').test(element) || (/^(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]\.?){0,63}[a-z0-9!#$%&'*+/=?^_`{|}~-]@(?:(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\])$/i).test(element.get('value'));
              */
              for (;;) {
                $this->output .= $this->a;
                $this->a = $this->get();

                if ($this->a === ']') {
                    break;
                } elseif ($this->a === '\\') {
                  $this->output .= $this->a;
                  $this->a       = $this->get();
                } elseif (ord($this->a) <= self::ORD_LF) {
                  throw new JSMinException('Unterminated regular expression set in regex literal.');
                }
              }
            } elseif ($this->a === '/') {
              break;
            } elseif ($this->a === '\\') {
              $this->output .= $this->a;
              $this->a       = $this->get();
            } elseif (ord($this->a) <= self::ORD_LF) {
              throw new JSMinException('Unterminated regular expression literal.');

            }

            $this->output .= $this->a;
          }

          $this->b = $this->next();
        }
    }
  }

  protected function get() {
    $c = $this->lookAhead;
    $this->lookAhead = null;

    if ($c === null) {
      if ($this->inputIndex < $this->inputLength) {
        $c = substr($this->input, $this->inputIndex, 1);
        $this->inputIndex += 1;
      } else {
        $c = null;
      }
    }

    if ($c === "\r") {
      return "\n";
    }

    if ($c === null || $c === "\n" || ord($c) >= self::ORD_SPACE) {
      return $c;
    }

    return ' ';
  }

  /* isAlphanum -- return true if the character is a letter, digit, underscore,
        dollar sign, or non-ASCII character.
  */
  protected function isAlphaNum($c) {
    return ord($c) > 126 || $c === '\\' || preg_match('/^[\w\$]$/', $c) === 1;
  }

  protected function min() {
    $this->a = "\n";
    $this->action(3);

    while ($this->a !== null) {
      switch ($this->a) {
        case ' ':
          if ($this->isAlphaNum($this->b)) {
            $this->action(1);
          } else {
            $this->action(2);
          }
          break;

        case "\n":
          switch ($this->b) {
            case '{':
            case '[':
            case '(':
            case '+':
            case '-':
              $this->action(1);
              break;

            case ' ':
              $this->action(3);
              break;

            default:
              if ($this->isAlphaNum($this->b)) {
                $this->action(1);
              }
              else {
                $this->action(2);
              }
          }
          break;

        default:
          switch ($this->b) {
            case ' ':
              if ($this->isAlphaNum($this->a)) {
                $this->action(1);
                break;
              }

              $this->action(3);
              break;

            case "\n":
              switch ($this->a) {
                case '}':
                case ']':
                case ')':
                case '+':
                case '-':
                case '"':
                case "'":
                  $this->action(1);
                  break;

                default:
                  if ($this->isAlphaNum($this->a)) {
                    $this->action(1);
                  }
                  else {
                    $this->action(3);
                  }
              }
              break;

            default:
              $this->action(1);
              break;
          }
      }
    }

    return $this->output;
  }

  /* next -- get the next character, excluding comments. peek() is used to see
             if a '/' is followed by a '/' or '*'.
  */
  protected function next() {
    $c = $this->get();

    if ($c === '/') {
      switch($this->peek()) {
        case '/':
          for (;;) {
            $c = $this->get();

            if (ord($c) <= self::ORD_LF) {
              return $c;
            }
          }

        case '*':
          $this->get();

          for (;;) {
            switch($this->get()) {
              case '*':
                if ($this->peek() === '/') {
                  $this->get();
                  return ' ';
                }
                break;

              case null:
                throw new JSMinException('Unterminated comment.');
            }
          }

        default:
          return $c;
      }
    }

    return $c;
  }

  protected function peek() {
    $this->lookAhead = $this->get();
    return $this->lookAhead;
  }
}

// -- Exceptions ---------------------------------------------------------------
class JSMinException extends Exception {}


/**
* php-json-minify
* @package JSONMin
* @version 0.6.2
* @link https://github.com/t1st3/php-json-minify
* @author t1st3 <https://github.com/t1st3>
* @license https://github.com/t1st3/php-json-minify/blob/master/LICENSE.md MIT
* @copyright Copyright (c) 2014, t1st3
* 
* 
* Based on JSON.minify (https://github.com/getify/JSON.minify) by Kyle Simspon (https://github.com/getify)
* JSON.minify is released under MIT license.
*
*/

/**
* The JSONMin class
* @author t1st3 <https://github.com/t1st3>
* @since 0.1.0
*/
class JSONMin {
  /**
  * The original JSON string
  * @var string $original_json The original JSON string
  * @since 0.1.0
  */
  protected $original_json = '';
  /**
  * The minified JSON string
  * @var string $minified_json The minified JSON string
  * @since 0.1.0
  */
  protected $minified_json = '';
  /**
  * Constructor
  * @name __construct
  * @param string $json Some JSON to minify
  * @since 0.1.0
  * @return object the JSONMin object
  */
  public function __construct ( $json ) {
    $this->original_json = $json;
    return $this;
  }
  /**
  * Get the minified JSON
  * @name getMin
  * @since 0.1.0
  * @return string Minified JSON string
  */
  public function getMin ( ) {
    $this->minified_json = $this::minify($this->original_json);
    return $this->minified_json;
  }
  /**
  * Print the minified JSON
  * @name printMin
  * @since 0.1.0
  * @return object the JSONMin object
  */
  public function printMin ( ) {
    echo $this->getMin();
    return $this;
  }
  /**
  * Static minify function
  * @name minify
  * @param string $json Some JSON to minify
  * @since 0.1.0
  * @return string Minified JSON string
  * @static
  */
  public static function minify ($json) {
    $tokenizer = "/\"|(\/\*)|(\*\/)|(\/\/)|\n|\r/";
    $in_string = false;
    $in_multiline_comment = false;
    $in_singleline_comment = false;
    $tmp; $tmp2; $new_str = array(); $ns = 0; $from = 0; $lc; $rc; $lastIndex = 0;
    while (preg_match($tokenizer,$json,$tmp,PREG_OFFSET_CAPTURE,$lastIndex)) {
      $tmp = $tmp[0];
      $lastIndex = $tmp[1] + strlen($tmp[0]);
      $lc = substr($json,0,$lastIndex - strlen($tmp[0]));
      $rc = substr($json,$lastIndex);
      if (!$in_multiline_comment && !$in_singleline_comment) {
        $tmp2 = substr($lc,$from);
        if (!$in_string) {
          $tmp2 = preg_replace("/(\n|\r|\s)*/","",$tmp2);
        }
        $new_str[] = $tmp2;
      }
      $from = $lastIndex;
      if ($tmp[0] == "\"" && !$in_multiline_comment && !$in_singleline_comment) {
        preg_match("/(\\\\)*$/",$lc,$tmp2);
        if (!$in_string || !$tmp2 || (strlen($tmp2[0]) % 2) == 0) { // start of string with ", or unescaped " character found to end string
          $in_string = !$in_string;
        }
        $from--; // include " character in next catch
        $rc = substr($json,$from);
      }
      else if ($tmp[0] == "/*" && !$in_string && !$in_multiline_comment && !$in_singleline_comment) {
        $in_multiline_comment = true;
      }
      else if ($tmp[0] == "*/" && !$in_string && $in_multiline_comment && !$in_singleline_comment) {
        $in_multiline_comment = false;
      }
      else if ($tmp[0] == "//" && !$in_string && !$in_multiline_comment && !$in_singleline_comment) {
        $in_singleline_comment = true;
      }
      else if (($tmp[0] == "\n" || $tmp[0] == "\r") && !$in_string && !$in_multiline_comment && $in_singleline_comment) {
        $in_singleline_comment = false;
      }
      else if (!$in_multiline_comment && !$in_singleline_comment && !(preg_match("/\n|\r|\s/",$tmp[0]))) {
        $new_str[] = $tmp[0];
      }
    }
    $new_str[] = $rc;
    return implode("",$new_str);
  }
}