<?php
	class Story extends DB{
		
		// === ĐÂY LÀ HÀM ĐÃ SỬA LỖI ===
		public function get_story($id=""){
			if($id == ""){
				// Khối IF (lấy tất cả): Không có bind_param
				$sql = "SELECT * FROM stories";
				$stmt = $this->connect()->prepare($sql);
			} else {
				// Khối ELSE (lấy 1): CÓ bind_param
				$sql = "SELECT * FROM stories WHERE id=?;";
				$stmt = $this->connect()->prepare($sql);
				$stmt->bind_param("i", $id); // Sửa 's' thành 'i' vì ID là số
			}
			$stmt->execute();
			return $stmt->get_result();
		}
		// ==============================
		
		public function add_story($title, $body, $file, $showing){
			$file_name = $file['name'];
			$file_tmp_name = $file['tmp_name'];
			$file_type = $file['type'];
			$file_size = $file['size'];
			$file_error = $file['error'];

			$file_ext = explode('.', $file_name);
			$file_name_raw = $file_ext[0];
			$file_actual_ext = strtolower(end($file_ext));

			$allowed = array('jpg');

			if(in_array($file_actual_ext, $allowed)){
				if($file_error === 0){
					if($file_size < 250000000){ // 250MB (rất lớn)
						$image_name = str_replace(" ", "_", $title);
						$full_file_name = "story".$image_name.".jpg" ;
						$file_direction = "assets/Story_images/" . $full_file_name;
						move_uploaded_file($file_tmp_name, $file_direction);
					} else {
						$_SESSION['message'] = "Kích thước ảnh quá lớn";
					}
				} else {
					$_SESSION['message'] = "Lỗi khi tải ảnh lên";
				}
			} else {
				$_SESSION['message'] = "Định dạng ảnh không hợp lệ (chỉ chấp nhận .jpg)";
			}

			$sql = "INSERT INTO `stories` SET `title`=?, `body`=?, `showing`=?;";
			$conn = $this->connect();
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('ssi', $title, $body, $showing); // showing là 1 hoặc 0 (integer)
			$stmt->execute();
		}

		public function update_story($post, $file){
			// 1. Lấy dữ liệu mới
			$id = $post['id'];
			$new_title = $post['title'];
			$new_body = $post['body'];

			// 2. Lấy dữ liệu cũ (đặc biệt là title cũ) để tìm file ảnh cũ
			$old_story_data = $this->get_story($id)->fetch_assoc();
			$old_title = $old_story_data['title'];

			// 3. Định nghĩa tên file ảnh cũ và mới dựa trên logic của dự án
			$old_image_name = "story" . str_replace(" ", "_", $old_title) . ".jpg";
			$old_image_path = "assets/Story_images/" . $old_image_name;

			$new_image_name = "story" . str_replace(" ", "_", $new_title) . ".jpg";
			$new_image_path = "assets/Story_images/" . $new_image_name;

			// 4. Cập nhật Title và Body trong CSDL (luôn luôn thực hiện)
			$sql = "UPDATE stories SET title = ?, body = ? WHERE id = ?";
			$stmt = $this->connect()->prepare($sql);
			$stmt->bind_param('ssi', $new_title, $new_body, $id);
			$stmt->execute();

			// 5. Xử lý file ảnh
			$new_file_uploaded = (isset($file['name']) && $file['error'] == 0 && $file['size'] > 0);

			if ($new_file_uploaded) {
				// TRƯỜNG HỢP 1: NGƯỜI DÙNG TẢI LÊN ẢNH MỚI
				$file_tmp_name = $file['tmp_name'];
				$file_ext = strtolower(end(explode('.', $file['name'])));
				$allowed = array('jpg');

				if(in_array($file_ext, $allowed) && $file['size'] < 250000000){
					if (file_exists($old_image_path) && $old_image_path != $new_image_path) {
						unlink($old_image_path);
					}
					move_uploaded_file($file_tmp_name, $new_image_path);
				} else {
					$_SESSION['message'] = "Ảnh mới không hợp lệ hoặc quá lớn";
				}
			
			} else if ($old_title != $new_title) {
				// TRƯỜNG HỢP 2: NGƯỜI DÙNG KHÔNG TẢI ẢNH, NHƯNG SỬA TITLE
				if (file_exists($old_image_path)) {
					rename($old_image_path, $new_image_path);
				}
			}
		}

		public function story_visibality($id){
			$con = $this->connect();
			$selected_stroy = $this->get_story($id);
			$row = $selected_stroy->fetch_assoc();
			if($row['showing'] == 1){
				$sql = "UPDATE `stories` SET `showing`=0 WHERE `id`=?;";
			} else {
				$sql = "UPDATE `stories` SET `showing`=1 WHERE `id`=?;";
			}
			$stmt = $con->prepare($sql);
			$stmt->bind_param("i", $id);
			$stmt->execute();
		}

		public function delete_story($id){
			// Xóa file ảnh trước khi xóa CSDL
			$story_data = $this->get_story($id)->fetch_assoc();
			if ($story_data) {
				$title = $story_data['title'];
				$image_name = "story" . str_replace(" ", "_", $title) . ".jpg";
				$image_path = "assets/Story_images/" . $image_name;
				
				if (file_exists($image_path)) {
					unlink($image_path);
				}
			}

			// Xóa CSDL
			$con = $this->connect();
			$sql = "DELETE FROM `stories` WHERE `id`=?;";
			$stmt = $con->prepare($sql);
			$stmt->bind_param("i", $id);
			$stmt->execute();
		}
	}
?>