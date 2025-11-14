<?php
	include 'includes/autoloader.inc.php';

	$cart = new Cart();
	$order = new Order();
	// Reset con trỏ nếu $carts đã được dùng ở đâu đó (để an toàn)
	if (isset($_SESSION['id'])) {
		$carts = $cart->get_cart($_SESSION['id']);
		if ($carts->num_rows > 0) {
			$carts->data_seek(0);
		}
	} else {
		// Xử lý nếu không có session id (ví dụ: chuyển về trang chủ)
		header("Location: index.php");
		exit();
	}

	$count = 0;
	$total = 0;
	$result = "";
	while($row = $carts->fetch_assoc()){
		$total += $row['product_price'];
		$count++;
	}

	if(isset($_POST['order-submit'])){
		$order->place_order();
		$order->cart_to_order($_POST['user_id']);
		$order->clear_cart($_POST['user_id']);
		$_SESSION['cart'] = 0;
		unset($_POST);
		$count = 0;
		$total = 0;

		/* ======================================== */
		/* === CODE SỬA LỖI (THÊM VÀO) === */
		/* ======================================== */

		// 1. Tạo thông báo thành công
		// (Chúng ta dùng 'sign_message' vì index.php đã có code để hiển thị nó)
		$_SESSION['sign_message'] = "Bạn đã đặt hàng thành công!";
		
		// 2. Chuyển hướng người dùng về trang chủ
		header("Location: index.php");
		
		// 3. Luôn dùng exit() sau khi chuyển hướng
		exit();
	}
?>