<?php
require_once __DIR__ . '/connection.inc.php';
require_once __DIR__ . '/functions.inc.php';
require_once __DIR__ . '/add_to_cart.inc.php';
$cat_arr = db_query('SELECT * FROM categories WHERE status = 1 ORDER BY categories')->fetch_all(MYSQLI_ASSOC);
$obj = new add_to_cart();
$totalProduct = $obj->totalProduct();
$wishlist_count = 0;
if (isset($_SESSION['USER_ID'])) {
    $wishlist_count = db_one('SELECT COUNT(*) AS count FROM wishlist WHERE user_id = ?', [(int) $_SESSION['USER_ID']])['count'];
}
$mypage = basename($_SERVER['SCRIPT_NAME'] ?? '');
$meta_title = 'Delight Fashion';
$meta_desc = 'A demonstration catalog and order-management application.';
$meta_keyword = 'catalog, products';
if ($mypage === 'product.php') {
    $id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
    $product_meta = $id ? db_one('SELECT meta_title, meta_desc, meta_keyword FROM product WHERE id = ? AND status = 1', [$id]) : null;
    if ($product_meta) {
        $meta_title = $product_meta['meta_title']; $meta_desc = $product_meta['meta_desc']; $meta_keyword = $product_meta['meta_keyword'];
    }
}
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="<?php echo h(csrf_token()); ?>">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo h($meta_title); ?></title>
    <meta name="description" content="<?php echo h($meta_desc); ?>">
    <meta name="keywords" content="<?php echo h($meta_keyword); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/core.css">
    <link rel="stylesheet" href="css/shortcode/shortcodes.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/custom.css">
	<script src="js/vendor/modernizr-3.5.0.min.js"></script>
    <style>
        .htc__shopping__cart a span.htc__wishlist{
            background: #c43b68;
            border-radius: 100%;
            color:#fff;
            font-size: 9px;
            height: 17px;
            line-height: 19px;
            position:absolute;
            right:15px;


            width: 17px;
            text-align: center;

            top: -4px;


        }
    </style>

</head>
<body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <!-- Body main wrapper start -->
    <div class="wrapper">
        <header id="htc__header" class="htc__header__area header--one">
            <div id="sticky-header-with-topbar" class="mainmenu__wrap sticky__header">
                <div class="container">
                    <div class="row">
                        <div class="menumenu__container clearfix">
                            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-5">
                                <div class="logo">
                                     <a href="index.php"><img src="images/logo-last.png" alt="" srcset=""></a>
                                </div>
                            </div>
                            <div class="col-md-7 col-lg-6 col-sm-5 col-xs-3">
                                <nav class="main__menu__nav hidden-xs hidden-sm">
                                    <ul class="main__menu">
                                        <li class="drop"><a href="index.php">Home</a></li>
                                        <?php
										foreach($cat_arr as $list){
											?>
											<li><a href="categories.php?id=<?php echo h($list['id']); ?>"><?php echo h($list['categories']); ?></a></li>
											<?php
										}
										?>
                                        <li><a href="contact.php">contact</a></li>
                                    </ul>
                                </nav>

                                <div class="mobile-menu clearfix visible-xs visible-sm">
                                    <nav id="mobile_dropdown">
                                        <ul>
                                            <li><a href="index.php">Home</a></li>
                                            <?php
											foreach($cat_arr as $list){
												?>
												<li><a href="categories.php?id=<?php echo h($list['id']); ?>"><?php echo h($list['categories']); ?></a></li>
												<?php
											}
											 ?>
                                            <!-- // <li><a href="contact.php">contact</a></li> -->
                                            <li> <a href="admin/login.php">Admin</a></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3 col-sm-6 col-xs-6">
                                <div class="header__right">
                                <div class="header__search search search__open">
                                        <a href="#"><i class="icon-magnifier icons"></i></a>
                                </div>
                                    <div class="header__account">
                                        <?php
                                        if(isset($_SESSION['USER_LOGIN'])){
                                            echo '<form action="logout.php" method="post" style="display:inline">' . csrf_field() . '<button type="submit">Logout</button></form><br><a href="my_order.php">My Order</a>';

                                        }else{
                                            echo '<a href="login.php">Login/Register</a>';

                                        }
                                        ?>

                                    </div>
                                    <div class="htc__shopping__cart">
										<?php
										if(isset($_SESSION['USER_ID'])){
										?>
										<a href="wishlist.php"><i class="icon-heart icons"></i></a>
                                        <a href="wishlist.php"><span class="htc__wishlist"><?php echo h($wishlist_count); ?></span></a>
										<?php } ?>
                                        <a href="cart.php"><i class="icon-handbag icons"></i></a>
                                        <a href="cart.php"><span class="htc__qua"><?php echo h($totalProduct); ?></span></a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-menu-area"></div>
                </div>
            </div>
        </header>
        <div class="body__overlay"></div>
		<div class="offset__wrapper">
            <div class="search__area">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="search__inner">
                                <form action="search.php" method="get">
                                    <input placeholder="Search here... " type="text" name="str">
                                    <button type="submit"></button>
                                </form>
                                <div class="search__close__btn">
                                    <span class="search__close__btn_icon"><i class="zmdi zmdi-close"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
