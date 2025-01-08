<?php
// Mulai sesi untuk logout
session_start();

// Jika pengguna ingin logout
if (isset($_GET['logout'])) {
    session_unset(); // Menghapus semua data sesi
    session_destroy(); // Menghancurkan sesi
    header("Location: login.php"); // Arahkan kembali ke halaman login
    exit();
}

// Koneksi database (sesuaikan dengan konfigurasi Anda)
$conn = new mysqli("localhost", "root", "", "mbkm");

// Periksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data user
$user_id = $_SESSION['user_id'];
$sql = "SELECT first_name, last_name, email, phone, address, profile_picture FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Ambil data pengguna
    $user = $result->fetch_assoc();
    
    // Gabungkan first_name dan last_name menjadi full_name
    $first_name = isset($user['first_name']) ? $user['first_name'] : '';
    $last_name = isset($user['last_name']) ? $user['last_name'] : '';
    $full_name = trim($first_name . ' ' . $last_name);
} else {
    echo "Pengguna tidak ditemukan!";
    exit();
}

// Jika ada POST request untuk update profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    // Update password jika diisi
    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_password, $user_id);
            $stmt->execute();
        }
    }
    
    // Handle upload foto profil
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['profile_picture']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $temp_name = $_FILES['profile_picture']['tmp_name'];
            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = 'uploads/profile/' . $new_filename;
            
            if (move_uploaded_file($temp_name, $upload_path)) {
                $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $upload_path, $user_id);
                $stmt->execute();
            }
        }
    }
    
    // Update informasi profil
    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone, $address, $user_id);
    
    if ($stmt->execute()) {
        // Update session variable after successful profile update
    $_SESSION['full_name'] = $full_name;
    $success_message = "Profil berhasil diperbarui!";
    } else {
        $error_message = "Gagal memperbarui profil.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - MBKM UPJ</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }
        .header img {
            height: 50px;
        }
        .profile-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 15px;
            object-fit: cover;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            resize: vertical;
        }
        .btn {
            background-color: rgb(62, 65, 255);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: rgb(45, 48, 255);
        }
        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .breadcrumb {
            margin-bottom: 20px;
        }
        .breadcrumb a {
            color: rgb(62, 65, 255);
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="assets/logo-upj.png" alt="Logo UPJ" height="50">
            <div class="breadcrumb">
                <a href="dashboard.php">Home</a> > Edit Profil
            </div>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="profile-section">
                <img src="<?php echo $user['profile_picture'] ?? 'assets/default-profile.png'; ?>" 
                     alt="Profile Picture" 
                     class="profile-picture">
                <div class="form-group">
                    <label for="profile_picture">Ubah Foto Profil</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                </div>
            </div>

            <div class="form-group">
                <label for="full_name">Nama Lengkap</label>
                <input type="text" id="full_name" name="full_name" 
                       value="<?php echo htmlspecialchars($full_name); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Nomor Telepon</label>
                <input type="tel" id="phone" name="phone" 
                       value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>

            <div class="form-group">
                <label for="address">Alamat</label>
                <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="new_password">Password Baru (Opsional)</label>
                <input type="password" id="new_password" name="new_password">
            </div>

            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password (Opsional)</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>

            <button type="submit" class="btn">Perbarui Profil</button>
        </form>
    </div>
</body>
</html>

<?php
// Tutup koneksi database
$conn->close();
?>