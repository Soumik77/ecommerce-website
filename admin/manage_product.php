<?php
require_once 'connection.inc.php';
require_admin();
$id = isset($_GET['id']) ? positive_int($_GET['id']) : 0;
$defaults = ['categories_id' => '', 'name' => '', 'mrp' => '0.00', 'price' => '0.00', 'qty' => 0, 'image' => 'demo-shirt.svg', 'short_desc' => '', 'description' => '', 'meta_title' => '', 'meta_desc' => '', 'meta_keyword' => '', 'best_seller' => 0];
$product = $id ? db_one('SELECT * FROM product WHERE id = ?', [$id]) : $defaults;
if (!$product) { http_response_code(404); exit('Product not found.'); }
$message = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    require_csrf();
    $newImage = null;
    try {
        foreach (['name' => 150, 'short_desc' => 2100, 'description' => 10000, 'meta_title' => 255, 'meta_desc' => 2000, 'meta_keyword' => 2000] as $key => $limit) $product[$key] = input_string($_POST, $key, $limit);
        if ($product['name'] === '') throw new InvalidArgumentException('Enter a product name.');
        $product['categories_id'] = positive_int($_POST['categories_id'] ?? null);
        if (!db_one('SELECT id FROM categories WHERE id = ?', [$product['categories_id']])) throw new InvalidArgumentException('Select a valid category.');
        $product['price'] = money_string(money_cents(input_string($_POST, 'price', 20)));
        $product['mrp'] = money_string(money_cents(input_string($_POST, 'mrp', 20)));
        $quantity = filter_var($_POST['qty'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1000000]]);
        if ($quantity === false) throw new InvalidArgumentException('Stock must be a non-negative whole number.');
        $product['qty'] = $quantity;
        $product['best_seller'] = isset($_POST['best_seller']) ? 1 : 0;
        $upload = $_FILES['image'] ?? null;
        if ($upload && $upload['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($upload['error'] !== UPLOAD_ERR_OK || $upload['size'] > 4 * 1024 * 1024 || !is_uploaded_file($upload['tmp_name'])) throw new InvalidArgumentException('Upload a JPEG or PNG image smaller than 4 MB.');
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']);
            $extension = ['image/jpeg' => 'jpg', 'image/png' => 'png'][$mime] ?? null;
            if (!$extension || getimagesize($upload['tmp_name']) === false) throw new InvalidArgumentException('Upload a valid JPEG or PNG image.');
            $newImage = bin2hex(random_bytes(16)) . '.' . $extension;
            if (!move_uploaded_file($upload['tmp_name'], PRODUCT_IMAGE_SERVER_PATH . $newImage)) throw new RuntimeException('Image could not be saved.');
            $product['image'] = $newImage;
        }
        $fields = ['categories_id','name','mrp','price','qty','image','short_desc','description','meta_title','meta_desc','meta_keyword','best_seller'];
        $values = array_map(fn ($field) => $product[$field], $fields);
        if ($id) {
            $values[] = $id;
            db_query('UPDATE product SET categories_id=?, name=?, mrp=?, price=?, qty=?, image=?, short_desc=?, description=?, meta_title=?, meta_desc=?, meta_keyword=?, best_seller=? WHERE id=?', $values);
        } else {
            db_query('INSERT INTO product (categories_id,name,mrp,price,qty,image,short_desc,description,meta_title,meta_desc,meta_keyword,best_seller) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)', $values);
        }
        redirect_to('admin/product.php');
    } catch (Throwable $error) {
        if ($newImage) @unlink(PRODUCT_IMAGE_SERVER_PATH . $newImage);
        if ($error instanceof InvalidArgumentException) $message = $error->getMessage();
        else { error_log('Product save failed: ' . get_class($error)); $message = 'The product could not be saved.'; }
    }
}
$categories = db_query('SELECT id, categories FROM categories ORDER BY categories')->fetch_all(MYSQLI_ASSOC);
require('top.inc.php');
?>
<main class="content"><div class="card"><div class="card-body"><h1><?php echo $id ? 'Edit' : 'Add'; ?> product</h1>
<p role="alert"><?php echo h($message); ?></p>
<form method="post" enctype="multipart/form-data"><?php echo csrf_field(); ?>
<p><label>Category <select class="form-control" name="categories_id" required><?php foreach ($categories as $category): ?><option value="<?php echo (int) $category['id']; ?>" <?php echo $category['id'] == $product['categories_id'] ? 'selected' : ''; ?>><?php echo h($category['categories']); ?></option><?php endforeach; ?></select></label></p>
<?php foreach (['name' => 'Product name','mrp' => 'List price','price' => 'Selling price','qty' => 'Stock quantity','meta_title' => 'Page title','meta_desc' => 'Page description','meta_keyword' => 'Page keywords'] as $field => $label): ?>
<p><label><?php echo h($label); ?><input class="form-control" name="<?php echo h($field); ?>" value="<?php echo h($product[$field]); ?>" <?php echo in_array($field, ['name','mrp','price','qty']) ? 'required' : ''; ?>></label></p><?php endforeach; ?>
<p><label>Short description<textarea class="form-control" name="short_desc" maxlength="2100"><?php echo h($product['short_desc']); ?></textarea></label></p>
<p><label>Description<textarea class="form-control" name="description" maxlength="10000"><?php echo h($product['description']); ?></textarea></label></p>
<p><label><input type="checkbox" name="best_seller" value="1" <?php echo $product['best_seller'] ? 'checked' : ''; ?>> Featured product</label></p>
<p><label>Image (optional JPEG or PNG, maximum 4 MB)<input type="file" name="image" accept="image/jpeg,image/png"></label></p>
<button class="btn btn-primary" type="submit">Save product</button> <a href="product.php">Cancel</a>
</form></div></div></main><?php require('footer.inc.php'); ?>
