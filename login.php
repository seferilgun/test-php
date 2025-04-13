```php
<?php
session_start();

// Veritabanı bağlantı bilgileri
$host = 'localhost';
$dbname = 'kullanicilar';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Kullanıcı adı ve şifre boş bırakılamaz.";
    } else {
        $stmt = $db->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: calculator.php");
            exit();
        } else {
            $error = "Yanlış kullanıcı adı veya şifre.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Giriş Yap</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Giriş</button>
        </form>
        <p>Hesabınız yok mu? <a href="register.php">Kayıt Ol</a></p>
    </div>
</body>
</html>
```

```html
/* styles.css */

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.login-container {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
}

.login-container h2 {
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
    text-align: left;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-group input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
}

button:hover {
    background-color: #3e8e41;
}

.error {
    color: red;
    margin-bottom: 15px;
}
```
```php
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesap Makinesi</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="calculator-container">
        <h2>Hesap Makinesi</h2>
        <p>Hoşgeldiniz! Hesaplama yapmak için kullanabilirsiniz.</p>
        <a href="logout.php">Çıkış Yap</a>
        <!-- Hesap makinesi içeriği buraya gelecek -->
    </div>
</body>
</html>
```
```php
<?php
session_start();

// Oturumu sonlandır
session_destroy();

// Giriş sayfasına yönlendir
header("Location: login.php");
exit();
?>
```

```php
<?php
// register.php

// Veritabanı bağlantı bilgileri
$host = 'localhost';
$dbname = 'kullanicilar';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Kullanıcı adı ve şifre boş bırakılamaz.";
    } else {
        // Şifreyi hash'le
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Kullanıcıyı veritabanına ekle
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        try {
            $stmt->execute([$username, $hashed_password]);
            header("Location: login.php"); // Başarılı kayıttan sonra giriş sayfasına yönlendir
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry hatası (aynı kullanıcı adı)
                $error = "Bu kullanıcı adı zaten alınmış.";
            } else {
                $error = "Kayıt sırasında bir hata oluştu: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Kayıt Ol</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Kayıt Ol</button>
        </form>
        <p>Zaten bir hesabınız var mı? <a href="login.php">Giriş Yap</a></p>
    </div>
</body>
</html>
```
`kullanicilar` adında bir veritabanı ve içerisinde `users` adında bir tablo oluşturulmalıdır. `users` tablosu aşağıdaki sütunlara sahip olmalıdır:

*   `id` INT AUTO_INCREMENT PRIMARY KEY
*   `username` VARCHAR(255) UNIQUE NOT NULL
*   `password` VARCHAR(255) NOT NULL
