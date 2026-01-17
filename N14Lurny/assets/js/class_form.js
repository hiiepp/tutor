// DỮ LIỆU TP.HCM (CẤU TRÚC CŨ: Giữ nguyên Q2, Q9, Q.Thủ Đức)
const hcmDataOld = {
    "Quận 1": ["Phường Tân Định", "Phường Đa Kao", "Phường Bến Nghé", "Phường Bến Thành", "Phường Nguyễn Thái Bình", "Phường Phạm Ngũ Lão", "Phường Cầu Ông Lãnh", "Phường Cô Giang", "Phường Nguyễn Cư Trinh", "Phường Cầu Kho"],
    "Quận 2": ["Phường Thảo Điền", "Phường An Phú", "Phường Bình An", "Phường Bình Trưng Đông", "Phường Bình Trưng Tây", "Phường Bình Khánh", "Phường An Khánh", "Phường Cát Lái", "Phường Thạnh Mỹ Lợi", "Phường An Lợi Đông", "Phường Thủ Thiêm"],
    "Quận 3": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14"],
    "Quận 4": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 6", "Phường 8", "Phường 9", "Phường 10", "Phường 13", "Phường 14", "Phường 15", "Phường 16", "Phường 18"],
    "Quận 5": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15"],
    "Quận 6": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14"],
    "Quận 7": ["Phường Tân Thuận Đông", "Phường Tân Thuận Tây", "Phường Tân Kiểng", "Phường Tân Hưng", "Phường Bình Thuận", "Phường Tân Quy", "Phường Phú Thuận", "Phường Tân Phú", "Phường Tân Phong", "Phường Phú Mỹ"],
    "Quận 8": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15", "Phường 16"],
    "Quận 9": ["Phường Long Bình", "Phường Long Thạnh Mỹ", "Phường Tân Phú", "Phường Hiệp Phú", "Phường Tăng Nhơn Phú A", "Phường Tăng Nhơn Phú B", "Phường Phước Long B", "Phường Phước Long A", "Phường Trường Thạnh", "Phường Long Phước", "Phường Long Trường", "Phường Phước Bình", "Phường Phú Hữu"],
    "Quận 10": ["Phường 1", "Phường 2", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15"],
    "Quận 11": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15", "Phường 16"],
    "Quận 12": ["Phường Thạnh Xuân", "Phường Thạnh Lộc", "Phường Hiệp Thành", "Phường Thới An", "Phường Tân Chánh Hiệp", "Phường An Phú Đông", "Phường Tân Thới Hiệp", "Phường Trung Mỹ Tây", "Phường Tân Hưng Thuận", "Phường Đông Hưng Thuận", "Phường Tân Thới Nhất"],
    "Quận Bình Thạnh": ["Phường 1", "Phường 2", "Phường 3", "Phường 5", "Phường 6", "Phường 7", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15", "Phường 17", "Phường 19", "Phường 21", "Phường 22", "Phường 24", "Phường 25", "Phường 26", "Phường 27", "Phường 28"],
    "Quận Thủ Đức": ["Phường Linh Xuân", "Phường Bình Chiểu", "Phường Linh Trung", "Phường Tam Bình", "Phường Tam Phú", "Phường Hiệp Bình Phước", "Phường Hiệp Bình Chánh", "Phường Linh Chiểu", "Phường Linh Tây", "Phường Linh Đông", "Phường Bình Thọ", "Phường Trường Thọ"],
    "Quận Gò Vấp": ["Phường 1", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15", "Phường 16", "Phường 17"],
    "Quận Phú Nhuận": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 13", "Phường 15", "Phường 17"],
    "Quận Tân Bình": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15"],
    "Quận Tân Phú": ["Phường Tân Sơn Nhì", "Phường Tây Thạnh", "Phường Sơn Kỳ", "Phường Tân Quý", "Phường Tân Thành", "Phường Phú Thọ Hòa", "Phường Phú Thạnh", "Phường Phú Trung", "Phường Hòa Thạnh", "Phường Hiệp Tân", "Phường Tân Thới Hòa"],
    "Quận Bình Tân": ["Phường Bình Hưng Hòa", "Phường Bình Hưng Hòa A", "Phường Bình Hưng Hòa B", "Phường Bình Trị Đông", "Phường Bình Trị Đông A", "Phường Bình Trị Đông B", "Phường Tân Tạo", "Phường Tân Tạo A", "Phường An Lạc", "Phường An Lạc A"],
    "Huyện Bình Chánh": ["Thị trấn Tân Túc", "Xã Phạm Văn Hai", "Xã Vĩnh Lộc A", "Xã Vĩnh Lộc B", "Xã Bình Lợi", "Xã Lê Minh Xuân", "Xã Tân Nhựt", "Xã Tân Kiên", "Xã Bình Hưng", "Xã Phong Phú", "Xã An Phú Tây", "Xã Hưng Long", "Xã Đa Phước", "Xã Tân Quý Tây", "Xã Bình Chánh", "Xã Quy Đức"],
    "Huyện Củ Chi": ["Thị trấn Củ Chi", "Xã Phú Mỹ Hưng", "Xã An Phú", "Xã Trung Lập Thượng", "Xã An Nhơn Tây", "Xã Nhuận Đức", "Xã Phạm Văn Cội", "Xã Phú Hòa Đông", "Xã Trung Lập Hạ", "Xã Trung An", "Xã Phước Thạnh", "Xã Phước Hiệp", "Xã Tân An Hội", "Xã Phước Vĩnh An", "Xã Thái Mỹ", "Xã Tân Thạnh Tây", "Xã Hòa Phú", "Xã Tân Thạnh Đông", "Xã Bình Mỹ", "Xã Tân Phú Trung", "Xã Tân Thông Hội"],
    "Huyện Hóc Môn": ["Thị trấn Hóc Môn", "Xã Tân Hiệp", "Xã Nhị Bình", "Xã Đông Thạnh", "Xã Tân Thới Nhì", "Xã Thới Tam Thôn", "Xã Xuân Thới Sơn", "Xã Tân Xuân", "Xã Xuân Thới Đông", "Xã Trung Chánh", "Xã Xuân Thới Thượng", "Xã Bà Điểm"],
    "Huyện Nhà Bè": ["Thị trấn Nhà Bè", "Xã Phước Kiển", "Xã Phước Lộc", "Xã Nhơn Đức", "Xã Phú Xuân", "Xã Long Thới", "Xã Hiệp Phước"],
    "Huyện Cần Giờ": ["Thị trấn Cần Thạnh", "Xã Bình Khánh", "Xã Tam Thôn Hiệp", "Xã An Thới Đông", "Xã Thạnh An", "Xã Long Hòa", "Xã Lý Nhơn"]
};

// --- LOGIC XỬ LÝ ---
document.addEventListener('DOMContentLoaded', function() {
    const methodSelect = document.getElementById('methodSelect');
    const onlineInput = document.getElementById('onlineInputGroup');
    const offlineInput = document.getElementById('offlineInputGroup');
    const districtSelect = document.getElementById('districtSelect');
    const wardSelect = document.getElementById('wardSelect');
    const streetInput = document.getElementById('streetInput');
    const onlineLink = document.getElementById('onlineLink');
    const oldLocationElement = document.getElementById('oldLocation'); // Có thể null nếu là trang tạo mới
    const oldLocation = oldLocationElement ? oldLocationElement.value : '';

    // 1. Đổ dữ liệu Quận
    for (let district in hcmDataOld) {
        let option = document.createElement('option');
        option.value = district;
        option.text = district;
        districtSelect.appendChild(option);
    }

    // 2. Logic chọn Quận -> Phường
    districtSelect.addEventListener('change', function() {
        wardSelect.innerHTML = '<option value="">-- Chọn Phường --</option>';
        const selectedDistrict = this.value;
        if (selectedDistrict && hcmDataOld[selectedDistrict]) {
            hcmDataOld[selectedDistrict].forEach(function(ward) {
                let option = document.createElement('option');
                option.value = ward;
                option.text = ward;
                wardSelect.appendChild(option);
            });
            wardSelect.disabled = false;
        } else {
            wardSelect.disabled = true;
        }
    });

    // 3. Logic hiển thị lại dữ liệu cũ khi sửa (Chỉ chạy khi có dữ liệu cũ)
    function parseOldLocation(loc) {
        // Format: "Số nhà, Phường, Quận, TP. Hồ Chí Minh"
        const parts = loc.split(',').map(p => p.trim());
        if (parts.length >= 4) {
            const dist = parts[parts.length - 2];
            const wd = parts[parts.length - 3];
            
            // Tìm và set Quận
            for(let i=0; i<districtSelect.options.length; i++){
                if(districtSelect.options[i].value === dist){
                    districtSelect.selectedIndex = i;
                    districtSelect.dispatchEvent(new Event('change')); // Trigger để load phường
                    break;
                }
            }
            
            // Tìm và set Phường (delay chút để Quận load xong)
            setTimeout(() => {
                for(let i=0; i<wardSelect.options.length; i++){
                    if(wardSelect.options[i].value === wd){
                        wardSelect.selectedIndex = i;
                        break;
                    }
                }
            }, 100);

            // Phần còn lại là tên đường
            const streetPart = parts.slice(0, parts.length - 3).join(', ');
            streetInput.value = streetPart;
        }
    }

    // 4. Ẩn hiện theo hình thức dạy
    function toggleLocationInputs() {
        if (methodSelect.value === 'Online') {
            onlineInput.classList.remove('d-none');
            offlineInput.classList.add('d-none');
            // Nếu là Online và có dữ liệu cũ là link
            if (oldLocation && (oldLocation.includes('http') || oldLocation.includes('Teams') || oldLocation.includes('Zoom'))) {
                onlineLink.value = oldLocation;
            }
        } else {
            onlineInput.classList.add('d-none');
            offlineInput.classList.remove('d-none');
            
            // Nếu là Offline và có dữ liệu cũ, thử parse
            if (oldLocation && !oldLocation.includes('http')) {
                // Chỉ parse 1 lần đầu tiên nếu các ô đang trống
                if(districtSelect.value === "") {
                    parseOldLocation(oldLocation);
                }
            }
        }
    }
    
    if(methodSelect) {
        methodSelect.addEventListener('change', toggleLocationInputs);
        toggleLocationInputs(); // Chạy khi load trang
    }
});

// 5. Hàm chuẩn bị dữ liệu trước khi Submit (Được gọi bởi validateDates)
function prepareLocationData() {
    const method = document.getElementById('methodSelect').value;
    const finalLocationInput = document.getElementById('finalLocation');

    if (method === 'Online') {
        const link = document.getElementById('onlineLink').value.trim();
        if (!link) { alert("Vui lòng nhập link lớp học Online!"); return false; }
        finalLocationInput.value = link;
    } else {
        const street = document.getElementById('streetInput').value.trim();
        const ward = document.getElementById('wardSelect').value;
        const district = document.getElementById('districtSelect').value;

        if (!street || !ward || !district) {
            alert("Vui lòng nhập đầy đủ địa chỉ (Số nhà, Phường, Quận)!");
            return false;
        }
        // Gộp chuỗi
        finalLocationInput.value = `${street}, ${ward}, ${district}, TP. Hồ Chí Minh`;
    }
    return true;
}

// 6. Hàm kiểm tra ngày tháng
function validateDates() {
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const errorMsg = document.getElementById('dateError');

    // Nếu trang không có ô nhập ngày thì bỏ qua (dự phòng)
    if (!startDateInput || !endDateInput) return prepareLocationData();

    const startDate = new Date(startDateInput.value);
    const endDate = new Date(endDateInput.value);

    if (!startDateInput.value || !endDateInput.value) return true;

    const diffTime = endDate - startDate;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 

    if (diffDays < 7) {
        if(errorMsg) errorMsg.classList.remove('d-none');
        endDateInput.classList.add('is-invalid');
        prepareLocationData(); // Vẫn gọi để cập nhật location, nhưng trả về false để chặn submit
        return false; 
    } else {
        if(errorMsg) errorMsg.classList.add('d-none');
        endDateInput.classList.remove('is-invalid');
        return prepareLocationData();
    }
}