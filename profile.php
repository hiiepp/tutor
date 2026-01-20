<?php
include '../includes/header.php';

/* DỮ LIỆU GIẢ LẬP – SAU NÀY KẾT NỐI DATABASE */
$student = [
    'name' => 'Nguyễn Văn A',
    'email' => 'student@gmail.com',
    'phone' => '0987654321',
    'dob' => '2004-05-10',
    'gender' => 'Nam',
    'address' => 'Cầu Giấy, Hà Nội',
    'school' => 'THPT Cầu Giấy',
    'grade' => '12',
    'avatar' => '' 
];
?>

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: {
                        600: '#198754', // Màu xanh lá chủ đạo đồ án N14
                        700: '#146c43',
                    }
                }
            }
        }
    }
</script>

<style>
    /* CSS bổ trợ để kiểm soát kích thước Avatar tuyệt đối */
    .avatar-fixed-box {
        width: 100px;
        height: 100px;
        min-width: 100px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #198754;
        border: 4px solid white;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }
    .avatar-fixed-box img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Chống méo ảnh */
    }
</style>

<body class="bg-slate-50">
    <main class="py-10">
        <div class="container mx-auto px-4 max-w-4xl">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Hồ sơ cá nhân</h1>
                <p class="text-gray-600 mt-2">Cập nhật thông tin để tăng độ tin cậy trên hệ thống N14_WebGiaSu</p>
            </div>

            <form action="process-profile.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Ảnh đại diện</h2>
                    <hr class="mb-6 opacity-50">

                    <div class="flex items-center space-x-6">
                        <div class="avatar-fixed-box" id="avatar-container">
                            <i id="avatar-icon" class="fas fa-user text-white text-4xl opacity-50"></i>
                            <img id="avatar-preview" src="<?= !empty($student['avatar']) ? 'assets/uploads/'.$student['avatar'] : '#' ?>" 
                                 style="<?= !empty($student['avatar']) ? 'display:block' : 'display:none' ?>">
                        </div>

                        <div class="space-y-3">
                            <label for="avatarUpload" class="cursor-pointer inline-flex items-center bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                                <input type="file" id="avatarUpload" name="avatar" accept="image/*" class="hidden" onchange="previewImage(this)">
                                <i class="fas fa-upload mr-2 text-primary-600"></i>Tải ảnh lên
                            </label>
                            <p class="text-xs text-gray-400">Định dạng JPG, PNG. Dung lượng tối đa 2MB.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <h2 class="text-sm font-bold text-primary-600 uppercase tracking-widest mb-6 border-l-4 border-primary-600 pl-3">Thông tin chi tiết</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Họ và tên *</label>
                            <input type="text" name="fullname" required value="<?= $student['name'] ?>" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600 outline-none transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Số điện thoại *</label>
                            <input type="tel" name="phone" required value="<?= $student['phone'] ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Email</label>
                            <input type="email" value="<?= $student['email'] ?>" disabled
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-lg bg-gray-50 text-gray-400 cursor-not-allowed">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Ngày sinh</label>
                            <input type="date" name="birthday" value="<?= $student['dob'] ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Giới tính</label>
                            <select name="gender" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 outline-none">
                                <option value="Nam" <?= $student['gender']=='Nam'?'selected':'' ?>>Nam</option>
                                <option value="Nữ" <?= $student['gender']=='Nữ'?'selected':'' ?>>Nữ</option>
                            </select>
                        </div>

                        <div class="space-y-2 md:col-span-1">
                            <label class="block text-sm font-bold text-gray-700">Địa chỉ</label>
                            <input type="text" name="address" value="<?= $student['address'] ?>"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 outline-none">
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Trường học</label>
                                <input type="text" name="school" value="<?= $student['school'] ?>" placeholder="VD: THPT Văn Lang"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Lớp / Khối</label>
                                <input type="text" name="grade" value="<?= $student['grade'] ?>" placeholder="VD: 12"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <a href="dashboard.php" class="text-gray-500 hover:text-gray-700 font-medium transition-colors px-4">Hủy bỏ</a>
                    <button type="submit" name="update_profile" class="bg-primary-600 text-white px-8 py-3 rounded-xl hover:bg-primary-700 transition-all font-bold shadow-md shadow-green-100">
                        <i class="fas fa-save mr-2"></i>Lưu tất cả thay đổi
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('avatar-preview');
                    const icon = document.getElementById('avatar-icon');
                    img.src = e.target.result;
                    img.style.display = 'block';
                    if(icon) icon.style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

<?php include '../includes/footer.php'; ?>