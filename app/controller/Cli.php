<?php defined ('OACI') || exit ('此檔案不允許讀取。');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, OACI
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://www.ioa.tw/
 */

class Cli extends Controller {
  const PIC = false;

  public static function createAdvOthers ($adv) {
    array_map (function ($adv) {
      $detail = AdvDetail::create (array (
          'adv_id' => $adv->id,
          'type' => AdvDetail::TYPE_PICTURE,
          'link' => '',
          'pic' => '',
        ));

      if (Cli::PIC) {
        $pic = CreateDemo::pics (1);
        $detail->pic->putUrl ($pic['url']);
      }

      echo "AdvDetail ID：" . $detail->id . "\n";
    }, array_fill (0, rand (1, 5), $adv));

    array_map (function ($adv) {
      $user_id = User::find ('one', array ('order' => 'RAND()', 'where' => array ()))->id;
      if (!$like = AdvLike::find ('one', array ('where' => array ('user_id = ? AND adv_id = ?', $user_id, $adv->id))))
        $like = AdvLike::create (array (
            'user_id' => $user_id,
            'adv_id' => $adv->id,
          ));
      echo "Adv Like ID：" . $like->id . "\n";
    }, array_fill (0, rand (0, 10), $adv));

    array_map (function ($adv) {
      $user_id = User::find ('one', array ('order' => 'RAND()', 'where' => array ()))->id;
      if (!$view = AdvView::find ('one', array ('where' => array ('user_id = ? AND adv_id = ?', $user_id, $adv->id))))
        $view = AdvView::create (array (
            'user_id' => $user_id,
            'adv_id' => $adv->id,
          ));
      echo "Adv View ID：" . $view->id . "\n";
    }, array_fill (0, rand (0, 10), $adv));

    array_map (function ($adv) {
      $user_id = User::find ('one', array ('order' => 'RAND()', 'where' => array ()))->id;
      if (!$msg = AdvMessage::find ('one', array ('where' => array ('user_id = ? AND adv_id = ?', $user_id, $adv->id))))
        $msg = AdvMessage::create (array (
            'user_id' => $user_id,
            'adv_id' => $adv->id,
            'content' => CreateDemo::chi (rand (50, 100)),
          ));
      echo "Adv Msg ID：" . $msg->id . "\n";
    }, array_fill (0, rand (0, 10), $adv));


    $adv->cnt_like = count ($adv->likes);
    $adv->cnt_view = count ($adv->views);
    $adv->cnt_message = count ($adv->messages);

    return $adv->save ();
  }
  public function x () {

  }
  public function index () {
    Load::lib ('CreateDemo.php');

    ModelConnection::instance ()->query ('TRUNCATE TABLE `users`;');
    ModelConnection::instance ()->query ('TRUNCATE TABLE `accounts`;');
    ModelConnection::instance ()->query ('TRUNCATE TABLE `notifies`;');

    ModelConnection::instance ()->query ('TRUNCATE TABLE `brands`;');
    ModelConnection::instance ()->query ('TRUNCATE TABLE `brand_products`;');
    ModelConnection::instance ()->query ('TRUNCATE TABLE `brand_product_details`;');

    ModelConnection::instance ()->query ('TRUNCATE TABLE `advs`;');
    ModelConnection::instance ()->query ('TRUNCATE TABLE `adv_likes`;');
    ModelConnection::instance ()->query ('TRUNCATE TABLE `adv_details`;');
    ModelConnection::instance ()->query ('TRUNCATE TABLE `adv_messages`;');
    ModelConnection::instance ()->query ('TRUNCATE TABLE `adv_views`;');

    ModelConnection::instance ()->query ('TRUNCATE TABLE `banks`;');
    ModelConnection::instance ()->query ('TRUNCATE TABLE `bonuses`;');
    ModelConnection::instance ()->query ('TRUNCATE TABLE `bonus_receives`;');
    ModelConnection::instance ()->query ('TRUNCATE TABLE `bonus_receive_details`;');

    $banks = array_map (function () {
      $bank = Bank::create (array (
          'name' => CreateDemo::chiName (rand (2, 3)) . '銀行',
          'code' => CreateDemo::eng (6),
        ));
      echo "Bank ID：" . $bank->id . "\n";
      return $bank;
    }, array_fill (0, 30, null));

    $users = array_map (function () {
      $user = User::create (array (
          'name' => CreateDemo::chiName (rand (3, 4)),
          'email' => CreateDemo::email (),
          'password' => password_hash (CreateDemo::eng (5), PASSWORD_DEFAULT),
          'access_token' => '',
          'facebook_id' => '',
          'avatar' => '',
          'city' => '',
          'active' => 'on',
          'phone' => CreateDemo::num (10),
          'brief' => CreateDemo::chi (rand (50, 100)),
          'expertise' => CreateDemo::chi (rand (50, 100)),
        ));

      if (Cli::PIC) {
        $pic = CreateDemo::pics (1);
        $user->avatar->putUrl ($pic['url']);
      }

      echo "User ID：" . $user->id . "\n";

      array_map (function ($user) {
        $acc = Account::create (array (
            'user_id' => $user->id,
            'name' => CreateDemo::chiName (rand (3, 4)),
            'bank_id' => Bank::find ('one', array ('order' => 'RAND()', 'where' => array ()))->id,
            'bank_branch' => CreateDemo::chi (2) . '分行',
            'bank_account' => CreateDemo::eng (12),
            'phone' => CreateDemo::num (10)
          ));
        echo "Account ID：" . $user->id . "\n";
        return $acc;
      }, array_fill (0, rand (1, 2), $user));


      array_map (function ($user) {
        $tmp = rand (0, 1) ? User::find ('one', array ('order' => 'RAND()', 'where' => array ())) : null;

        $notify = Notify::create (array (
            'user_id' => $user->id,
            'send_id' => $tmp ? $tmp->id : 0,
            'content' => CreateDemo::chi (rand (10, 30)),
            'read' => rand (0, 1) ? Notify::READ_YES : Notify::READ_NO,
          ));

        echo "Notify ID：" . $notify->id . "\n";

        return $notify;
      }, array_fill (0, rand (0, 10), $user));

      return $user;
    }, array_fill (0, 30, null));
    shuffle ($users);
    echo "\n";

    $brands = array_map (function ($user) {
      $brand = Brand::create (array (
          'user_id' => $user->id,
          'name' => CreateDemo::chi (rand (50, 100)),
          'tax_number' => CreateDemo::num (9),
          'email' => CreateDemo::email (),
          'phone' => CreateDemo::num (10),
          'company_name' => CreateDemo::chi (rand (5, 10)),
          'company_city' => CreateDemo::chi (rand (5, 10)),
          'company_area' => CreateDemo::chi (rand (5, 10)),
          'company_address' => CreateDemo::chi (rand (5, 10)),
          'website' => CreateDemo::url (),
          'description' => CreateDemo::chi (rand (100, 150)),
        ));
      echo "Brand ID：" . $brand->id . "\n";

      array_map (function ($brand) {
        $product = BrandProduct::create (array (
            'brand_id' => $brand->id,
            'name' => CreateDemo::chi (rand (5, 10)),
            'rule' => CreateDemo::chi (rand (50, 100)),
            'description' => CreateDemo::chi (rand (50, 100)),
            'cnt_shoot' => 0,
          ));
        echo "Product ID：" . $product->id . "\n";

        array_map (function ($product) {
          $detail = BrandProductDetail::create (array (
              'brand_product_id' => $product->id,
              'type' => BrandProductDetail::TYPE_PICTURE,
              'pic' => '',
              'url' => '',
            ));

          if (Cli::PIC) {
            $pic = CreateDemo::pics (1);
            $detail->pic->putUrl ($pic['url']);
          }
          echo "Detail ID：" . $detail->id . "\n";

          return $detail;
        }, array_fill (0, rand (0, 5), $product));

        array_map (function ($product) use ($brand) {
          $user = User::find ('one', array ('order' => 'RAND()', 'where' => array ()));

          $adv = Adv::create (array (
              'user_id' => $user->id,
              'brand_id' => $brand->id,
              'brand_product_id' => $product->id,
              'title' => CreateDemo::chi (rand (5, 10)),
              'type' => Adv::TYPE_PICTURE,
              'review' => Adv::REVIEW_PASS,
              'description' => CreateDemo::chi (rand (50, 100)),
              'content' => CreateDemo::chi (rand (50, 100)),

              'cnt_like' => 0,
              'cnt_message' => 0,
              'cnt_view' => 0,
            ));

          echo "Product Adv ID：" . $adv->id . "\n";

          $bonus = Bonus::create (array (
              'user_id' => $user->id,
              'adv_id' => $adv->id,
              'price' => $t = CreateDemo::num (2),
              'remain_price' => $t,
            ));

          return Cli::createAdvOthers ($adv);
        }, array_fill (0, rand (0, 10), $product));

        $product->cnt_shoot = count (array_unique (array_orm_column ($product->advs, 'user_id')));

        return $product->save ();
      }, array_fill (0, rand (0, 4), $brand));

      array_map (function ($brand) {
        $user = User::find ('one', array ('order' => 'RAND()', 'where' => array ()));

        $adv = Adv::create (array (
            'user_id' => $user->id,
            'brand_id' => $brand->id,
            'brand_product_id' => 0,
            'title' => CreateDemo::chi (rand (5, 10)),
            'type' => Adv::TYPE_PICTURE,
            'review' => Adv::REVIEW_PASS,
            'description' => CreateDemo::chi (rand (30, 70)),
            'content' => CreateDemo::chi (rand (30, 70)),

            'cnt_like' => 0,
            'cnt_message' => 0,
            'cnt_view' => 0,
          ));

        echo "Brand Adv ID：" . $adv->id . "\n";

        $bonus = Bonus::create (array (
            'user_id' => $user->id,
            'adv_id' => $adv->id,
            'price' => $t = CreateDemo::num (2),
            'remain_price' => $t,
          ));
        return Cli::createAdvOthers ($adv);
      }, array_fill (0, rand (0, 4), $brand));

      echo "\n";
      return $brand;
    }, array_slice ($users, 0, rand (2, count ($users) / 2)));
    echo "\n";


    shuffle ($users);
    $receives = array_map (function ($receive) {
      $price = CreateDemo::num (2);
      $user = User::find ('one', array ('order' => 'RAND()', 'where' => array ()));

      if (!$bonusReceive = $user->receive (Account::find ('one', array ('order' => 'RAND()', 'where' => array ('user_id = ?', $user->id))), $price, BonusReceive::TYPE_ATM))
        echo "GG~~~~~~~~~~\n";
      else
        echo "BonusReceive ID：" . $bonusReceive->id . "\n";

      return $bonusReceive;
    }, array_slice ($users, 0, rand (2, count ($users) / 2)));
  }
}
