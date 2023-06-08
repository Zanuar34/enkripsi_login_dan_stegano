<?php
session_start();
if (isset($_SESSION["user"]) && $_SESSION["user"] == true) {
    echo "Welcome to dashboard, " . htmlspecialchars($_SESSION["user"]) . "!";
} else {
    echo "Please login first.";
   header("Location: dashboard.php");
}

function shift28($string) {
    $shiftedString = "";
    $length = strlen($string);
    for ($i = 0; $i < $length; $i++) {
        $char = $string[$i];
        // Menggeser karakter hanya jika karakter adalah huruf
        if (ctype_alpha($char)) {
            $ascii = ord($char);
            $shiftedAscii = $ascii + 28;
            // Menjaga rentang huruf A-Z / a-z
            if (ctype_upper($char)) {
                $shiftedAscii = ($shiftedAscii - 65) % 26 + 65;
            } else {
                $shiftedAscii = ($shiftedAscii - 97) % 26 + 97;
            }
            $shiftedChar = chr($shiftedAscii);
            $shiftedString .= $shiftedChar;
        } else {
            $shiftedString .= $char;
        }
    }
    return $shiftedString;
}

require_once "database.php";

if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $shiftedEmail = shift28($email); // Decrypt the entered email for comparison

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_stmt_init($mysqli);
    $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
    if ($prepareStmt) {
        mysqli_stmt_bind_param($stmt, "s", $shiftedEmail); // Use the decrypted email for comparison
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = mysqli_num_rows($result);
        if ($rowCount > 0) {
            $user = mysqli_fetch_assoc($result);
            $storedPassword = $user["password"];
            $passwordShifted = shift28($password);
            if (password_verify($passwordShifted, $storedPassword)) { // Verify the shifted password
                session_start();
                $_SESSION["user"] = "yes";
                header("Location: dashboard.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Password does not match</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Email does not exist</div>";
        }
    } else {
        die("Something went wrong");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form action="login.php" method="post">
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Login" name="login">
            </div>
        </form>
        <div>
            <p>Not registered? <a href="registration.php">Register here</a></p>
        </div>
    </div>
</body>
</html>