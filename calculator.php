```php
<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$result = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $num1 = $_POST["num1"];
    $num2 = $_POST["num2"];
    $operation = $_POST["operation"];

    if (is_numeric($num1) && is_numeric($num2)) {
        switch ($operation) {
            case "add":
                $result = $num1 + $num2;
                break;
            case "subtract":
                $result = $num1 - $num2;
                break;
            case "multiply":
                $result = $num1 * $num2;
                break;
            case "divide":
                if ($num2 != 0) {
                    $result = $num1 / $num2;
                } else {
                    $result = "Sıfıra bölme hatası!";
                }
                break;
            default:
                $result = "Geçersiz işlem!";
        }
    } else {
        $result = "Lütfen geçerli sayılar girin!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hesap Makinesi</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Hesap Makinesi</h1>
    <form method="post">
        Sayı 1: <input type="text" name="num1"><br><br>
        Sayı 2: <input type="text" name="num2"><br><br>
        İşlem:
        <select name="operation">
            <option value="add">Toplama</option>
            <option value="subtract">Çıkarma</option>
            <option value="multiply">Çarpma</option>
            <option value="divide">Bölme</option>
        </select><br><br>
        <input type="submit" value="Hesapla">
    </form>

    <?php if ($result != '') { ?>
        <h2>Sonuç: <?php echo $result; ?></h2>
    <?php } ?>

    <p><a href="logout.php">Çıkış Yap</a></p>
</body>
</html>
```