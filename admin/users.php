<?php
$root = '../';
require __DIR__ . '/../config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int)($_POST['user_id'] ?? 0);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'user'");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        setFlash('error', 'User not found.');
    } else {
        $newValue = $user['is_active'] == 1 ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        $stmt->execute([$newValue, $userId]);
        setFlash('success', 'User ' . ($newValue == 1 ? 'activated.' : 'deactivated.'));
    }

    header("Location: users.php");
    exit;
}

$users = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC")->fetchAll();

$title = 'Manage Users';
include __DIR__ . '/../header.php';
?>
<h1>Manage Users</h1>

<div class="table-wrap">
<table>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Registered</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php if (empty($users)): ?>
        <tr><td colspan="5">No users yet.</td></tr>
    <?php endif; ?>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= e($u['name']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e($u['created_at']) ?></td>
            <td><?= $u['is_active'] == 1 ? 'Active' : 'Deactivated' ?></td>
            <td>
                <form method="post" class="inline-form">
                    <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                    <?php if ($u['is_active'] == 1): ?>
                        <button type="submit" class="red">Deactivate</button>
                    <?php else: ?>
                        <button type="submit" class="green">Activate</button>
                    <?php endif; ?>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
