<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: index.php");
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

if (isset($_POST["submit"])) {
    $fullName = shift28($_POST["fullname"]); // Mengenkripsi Full Name
    $email = shift28($_POST["email"]); // Mengenkripsi Email
    $password = $_POST["password"];
    $passwordRepeat = $_POST["repeat_password"];

    // ...

    $passwordShifted = shift28($password);
    $passwordHash = password_hash($passwordShifted, PASSWORD_DEFAULT);

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_stmt_init($mysqli);
    $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
    if ($prepareStmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = mysqli_num_rows($result);
        if ($rowCount > 0) {
            echo "<div class='alert alert-danger'>Email already exists!</div>";
        } else {
            $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
            $stmt = mysqli_stmt_init($mysqli);
            $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
            if ($prepareStmt) {
                mysqli_stmt_bind_param($stmt, "sss", $fullName, $email, $passwordHash);
                mysqli_stmt_execute($stmt);
                echo "<div class='alert alert-success'>You are registered successfully.</div>";
            } else {
                die("Something went wrong");
            }
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
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div>
            <p>Already registered? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
