<!DOCTYPE html>
<html lang="tw">
  <head>
    <meta http-equiv="Content-Language" content="zh-tw" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui" />

    <title>ADPost</title>

    <?php echo isset ($metas) ? implode ('', array_map (function ($meta) { return $meta ? '<meta ' . implode (' ', array_map (function ($k, $v) { return $k . '="' . str_replace ('"', "'", $v) . '"'; }, array_keys ($meta), array_values ($meta))) . '/>' : ''; }, $metas)) : '';?>
    <?php echo $asset->renderCSS ();?>
    <?php echo $asset->renderJS ();?>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-114990077-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-114990077-1');
</script>

  </head>
  <body lang="zh-tw">

    <main id='main'>
      <div id='box' title='驗證' class='verify-email' data-url='<?php echo URL::base ();?>'>
        <p style='text-align: center;'>信箱驗證完成，系統即將為你跳轉到首頁！</p>
        <p style='text-align: center;'>若未跳轉，請點擊下方登入按鈕。</p>
        <br>
        <a href="<?php echo URL::base ();?>" title='回首頁'>回首頁</a>
      </div>
    </main>

  </body>
</html>
