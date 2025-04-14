<?php
session_start();

$users = [];
$response = file_get_contents("http://localhost/ecommerce-goup3/api/users.php");
if ($response) {
  $users = json_decode($response, true);
}

if (isset($_GET['delete'])) {
  $deletePayload = json_encode(["id" => (int) $_GET['delete']]);
  $ch = curl_init("http://localhost/ecommerce-goup3/api/users.php");
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $deletePayload);
  $result = curl_exec($ch);
  curl_close($ch);
  header("Location: users_admin.php");
  exit();
}

?>

<?php include '../includes/header.php'; ?>
<div class="container mt-4" style="min-height: 80vh;">
  <h2>User Management</h2>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Username</th>
        <th>Name</th>
        <th>Email</th>
        <th>Admin</th>
        <th style="width: 150px;">Edit</th>
        <th style="width: 150px;">Delete</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><?= htmlspecialchars($u['name']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= $u['is_admin'] ? 'Yes' : 'No' ?></td>
          <td>
            <a href="users_admin_details.php?Id=<?= $u['id'] ?>" class="btn btn-success btn-sm"><i class="bi bi-pen"></i>&nbsp;Edit</a>
          </td>
            <td>  
            <a href="?delete=<?= $u['id'] ?>" class="btn btn-danger btn-sm"
              onclick="return confirm('Are you sure to delete this user?')"><i class="bi bi-trash3"></i>&nbsp;Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include '../includes/footer.php'; ?>