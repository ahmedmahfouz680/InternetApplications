<?php
$conn = new mysqli("localhost", "root", "", "company_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $id = $_POST['id'];

    if ($id != "") {
        $stmt = $conn->prepare("UPDATE employees SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO employees (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute();
    }
    header("Location: index.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM employees WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php");
    exit();
}

$edit_id = "";
$edit_name = "";
$edit_email = "";

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res) {
        $edit_name = $res['name'];
        $edit_email = $res['email'];
    }
}

$result = $conn->query("SELECT * FROM employees");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f7f6; margin: 0; padding: 40px 20px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 650px; background: #ffffff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        form { display: flex; gap: 10px; margin-bottom: 25px; flex-wrap: wrap; }
        input[type="text"], input[type="email"] { flex: 1; min-width: 180px; padding: 10px 12px; border: 1px solid #dcdcdc; border-radius: 6px; font-size: 14px; outline: none; transition: border-color 0.2s; }
        input[type="text"]:focus, input[type="email"]:focus { border-color: #2563eb; }
        button { padding: 10px 20px; background-color: #2563eb; color: #ffffff; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background-color 0.2s; }
        button:hover { background-color: #1d4ed8; }
        table { width: 100%; border-collapse: collapse; overflow: hidden; border-radius: 6px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eeeeee; font-size: 14px; }
        th { background-color: #f8fafc; color: #334155; font-weight: 600; border-bottom: 2px solid #e2e8f0; }
        tr:hover { background-color: #f8fafc; }
        .actions { display: flex; gap: 8px; }
        a.btn { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: 500; transition: opacity 0.2s; }
        a.btn:hover { opacity: 0.85; }
        a.edit { background-color: #e0f2fe; color: #0284c7; }
        a.del { background-color: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>

<div class="container">
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
        <input type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($edit_name); ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($edit_email); ?>" required>
        <button type="submit" name="save"><?php echo $edit_id ? 'Update' : 'Add'; ?></button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td>
                <div class="actions">
                    <a class="btn edit" href="?edit=<?php echo $row['id']; ?>">Update</a>
                    <a class="btn del" href="?delete=<?php echo $row['id']; ?>">Delete</a>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>