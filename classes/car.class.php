<?php
	class Car extends DB{
		
		// === SỬA LỖI BẢO MẬT (SQL INJECTION) VÀ TỐI ƯU ===
		public function get_car($id=""){
			if($id == ""){
				$sql = "SELECT * FROM cars";
				$stmt = $this->connect()->prepare($sql);
			} else {
				$sql = "SELECT * FROM cars WHERE id = ?";
				$stmt = $this->connect()->prepare($sql);
				$stmt->bind_param("i", $id);
			}
			$stmt->execute();
			return $stmt->get_result(); // Trả về kết quả
		}

		// === SỬA LỖI CÁC CÂU THÔNG BÁO SAI ===
		public function insert_car($POST, $file){
				$file_name = $file['name'];
				$file_tmp_name = $file['tmp_name'];
				$file_type = $file['type'];
				$file_size = $file['size'];
				$file_error = $file['error'];

				$file_ext = explode('.', $file_name);
				$file_name_raw = $file_ext[0];
				$file_actual_ext = strtolower(end($file_ext));

				$allowed = array('jpg', 'jpeg', 'png');

				if(in_array($file_actual_ext, $allowed)){
					if($file_error === 0){
						if($file_size < 5000000){ // 5MB
							$full_file_name = "Car".$file_name_raw."." . $file_actual_ext;
							$file_direction = "assets/Car_images/" . $full_file_name;
							move_uploaded_file($file_tmp_name, $file_direction);
							$POST['image'] = $full_file_name;
						} else {
							// SỬA LỖI: Thông báo đúng
							$_SESSION['message'] = "Kích thước ảnh quá lớn (tối đa 5MB)";
						}
					} else {
						// SỬA LỖI: Gõ chữ
						$_SESSION['message'] = "Lỗi khi tải ảnh lên";
					}
				} else {
					// SỬA LỖI: Thông báo đúng
					$_SESSION['message'] = "Định dạng ảnh không hợp lệ (chỉ .jpg, .jpeg, .png)";
				}
				
				// Chỉ chèn vào CSDL nếu $POST['image'] đã được gán (tức là upload thành công)
				if(isset($POST['image'])){
					unset($POST['add-car-submit']);
					$sql = "INSERT INTO `cars` SET";
					$i = 0;
					foreach ($POST as $key => $value) {
						if($i === 0){
							$sql = $sql . " `$key`=?";
						}else{
							$sql = $sql . ", `$key`=?";
						}
						$i++;
					}
					$sql = $sql . ';';
					$conn = $this->connect();
					$stmt = $conn->prepare($sql);
					$values = array_values($POST);
					$type = str_repeat('s', count($values));
					$stmt->bind_param($type, ...$values);
					$stmt->execute();
				}
		}

		/* ========================================
		 * SỬA HÀM UPDATE (YÊU CẦU CHÍNH)
		 * Đã sửa để chấp nhận file ảnh, xóa ảnh cũ và vá lỗi SQL Injection
		 * ======================================== */
		public function update_car($post, $file){
			$id = $post['id'];
			unset($post['id']);
			unset($post['update-car-submit']);

			$types = "";
			$params = [];

			// --- Xử lý file ảnh (nếu có file mới) ---
			$new_file_uploaded = (isset($file['name']) && $file['error'] == 0 && $file['size'] > 0);
			
			if ($new_file_uploaded) {
				// Chạy kiểm tra file mới
				$file_name = $file['name'];
				$file_tmp_name = $file['tmp_name'];
				$file_size = $file['size'];
				$file_ext = strtolower(end(explode('.', $file_name)));
				$allowed = array('jpg', 'jpeg', 'png');

				if(in_array($file_ext, $allowed) && $file_size < 5000000){
					// Lấy tên file ảnh cũ để xóa
					$old_car_data = $this->get_car($id)->fetch_assoc();
					$old_image_name = $old_car_data['image'];

					// Tạo tên file mới
					$file_name_raw = explode('.', $file_name)[0];
					$full_file_name = "Car".$file_name_raw."." . $file_ext;
					$file_direction = "assets/Car_images/" . $full_file_name;

					// Upload file mới
					if(move_uploaded_file($file_tmp_name, $file_direction)){
						// Xóa file cũ (nếu tồn tại)
						$old_image_path = "assets/Car_images/" . $old_image_name;
						if (file_exists($old_image_path) && !empty($old_image_name)) {
							unlink($old_image_path);
						}
						// Thêm 'image' vào $post để nó được update
						$post['image'] = $full_file_name;
					}
				} else {
					$_SESSION['message'] = "Ảnh mới không hợp lệ hoặc quá lớn (bỏ qua cập nhật ảnh)";
				}
			}

			// --- Xây dựng câu lệnh SQL (an toàn) ---
			$sql = "UPDATE `cars` SET";
			$i = 0;
			foreach ($post as $key => $value) {
				if($i === 0){
					$sql = $sql . " `$key` = ?";
				} else {
					$sql = $sql . ", `$key` = ?";
				}
				$params[] = $value; // Thêm giá trị vào mảng params
				$types .= "s"; // Giả sử tất cả đều là string
				$i++;
			}

			// Thêm ID vào câu lệnh WHERE (vá lỗi SQL Injection)
			$sql = $sql . " WHERE `id` = ?;";
			$params[] = $id; // Thêm ID vào mảng params
			$types .= "i"; // 'i' for integer

			$conn = $this->connect();
			$stmt = $conn->prepare($sql);
			$stmt->bind_param($types, ...$params); // Bind động
			$stmt->execute();
		}
		
		// === SỬA LỖI (KHÔNG XÓA ẢNH) VÀ BẢO MẬT (SQL INJECTION) ===
		public function delete_car($id){
			// 1. Lấy tên file ảnh trước khi xóa
			$car_data = $this->get_car($id)->fetch_assoc();
			if($car_data && !empty($car_data['image'])){
				$image_name = $car_data['image'];
				$image_path = "assets/Car_images/" . $image_name;
				
				// 2. Xóa file ảnh khỏi server
				if (file_exists($image_path)) {
					unlink($image_path);
				}
			}

			// 3. Xóa CSDL (dùng prepared statement)
			$conn = $this->connect();
			
			$sql1 = "DELETE FROM cars WHERE id = ?";
			$stmt1 = $conn->prepare($sql1);
			$stmt1->bind_param("i", $id);
			$stmt1->execute();

			$sql2 = "DELETE FROM cart WHERE product_id = ?";
			$stmt2 = $conn->prepare($sql2);
			$stmt2->bind_param("i", $id);
			$stmt2->execute();
		}
		
		// === SỬA LỖI BẢO MẬT (SQL INJECTION) ===
		public function search(){
			$con = $this->connect();
			if(isset($_POST['regular-search'])){
				$search = $_POST['regular-search'];
				// Thêm % cho truy vấn LIKE
				$search_param = "%" . $search . "%"; 
				
				$sql = "SELECT * FROM `cars` WHERE `manufacturer` LIKE ? OR `model` LIKE ?;";
				$stmt = $con->prepare($sql);
				$stmt->bind_param("ss", $search_param, $search_param);
				$stmt->execute();
				return $stmt->get_result();
			}
		}
	}
?>