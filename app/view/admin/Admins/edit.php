<div class='back'>
  <a href="<?php echo RestfulURL::index ();?>" class='icon-36'>回上一頁</a>
</div>

<?php echo $form->appendFormRows (
  Restful\Text::need ('名稱', 'name')->setAutofocus (true)->setMaxLength (255),
  Restful\Text::need ('帳號', 'account')->setAutofocus (true)->setMaxLength (255),
  Restful\Password::maybe ('修改密碼', 'password', '')->setAutofocus (true)->setMaxLength (255),
  Restful\Password::maybe ('確認密碼', 'password_check', '')->setAutofocus (true)->setMaxLength (255),
  Restful\Checkboxs::maybe ('角色權限', 'roles')->setItemKVs (AdminRole::$roleTexts)->setMany ('roles')->setColumnName ('role')
);?>
