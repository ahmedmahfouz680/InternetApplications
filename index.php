<?php
$host     = "localhost";
$username = "root";
$password = "";
$dbname   = "people_db";

$conn = new mysqli($host, $username,$password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($dbname);

$table_sql = "CREATE TABLE IF NOT EXISTS `people` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($table_sql);

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_person'])) {
    $person_name = trim($_POST['person_name']);
    
    if (!empty($person_name)) {
        $stmt =$conn->prepare("INSERT INTO people (name) VALUES (?)");
        $stmt->bind_param("s", $person_name);
        
        if ($stmt->execute()) {$message = "<div class='alert success'>Person added successfully!</div>";
        } else {
            $message = "<div class='alert error'>Error adding record.</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='alert error'>Please enter a name.</div>";
    }
}


$result =$conn->query("SELECT * FROM people ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>People Management System</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f4f6f9;
            color: #333;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 600px;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        input[type="text"] {
            flex: 1;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }

        input[type="text"]:focus {
            border-color: #3498db;
        }

        button {
            padding: 12px 20px;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #27ae60;
        }

        .alert {
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #34495e;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .empty-row {
            text-align: center;
            color: #777;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>People List</h2>

    <?php echo $message; ?>

    <form action="" method="POST">
        <input type="text" name="person_name" placeholder="Enter person name..." required>
        <button type="submit" name="add_person">Add</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result &&$result->num_rows > 0): ?>
                <?php $i = 1; while($row =$result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="empty-row">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>