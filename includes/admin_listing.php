<?php
require_once dirname(__DIR__) . '/connection.inc.php';
require_admin();
// The definition comes from a fixed PHP page, never from request parameters.
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    require_csrf();
    $id = positive_int($_POST['id'] ?? null);
    $action = input_string($_POST, 'action', 20);
    $table = $definition['table'];
    if ($table === 'contact_us' && $action === 'delete') {
        db_query('DELETE FROM contact_us WHERE id = ?', [$id]);
    } elseif (in_array($table, ['users', 'product', 'categories'], true) && in_array($action, ['activate', 'deactivate'], true)) {
        db_query('UPDATE ' . $table . ' SET status = ? WHERE id = ?', [$action === 'activate' ? 1 : 0, $id]);
    } else {
        throw new InvalidArgumentException('Invalid action.');
    }
    redirect_to('admin/' . $definition['page']);
}
$rows = db_query($definition['query'])->fetch_all(MYSQLI_ASSOC);
require dirname(__DIR__) . '/admin/top.inc.php';
?>
<main class="content"><div class="card"><div class="card-body">
<h1><?php echo h($definition['title']); ?></h1>
<?php if (!empty($definition['edit'])): ?><p><a href="<?php echo h($definition['edit']); ?>">Add new</a></p><?php endif; ?>
<div class="table-responsive"><table class="table"><thead><tr>
<?php foreach ($definition['columns'] as $label): ?><th><?php echo h($label); ?></th><?php endforeach; ?><th>Actions</th></tr></thead><tbody>
<?php foreach ($rows as $row): ?><tr>
<?php foreach ($definition['columns'] as $key => $label): ?><td><?php echo h($row[$key]); ?></td><?php endforeach; ?>
<td><?php if (!empty($definition['edit'])): ?><a href="<?php echo h($definition['edit']); ?>?id=<?php echo (int) $row['id']; ?>">Edit</a><?php endif; ?>
<?php if ($definition['table'] === 'contact_us') echo post_button('delete', (int) $row['id'], 'Delete'); else echo post_button($row['status'] ? 'deactivate' : 'activate', (int) $row['id'], $row['status'] ? 'Deactivate' : 'Activate'); ?>
</td></tr><?php endforeach; ?></tbody></table></div>
<?php if ($definition['table'] !== 'contact_us'): ?><p>Deactivate records to preserve their order and catalog relationships.</p><?php endif; ?>
</div></div></main><?php require dirname(__DIR__) . '/admin/footer.inc.php'; ?>
