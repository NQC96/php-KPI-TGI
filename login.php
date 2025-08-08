<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Đăng Nhập</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap');
        body { font-family: 'Be Vietnam Pro', sans-serif; }
        /* Thêm style cho nút khi bị vô hiệu hóa */
        button:disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-2">Đăng Nhập</h2>
        <p class="text-center text-gray-500 mb-8">Chào mừng bạn trở lại!</p>

        <form id="loginForm">
            <div class="mb-5">
                <label for="username" class="block mb-2 text-sm font-medium text-gray-600">Tên đăng nhập</label>
                <input type="text" id="username" name="username" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Nhập tên đăng nhập của bạn" required>
            </div>
            <div class="mb-8">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-600">Mật khẩu</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="••••••••" required>
            </div>
            <button type="submit" id="submitButton" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition">
                Đăng Nhập
            </button>
        </form>

        <div id="message" class="mt-6 text-center text-sm"></div>
         <div class="text-center mt-4">
            <a href="register.html" class="font-medium text-blue-600 hover:underline">Chưa có tài khoản? Đăng ký ngay</a>
        </div>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const messageDiv = document.getElementById('message');
        const submitButton = document.getElementById('submitButton');

        loginForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            // Vô hiệu hóa nút và hiển thị trạng thái đang tải
            submitButton.disabled = true;
            submitButton.textContent = 'Đang xử lý...';
            messageDiv.textContent = '';

            const formData = new FormData(this);

            try {
                const response = await fetch('login.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                messageDiv.textContent = result.message;
                if (result.success) {
                    messageDiv.className = 'mt-6 text-center text-sm text-green-600';
                    // Chuyển hướng sau khi đăng nhập thành công
                    // setTimeout(() => { window.location.href = '/dashboard.php'; }, 1500);
                } else {
                    messageDiv.className = 'mt-6 text-center text-sm text-red-600';
                }
            } catch (error) {
                console.error('Lỗi:', error);
                messageDiv.textContent = 'Có lỗi xảy ra, không thể kết nối đến máy chủ.';
                messageDiv.className = 'mt-6 text-center text-sm text-red-600';
            } finally {
                // Kích hoạt lại nút sau khi xử lý xong
                submitButton.disabled = false;
                submitButton.textContent = 'Đăng Nhập';
            }
        });
    </script>
</body>
</html>
