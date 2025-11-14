<?php
class Product extends DB{
	
	// === SỬA LỖI BẢO MẬT (SQL INJECTION) VÀ TỐI ƯU ===
	public function get_product($id=""){
		if($id == ""){
			$sql = "SELECT * FROM products";
			$stmt = $this->connect()->prepare($sql);
		} else {
			$sql = "SELECT * FROM products WHERE id = ?";
			$stmt = $this->connect()->prepare($sql);
			$stmt->bind_param("i", $id);
		}
		$stmt->execute();
		return $stmt->get_result(); // Trả về kết quả
	}

	// === SỬA LỖI CÁC CÂU THÔNG BÁO SAI ===
	public function insert_product($POST, $file){
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
					if($file_size < 250000000){ // 250MB
						$full_file_name = "Product".$file_name_raw."." . $file_actual_ext;
						$file_direction = "assets/Product_images/" . $full_file_name;
						move_uploaded_file($file_tmp_name, $file_direction);
						$POST['image'] = $full_file_name;
					} else {
						// SỬA LỖI: Thông báo đúng
						$_SESSION['message'] = "Kích thước ảnh quá lớn";
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
				unset($POST['add-product-submit']);
				$sql = "INSERT INTO `products` SET";
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
	public function update_product($post, $file){
		$id = $post['id'];
		unset($post['id']);
		unset($post['update-product-submit']);

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

			if(in_array($file_ext, $allowed) && $file_size < 250000000){
				// Lấy tên file ảnh cũ để xóa
				$old_data = $this->get_product($id)->fetch_assoc();
				$old_image_name = $old_data['image'];

				// Tạo tên file mới
				$file_name_raw = explode('.', $file_name)[0];
				$full_file_name = "Product".$file_name_raw."." . $file_ext;
				$file_direction = "assets/Product_images/" . $full_file_name;

				// Upload file mới
				if(move_uploaded_file($file_tmp_name, $file_direction)){
					// Xóa file cũ (nếu tồn tại)
					$old_image_path = "assets/Product_images/" . $old_image_name;
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
		$sql = "UPDATE `products` SET";
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
	public function delete_product($id){
		// 1. Lấy tên file ảnh trước khi xóa
		$product_data = $this->get_product($id)->fetch_assoc();
		if($product_data && !empty($product_data['image'])){
			$image_name = $product_data['image'];
			$image_path = "assets/Product_images/" . $image_name;
			
			// 2. Xóa file ảnh khỏi server
			if (file_exists($image_path)) {
				unlink($image_path);
			}
		}

		// 3. Xóa CSDL (dùng prepared statement)
		$conn = $this->connect();
		
		$sql1 = "DELETE FROM products WHERE id = ?";
		$stmt1 = $conn->prepare($sql1);
		$stmt1->bind_param("i", $id);
		$stmt1->execute();

		// Cẩn thận: product_id_2 là cột trong bảng giỏ hàng (cart)
		$sql2 = "DELETE FROM cart WHERE product_id_2 = ?";
		$stmt2 = $conn->prepare($sql2);
		$stmt2->bind_param("i", $id);
		$stmt2->execute();
	}
}
?>