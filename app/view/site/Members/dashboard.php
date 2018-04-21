<div class="alert <?php echo $flash['type'] ? ' ' . $flash['type'] . '' : '';?>"><?php echo $flash['msg'];?></div>

<div id='member'>
<?php
  echo View::create ('site/Members/_header.php')->with ('now', 'dashboard')->get (); ?>
</div>

<div id='add-ad-btn'>
  <a class='icon-11' href='<?php echo URL::base ('advs/add');?>'>新增廣告</a>
</div>

<div id='ads'><?php echo implode ('', array_map (function ($adv) {
    $status = '<div class="status" data-title="上下架">' . form_switch ('', '', '', $adv->enable == Adv::ENABLE_ON, array ('class' => 'switch ajax', 'data-column' => 'enable', 'data-url' => URL::base ('advs/' . $adv->id . '/enable'), 'data-true' => Adv::ENABLE_ON, 'data-false' => Adv::ENABLE_OFF)) . '</div>';
    $name = '<div class="name" data-title="廣告名稱"><div class="cover"><img src="' . ($adv->default ? $adv->default->pic->url ('w500') : Asset::url ('assets/img/d4.png')) . '" /></div><b>' . $adv->title . '</b><span>' . $adv->min_column ('content', 200) . '</span><time>' . $adv->created_at->format ('Y-m-d H:i:s') . '</time></div>';
    $pv = '<div class="pv" data-title="瀏覽量">' . $adv->cnt_view . '</div>';
    $like = '<div class="like-cnt" data-title="喜愛數">' . $adv->cnt_like . '</div>';
    $reply = '<div class="reply-cnt" data-title="回應數">' . $adv->cnt_message . '</div>';
    $ctrl = '<div class="ctrl" data-title="操作"><a href="' . URL::base ('advs/' . $adv->id . '/edit') . '" class="icon-12">編輯</a><a href="' . $adv->content_page_url () . '" class="icon-13">瀏覽</a><a data-url="' . $adv->content_page_url () . '" class="icon-14 share-fb">分享</a></div>';
    $label = '<label><a>詳細資訊</a></label>';
    $detail = '<div class="detail"><b>產品簡述</b><p>' . $adv->description . '</p><b>產品介紹</b><p>' . $adv->content . '</p><b>網址連結</b><p><a href="' . $adv->website_url . '">' . $adv->website_url . '</a></p></div>';

    return '<div class="ad">' . $status . $name . $pv . $like . $reply . $ctrl . $label . $detail . '</div>';
  }, $advs));?></div>
