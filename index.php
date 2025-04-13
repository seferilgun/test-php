```php
<?php
session_start();

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: calculator.php");
    exit;
} else {
    header("location: login.php");
    exit;
}

?>

</body>
</html>
```
