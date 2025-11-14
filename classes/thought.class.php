<?php
	class Thought extends DB{
		public function get_thought($id=""){
			if($id == ""){
				$sql = "SELECT * FROM my_thoughts";
				$stmt = $this->connect()->prepare($sql);
			} else {
				$sql = "SELECT * FROM my_thoughts WHERE id = ?";
				$stmt = $this->connect()->prepare($sql);
				$stmt->bind_param("i", $id);
			}
			$stmt->execute();
			return $stmt->get_result();
		}

		public function insert_thought($POST, $file){
				$file_name = $file['name'];
				$file_tmp_name = $file['tmp_name'];
				$file_type = $file['type'];
				$file_size = $file['size'];
				$file_error = $file['error'];

				$file_ext = explode('.', $file_name);
				$file_name_raw = $file_ext[0]; // Tên file gốc
				$file_actual_ext = strtolower(end($file_ext));

				$allowed = array('jpg', 'jpeg', 'png');

				if(in_array($file_actual_ext, $allowed)){
					if($file_error === 0){
						if($file_size < 250000000){ // 250MB
							$full_file_name = "Thought".$file_name_raw."." . $file_actual_ext;
							$file_direction = "assets/Thought_images/" . $full_file_name;
							move_uploaded_file($file_tmp_name, $file_direction);
							$POST['image'] = $full_file_name;
						} else {
							// === SỬA LỖI: Thông báo sai ===
							$_SESSION['message'] = "Kích thước ảnh quá lớn"; // "Image size is too large"
						}
					} else {
						// === SỬA LỖI: Gõ chữ sai ===
						$_SESSION['message'] = "Lỗi khi tải ảnh lên"; // "Uploading image error"
					}
				} else {
					// === SỬA LỖI: Thông báo sai ===
					$_SESSION['message'] = "Định dạng ảnh không hợp lệ (chỉ .jpg, .jpeg, .png)"; // "Invalid image format"
				}
				
				// Chỉ chèn vào CSDL nếu $POST['image'] đã được gán (tức là upload thành công)
				if(isset($POST['image'])){
					unset($POST['add-thoughts-submit']);
					$sql = "INSERT INTO `my_thoughts` SET";
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
		 * HÀM MỚI (UPDATE) ĐƯỢC THÊM VÀO
		 * ======================================== */
		public function update_thought($post, $file){
			// 1. Lấy dữ liệu
			$id = $post['id'];
			$title = $post['title'];
			$tag = $post['tag'];
			$body = $post['body'];

			$sql = "UPDATE my_thoughts SET title = ?, tag = ?, body = ?";
			$types = "sssi"; // s = string, i = integer
			$params = [$title, $tag, $body];

			// 2. Xử lý ảnh (nếu có file mới)
			$new_file_uploaded = (isset($file['name']) && $file['error'] == 0 && $file['size'] > 0);
			
			if($new_file_uploaded){
				// Lấy tên file ảnh cũ để xóa
				$old_thought_data = $this->get_thought($id)->fetch_assoc();
				$old_image_name = $old_thought_data['image'];

				// Chạy kiểm tra file mới
				$file_name = $file['name'];
				$file_tmp_name = $file['tmp_name'];
				$file_size = $file['size'];
				$file_ext = strtolower(end(explode('.', $file_name)));
				$allowed = array('jpg', 'jpeg', 'png');

				if(in_array($file_ext, $allowed) && $file_size < 250000000){
					// Tạo tên file mới
					$file_name_raw = explode('.', $file_name)[0];
					$full_file_name = "Thought".$file_name_raw."." . $file_ext;
					$file_direction = "assets/Thought_images/" . $full_file_name;

					// Upload file mới
					if(move_uploaded_file($file_tmp_name, $file_direction)){
						// Xóa file cũ (nếu tồn tại)
						$old_image_path = "assets/Thought_images/" . $old_image_name;
						if (file_exists($old_image_path)) {
							unlink($old_image_path);
						}

						// Thêm ảnh mới vào câu lệnh SQL
						$sql .= ", image = ?";
						$types .= "s";
						$params[] = $full_file_name;
					}
				} else {
					$_SESSION['message'] = "Ảnh mới không hợp lệ hoặc quá lớn";
				}
			}

			// 3. Hoàn tất câu lệnh SQL và thực thi
			$sql .= " WHERE id = ?";
			$params[] = $id;

			$stmt = $this->connect()->prepare($sql);
			$stmt->bind_param($types, ...$params);
			$stmt->execute();
		}

		// === SỬA LỖI: Viết lại hàm Delete cho bảo mật và xóa file ===
		public function delete_thought($id){
			// 1. Lấy tên file ảnh trước khi xóa
			$thought_data = $this->get_thought($id)->fetch_assoc();
			if($thought_data && !empty($thought_data['image'])){
				$image_name = $thought_data['image'];
				$image_path = "assets/Thought_images/" . $image_name;

				// 2. Xóa file ảnh khỏi server
				if (file_exists($image_path)) {
					unlink($image_path);
				}
			}

			// 3. Xóa CSDL (dùng prepared statement)
			$sql = "DELETE FROM my_thoughts WHERE id = ?;";
			$stmt = $this->connect()->prepare($sql);
			$stmt->bind_param("i", $id);
			$stmt->execute();
		}
	}
?>