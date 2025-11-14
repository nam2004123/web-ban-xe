<?php
    session_start();

	// === CODE MỚI THÊM VÀO ĐỂ CHẶN ADMIN ===
    if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
        // Nếu là Admin, đá về trang admin
        header("Location: admin.php");
        exit();
    }
    // ======================================
    include 'includes/order.inc.php';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.7.2/css/all.min.css" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/checkout.css">
		<title>NC CARS | Thanh toán</title>
	</head>
	<body>
		<div class="all">
			<div class="nav">
				<a href="cart.php"><i class="fas fa-angle-double-left"></i></a><h1>NC CARS</h1>
			</div>
			<div class="container">
				<h2> Thanh toán </h2>
				<div class="checkout-info">
					<div id="checkout-flex" class="checkout-form">
						<h3>Thông tin khách hàng</h3>
						<form action="checkout.php" method="post">
							<input type="hidden" name="user_id" value="<?php echo $_SESSION['id']; ?>"><br>
							<input type="text" name="address" placeholder="Địa chỉ..."><br>
							<input type="text" name="city" placeholder="Thành phố..."><br>
							<input type="text" name="phone" placeholder="Số điện thoại..."><br>
							<input type="text" name="postal_code" placeholder="Mã bưu điện..."><br>
							<button type="submit" name="order-submit" class="order-submit-btn"> Đặt hàng</button><br>
						</form>
					</div>
					<div id="checkout-flex" class="checkout-details">
						<h3 style="margin-bottom: 35px;">Thông tin đơn hàng</h3>
						<h4>Tổng cộng: <?php echo number_format($total); ?> VNĐ</h4>
						<h4>Tổng số mặt hàng: <?php echo $count; ?></h4>
					</div>
				</div>
			</div>
		</div>
		<footer>
			<div class="social">
			  <h2>THEO DÕI CHÚNG TÔI</h2>
			  <a href="#"> <i class="fab fa-facebook"> <span></span> </i> </a>
			  <a href="#"> <i class="fab fa-instagram"> <span></span> </i> </a>
			  <a href="#"> <i class="fab fa-twitter"> <span></span> </i> </a>
			  <a href="#"> <i class="fab fa-youtube"> <span></span> </i> </a>
            </div>
            <div class="credit">
		 	    <h1>NC CARS | Phát triển bởi NC CARS</h1>
            </div>
		</footer>
	</body>
</html>