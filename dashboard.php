<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <h1>Dashboard Steganografi</h1>
    <?php
// Fungsi untuk menyembunyikan pesan dalam gambar
function hideMessage($imagePath, $message) {
    // Membaca gambar
    $image = imagecreatefromjpeg($imagePath);

    // Mengubah pesan menjadi bitstream
    $bits = str_split($message);

    // Menyembunyikan setiap bit dalam piksel gambar
    $index = 0;
    $messageLength = count($bits);
    $maxX = imagesx($image);
    $maxY = imagesy($image);
    for ($x = 0; $x < $maxX; $x++) {
        for ($y = 0; $y < $maxY; $y++) {
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            // Menyembunyikan bit pesan dalam komponen LSB dari setiap warna piksel
            if ($index < $messageLength) {
                $r = ($r & 0xFE) | $bits[$index];
                $index++;
            }
            if ($index < $messageLength) {
                $g = ($g & 0xFE) | $bits[$index];
                $index++;
            }
            if ($index < $messageLength) {
                $b = ($b & 0xFE) | $bits[$index];
                $index++;
            }

            // Mengganti warna piksel dengan warna yang sudah dimodifikasi
            $color = imagecolorallocate($image, $r, $g, $b);
            imagesetpixel($image, $x, $y, $color);
        }
    }

    // Menyimpan gambar yang sudah dimodifikasi
    imagejpeg($image, 'hidden_image.jpg');

    // Menghapus gambar dari memori
    imagedestroy($image);
}

// Fungsi untuk mengambil pesan yang disembunyikan dalam gambar
function retrieveMessage($imagePath) {
    // Membaca gambar
    $image = imagecreatefromjpeg($imagePath);

    // Mengambil bit pesan dari komponen LSB setiap piksel
    $bits = array();
    $maxX = imagesx($image);
    $maxY = imagesy($image);
    for ($x = 0; $x < $maxX; $x++) {
        for ($y = 0; $y < $maxY; $y++) {
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            // Mengambil bit pesan dari komponen LSB setiap warna piksel
            $bits[] = $r & 1;
            $bits[] = $g & 1;
            $bits[] = $b & 1;
        }
    }

    // Mengubah bitstream menjadi pesan
    $message = implode('', $bits);

    // Mengembalikan pesan yang telah diambil
    return $message;
}

// Form untuk menyembunyikan pesan dalam gambar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["hide"])) {
    $message = $_POST["message"];
    $imagePath = $_FILES["image"]["tmp_name"];

    if (!empty($message) && !empty($imagePath)) {
        ini_set('memory_limit', '-1'); // Menghilangkan batas alokasi memori
        hideMessage($imagePath, $message);
        echo "Pesan berhasil disembunyikan dalam gambar.";
    } else {
        echo "Silakan isi pesan dan pilih gambar.";
    }
}

// Form untuk mengambil pesan yang disembunyikan dalam gambar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["retrieve"])) {
    $imagePath = $_FILES["hidden_image"]["tmp_name"];

    if (!empty($imagePath)) {
        ini_set('memory_limit', '-1');
        $message = retrieveMessage($imagePath);
        echo "Pesan yang disembunyikan dalam gambar: " . $message;
    } else {
        echo "Silakan pilih gambar tersembunyi.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Steganografi</title>
</head>
<body>

    <h2>Sembunyikan Pesan dalam Gambar</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="message">Pesan:</label>
        <input type="text" name="message" id="message" required><br><br>
        <label for="image">Gambar:</label>
        <input type="file" name="image" id="image" accept="image/jpeg" required><br><br>
        <input type="submit" name="hide" value="Sembunyikan Pesan">
    </form>

    <h2>Ambil Pesan dari Gambar Tersembunyi</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="hidden_image">Gambar Tersembunyi:</label>
        <input type="file" name="hidden_image" id="hidden_image" accept="image/jpeg" required><br><br>
        <input type="submit" name="retrieve" value="Ambil Pesan">
    </form>
</body>
</html>
