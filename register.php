<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "register";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $conn->real_escape_string(trim($_POST['username']));
    $newPassword = $_POST['password'];

    // Check if the username already exists
    $checkUser  = $conn->prepare("SELECT * FROM registration WHERE username = ?");
    $checkUser->bind_param("s", $newUsername);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows > 0) {
        echo "Username already exists. Please choose another one.";
    } else {
        // Hash the password before storing it
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $sql = $conn->prepare("INSERT INTO registration (username, password) VALUES (?, ?)");
        $sql->bind_param("ss", $newUsername, $hashedPassword);

        if ($sql->execute()) {
            echo "Account created successfully! You can now <a href='login.php'>log in</a>.";
        } else {
            echo "Error: " . $sql->error;
        }

        $sql->close();
    }

    $checkUser->close();
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>

<body>
    <h2>Create Account</h2>
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <br>
        <input type="submit" value="Create Account">
    </form>
    <p>Already have an account? <a href="login.php">Log in here</a>.</p>
</body>

</html>