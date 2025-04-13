```php
<?php
session_start();

// Veritabanı bağlantı bilgileri
$host = 'localhost';
$dbname = 'your_database_name';
$username = 'your_username';
$password = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if (empty($username)) {
        $errors[] = "Kullanıcı adı boş olamaz.";
    }

    if (empty($password)) {
        $errors[] = "Şifre boş olamaz.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Şifre en az 8 karakter olmalıdır.";
    }


    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);

        try {
            $stmt->execute();
            $_SESSION['message'] = "Kayıt başarılı! Lütfen giriş yapın.";
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $errors[] = "Bu kullanıcı adı zaten alınmış.";
            } else {
                $errors[] = "Kayıt sırasında bir hata oluştu: " . $e->getMessage();
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

<div class="container">
    <h1>Kayıt Ol</h1>

    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
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

```css
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

.container {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
}

h1 {
    text-align: center;
    color: #333;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box; /* Padding dahil genişliği korur */
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

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
}

.error-message ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.error-message li {
    margin-bottom: 5px;
}
```

```php
<?php
session_start();

// Eğer oturum zaten açıksa anasayfaya yönlendir
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Veritabanı bağlantı bilgileri
$host = 'localhost';
$dbname = 'your_database_name';
$username = 'your_username';
$password = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if (empty($username)) {
        $errors[] = "Kullanıcı adı boş olamaz.";
    }

    if (empty($password)) {
        $errors[] = "Şifre boş olamaz.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Oturum başlat
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id']; // Kullanıcı ID'sini de oturuma ekle
            header("Location: index.php"); // Giriş yaptıktan sonra anasayfaya yönlendir
            exit();
        } else {
            $errors[] = "Kullanıcı adı veya şifre hatalı.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h1>Giriş Yap</h1>

    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="success-message">
            <?php echo $_SESSION['message']; ?>
            <?php unset($_SESSION['message']); ?>
        </div>
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

        <button type="submit">Giriş Yap</button>
    </form>

    <p>Hesabınız yok mu? <a href="register.php">Kayıt Ol</a></p>
</div>

</body>
</html>
```

```php
<?php
session_start();

// Eğer oturum açılmamışsa login sayfasına yönlendir
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Oturumu kapatma
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anasayfa</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h1>Hoş Geldiniz, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Başarıyla giriş yaptınız.</p>
    <a href="index.php?logout=true">Çıkış Yap</a>
</div>

</body>
</html>
```

Yukarıdaki kod parçacıkları çalışır bir kayıt, giriş ve anasayfa (index) sistemi oluşturmaktadır.  Kullanıcıların veritabanına kaydedilmesi, giriş yapması ve oturum açıp kapatabilmesi için gereken temel işlevleri sunmaktadır. Bu kodları kendi sunucunuzda kullanmadan önce, veritabanı bağlantı bilgilerini ve veritabanı şemasını (users tablosu) kendi ortamınıza göre yapılandırmanız önemlidir.
