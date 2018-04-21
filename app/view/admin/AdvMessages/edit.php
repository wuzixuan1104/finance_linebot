<div class='back'>
  <a href="<?php echo RestfulURL::index ();?>" class='icon-36'>回上一頁</a>
</div>

<?php echo $form->appendFormRows (
  Restful\Switcher::need ('是否啟用', 'enable')->setCheckedValue (Adv::ENABLE_ON),
  Restful\Switcher::need ('是否審核', 'review')->setCheckedValue (Adv::REVIEW_PASS),
  Restful\Selecter::need ('廣告分類', 'adv_type_id')->setItemObjs (AdvType::find ('all', array ('select' => 'id, name')), 'id', 'name'),
  Restful\Selecter::need ('廣告種類', 'type')->setItemObjs ( isset( Adv::$typeTexts ) ? array_map( function($value, $text) {
    return (object)array( 'value' => $value, 'text' => $text);
  }, array_keys(Adv::$typeTexts), Adv::$typeTexts ): array() , 'value', 'text'),
  Restful\Text::need ('標題', 'title')->setAutofocus (true)->setMaxLength (255),
  Restful\PureText::need ('簡述', 'description'),
  Restful\PureText::need ('內容', 'content'),
  Restful\Text::need ('網站鏈結', 'website_url'),
Restful\Images::maybe ('其他照片', 'pic')->setTip ('可上傳多張，預覽僅示意，未按比例')->setAccept ('image/*')->setMany ('detail')->setColumnName ('pic')
  // Restful\Images::maybe ('其他影片', 'link')->setTip ('可上傳多張，預覽僅示意，未按比例')->setAccept ('image/*')->setMany ('detail')->setColumnName ('link')
);?>
