<?php
// Thiết lập header để trả về JSON và chỉ chấp nhận phương thức POST
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
    exit;
}

// --- Cấu hình kết nối Database ---
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'my_login_app';

// --- Kết nối và Bắt lỗi ---
try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        throw new Exception("Lỗi kết nối CSDL: " . $conn->connect_error);
    }

    // --- Xử lý dữ liệu từ Form ---
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';
    $mscn = $_POST['mscn'] ?? null;

    // --- Kiểm tra dữ liệu đầu vào ---
    if (empty($username) || empty($password) || empty($email)) {
        throw new Exception('Tên đăng nhập, mật khẩu và email không được để trống.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Định dạng email không hợp lệ.');
    }

    // --- KIỂM TRA DỮ LIỆU TỒN TẠI (LOGIC MỚI, ĐÁNG TIN CẬY HƠN) ---
    // 1. Kiểm tra Username
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('Tên đăng nhập này đã tồn tại.');
    }
    $stmt->close();

    // 2. Kiểm tra Email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('Email này đã được sử dụng.');
    }
    $stmt->close();

    // 3. Kiểm tra MSCN (chỉ kiểm tra nếu người dùng có nhập)
    if (!empty($mscn)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE mscn = ?");
        $stmt->bind_param("s", $mscn);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception('Mã số cán bộ này đã tồn tại.');
        }
        $stmt->close();
    }

    // --- Băm mật khẩu và chèn vào DB ---
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $mscn_to_db = !empty($mscn) ? $mscn : null;

    $stmt = $conn->prepare("INSERT INTO users (username, email, mscn, password_hash) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $mscn_to_db, $password_hash);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Đăng ký thành công! Đang chuyển đến trang đăng nhập...']);
    } else {
        throw new Exception('Lỗi khi tạo tài khoản. Vui lòng thử lại.');
    }
    
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Bắt tất cả các lỗi và trả về thông báo
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
