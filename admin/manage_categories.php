<?php
require_once 'connection.inc.php';
require_admin();
$id = isset($_GET['id']) ? positive_int($_GET['id']) : 0;
$category = $id ? db_one('SELECT * FROM categories WHERE id = ?', [$id]) : ['categories' => ''];
if (!$category) { http_response_code(404); exit('Category not found.'); }
$message = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    require_csrf();
    $category['categories'] = input_string($_POST, 'categories', 100);
    if ($category['categories'] === '') $message = 'Enter a category name.';
    else {
        try {
            if ($id) db_query('UPDATE categories SET categories = ? WHERE id = ?', [$category['categories'], $id]);
            else db_query('INSERT INTO categories (categories, status) VALUES (?, 1)', [$category['categories']]);
            redirect_to('admin/categories.php');
        } catch (mysqli_sql_exception $error) {
            if ($error->getCode() === 1062) $message = 'That category already exists.'; else throw $error;
        }
    }
}
require('top.inc.php');
?>
<main class="content"><div class="card"><div class="card-body"><h1><?php echo $id ? 'Edit' : 'Add'; ?> category</h1>
<p role="alert"><?php echo h($message); ?></p><form method="post"><?php echo csrf_field(); ?>
<p><label>Category <input class="form-control" name="categories" maxlength="100" required value="<?php echo h($category['categories']); ?>"></label></p>
<button class="btn btn-primary" type="submit">Save</button> <a href="categories.php">Cancel</a></form></div></div></main>
<?php require('footer.inc.php'); ?>
