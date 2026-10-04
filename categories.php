<?php
require_once 'connection.inc.php';
require_once 'functions.inc.php';
$cat_id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
if (!$cat_id || $cat_id < 1) redirect_to('index.php');
$sort = is_string($_GET['sort'] ?? '') ? ($_GET['sort'] ?? '') : '';
$sorts = ['price_high' => ' order by product.price desc ', 'price_low' => ' order by product.price asc ', 'new' => ' order by product.id desc ', 'old' => ' order by product.id asc '];
$price_high_selected = $sort === 'price_high' ? 'selected' : '';
$price_low_selected = $sort === 'price_low' ? 'selected' : '';
$new_selected = $sort === 'new' ? 'selected' : '';
$old_selected = $sort === 'old' ? 'selected' : '';
$get_product = get_product($con, '', $cat_id, '', '', $sorts[$sort] ?? '');
require('top.php');
?>
<div class="body__overlay"></div>

        <!-- Start Bradcaump area -->
        <div class="ht__bradcaump__area" style="background: rgba(0, 0, 0, 0) url(images/bg/4.jpg) no-repeat scroll center center / cover ;">
            <div class="ht__bradcaump__wrap">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="bradcaump__inner">
                                <nav class="bradcaump-inner">
                                  <a class="breadcrumb-item" href="index.php">Home</a>
                                  <span class="brd-separetor"><i class="zmdi zmdi-chevron-right"></i></span>
                                  <span class="breadcrumb-item active">Products</span>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Bradcaump area -->
        <!-- Start Product Grid -->
        <section class="htc__product__grid bg__white ptb--100">
            <div class="container">
                <div class="row">
					<?php if(count($get_product)>0){?>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="htc__product__rightidebar">
                        <div class="htc__grid__top">
                                <div class="htc__select__option">
                                    <select class="ht__select" onchange="sort_product_drop('<?php echo h($cat_id); ?>','<?php echo h(SITE_PATH); ?>')" id="sort_product_id">
                                        <option value="">Default sorting</option>
                                        <option value="price_low" <?php echo h($price_low_selected); ?>>Sort by price low to high</option>
                                        <option value="price_high" <?php echo h($price_high_selected); ?>>Sort by price high to low</option>
                                        <option value="new" <?php echo h($new_selected); ?>>Sort by new first</option>
										<option value="old" <?php echo h($old_selected); ?>>Sort by old first</option>
                                    </select>
                                </div>

                            </div>
                            <!-- Start Product View -->
                            <div class="row">
                                <div class="shop__grid__view__wrap">
                                    <div role="tabpanel" id="grid-view" class="single-grid-view tab-pane fade in active clearfix">
                                        <?php
										foreach($get_product as $list){
										?>
										<!-- Start Single Category -->
										<div class="col-md-4 col-lg-3 col-sm-4 col-xs-12">
											<div class="category">
												<div class="ht__cat__thumb">
													<a href="product.php?id=<?php echo h($list['id']); ?>">
														<img src="<?php echo h(PRODUCT_IMAGE_SITE_PATH.$list['image']); ?>" alt="product images">
													</a>
												</div>
												<div class="fr__hover__info">
										<ul class="product__action">
											<li><a href="javascript:void(0)" onclick="wishlist_manage('<?php echo h($list['id']); ?>','add')"><i class="icon-heart icons"></i></a></li>
											<li><a href="javascript:void(0)" onclick="manage_cart('<?php echo h($list['id']); ?>','add')"><i class="icon-handbag icons"></i></a></li>
										</ul>
									</div>
												<div class="fr__product__inner">
													<h4><a href="product.php?id=<?php echo h($list['id']); ?>"><?php echo h($list['name']); ?></a></h4>
													<ul class="fr__pro__prize">
														<li class="old__prize">$<?php echo h($list['mrp']); ?></li>
														<li>$<?php echo h($list['price']); ?></li>
													</ul>
												</div>
											</div>
										</div>
										<?php } ?>
                                    </div>
							   </div>
                            </div>
                        </div>
                    </div>
					<?php } else {
						echo "Data not found";
					} ?>

				</div>
            </div>
        </section>
        <!-- End Product Grid -->
        <!-- End Banner Area -->
<?php require('footer.php')?>
