<!DOCTYPE html>
<html lang="tw">
  <head>
    <meta http-equiv="Content-Language" content="zh-tw" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui" />

    <title>AD-POST 後台系統</title>

    <?php echo $asset->renderCSS ();?>
    <?php echo $asset->renderJS ();?>

  </head>
  <body lang="zh-tw">

    <main id='main'>
      <header id='main-header'>
        <a id='hamburger' class='icon-01'></a>
        <nav><b><?php echo isset ($title) && $title ? $title : '';?></b></nav>
        <a href='<?php echo URL::base ('admin/logout');?>' class='icon-02'></a>
      </header>

      <div class='flash <?php echo $flash['type'];?>'><?php echo $flash['msg'];?></div>

      <div id='container'>
  <?php echo isset ($content) ? $content : ''; ?>
      </div>

    </main>

    <div id='menu'>
      <header id='menu-header'>
        <a href='<?php echo URL::base ();?>' class='icon-21'></a>
        <span>AD-POST 後台系統</span>
      </header>

      <div id='menu-user'>
        <figure class='_ic'>
          <img src="<?php echo Asset::url ('assets/img/admin.png');?>">
        </figure>

        <div>
          <span>Hi, 您好!</span>
          <b><?php echo Admin::current ()->name;?></b>
        </div>
      </div>

      <div id='menu-main'>
        <div>
          <span class='icon-14'>會員</span>
          <div>
            <a href="<?php echo $url = RestfulURL::url ('admin/Users@index');?>" class='icon-16<?php echo isset ($current_url) && $url === $current_url ? ' active' : '';?>'>前台會員</a>
      <?php if (Admin::current()->in_roles (array (AdminRole::ROLE_ADMIN))) { ?>
              <a href="<?php echo $url = RestfulURL::url ('admin/Admins@index');?>" class='icon-15<?php echo isset ($current_url) && $url === $current_url ? ' active' : '';?>'>後台會員</a>
      <?php } ?>
          </div>
        </div>

        <div>
          <span class='icon-14'>廣告</span>
          <div>
            <a href="<?php echo $url = RestfulUrl::url ('admin/Advs@index');?>" class='icon-19<?php echo isset ($current_url) && $url === $current_url ? ' active' : '';?>'>廣告列表</a>
          </div>
        </div>

        <div>
          <span class='icon-14'>品牌</span>
          <div>
            <a href="<?php echo $url = RestfulUrl::url ('admin/Brands@index');?>" class='icon-42<?php echo isset ($current_url) && $url === $current_url ? ' active' : '';?>'>品牌列表</a>
            <a href="<?php echo $url = RestfulUrl::url ('admin/BrandProducts@index');?>" class='icon-42<?php echo isset ($current_url) && $url === $current_url ? ' active' : '';?>'>品牌商品列表</a>

          </div>
        </div>

        <div>
          <span class='icon-14'>帳戶</span>
          <div>
            <a href="<?php echo $url = RestfulUrl::url ('admin/Accounts@index');?>" class='icon-42<?php echo isset ($current_url) && $url === $current_url ? ' active' : '';?>'>帳戶列表</a>
          </div>
        </div>

        <div>
          <span class='icon-14'>獎金</span>
          <div>
            <a href="<?php echo $url = RestfulUrl::url ('admin/Bonuses@index');?>" class='icon-42<?php echo isset ($current_url) && $url === $current_url ? ' active' : '';?>'>獎金列表</a>
            <a href="<?php echo $url = RestfulUrl::url ('admin/BonusReceives@index');?>" class='icon-42<?php echo isset ($current_url) && $url === $current_url ? ' active' : '';?>'>獎金領取列表</a>

          </div>
        </div>

  <?php if (Admin::current()->in_roles (array (AdminRole::ROLE_ROOT))) { ?>
          <div>
            <span class='icon-14' data-cntlabel='backup' data-cnt='<?php echo $bcnt = Backup::count (Where::create ('`read` = ?', Backup::READ_NO));?>'>系統</span>
            <div>
              <a data-cntlabel='backup' data-cnt='<?php echo $bcnt;?>' href="<?php echo $url = RestfulURL::url ('admin/Backups@index');?>" class='icon-22<?php echo isset ($current_url) && $url === $current_url ? ' active' : '';?>'>每日備份</a>
            </div>
          </div>
  <?php } ?>


      </div>
    </div>

    <footer id='footer'><span>後台版型設計 by </span><a href='http://www.adpost.com.tw/' target='_blank'>AD Post</a></footer>

  </body>
</html>
