<?php
    session_start();
    include 'includes/more.inc.php';
    include 'includes/search.inc.php';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.7.2/css/all.min.css" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/more.css">
		<title>NC CARS | Xem thêm</title>
	</head>
	<body>
		<div class="nav">
			<a href="javascript:history.back();"><i class="fas fa-angle-double-left"></i></a><h1>NC CARS</h1>
		</div>

        <h1 class="headling">Xem thêm</h1>
		<div class="container">
			<div class="results">
                <?php
                if(isset($_GET['type'])){
                    // Hiển thị tiêu đề
                    if ($_GET['type'] == 'all_cars') {
                        echo '<h3>Tất cả xe</h3>';
                    } else {
                        // $type được lấy từ file 'more.inc.php'
                        echo '<h3>'.$type.'</h3>';
                    }
                    
                    // Vòng lặp
                    while($row = $cars->fetch_assoc()){
                        // Hiển thị xe theo loại (Regular, Sports, Off road)
                        if($row['car_type'] == $type){
                            echo '<div class="item">
                                <img src="assets/Car_images/'.$row['image'].'" height="150px" width="195px;">
                                <h2>'.$row['manufacturer'].'</h2>
                                <p>'.$row['model'].'</p>
                                <p>'.number_format($row['price']).' VNĐ</p>
                                <form class="" action="product.php?id='.$row['id'].'" method="post">
                                    <button type="submit" name="add-to-cart-index"> Thêm vào giỏ</button>
                                </form>
                            </div>';
                        }
                        
                        // Hiển thị tất cả xe (Đã sửa lỗi logic từ 'all' thành 'all_cars')
                        if($type == 'all_cars'){
                            echo '<div class="item">
                                <img src="assets/Car_images/'.$row['image'].'" height="150px" width="195px;">
                                <h2>'.$row['manufacturer'].'</h2>
                                <p>'.$row['model'].'</p>
                                <p>'.number_format($row['price']).' VNĐ</p>
                                <form class="" action="product.php?id='.$row['id'].'" method="post">
                                    <button type="submit" name="add-to-cart-index"> Thêm vào giỏ</button>
                                </form>
                            </div>';
                        }
                    }
                } else {
                    // Hiển thị các bài viết (post)
                    foreach($thoughts as $row){
                        echo '
                        <div class="post">
                            <div class="post-mini1"  style="background-image:url(assets/Thought_images/'.$row['image'].');
                                    background-size: cover;
                                    background-position:center;">
                            </div>
                            <div class="post-mini2">
                                <h2>'.$row['title'].'</h2>
                                <i class="far fa-user"> <p style="display: inline;"> NC CARS</p></i>
                                <p style="display: inline; padding:0px 5px 0px 5px;"> | </p>
                                <i class="far fa-calendar-alt"> <p style="display:inline;"> '.$row['create_time'].'</p></i>
                                <p style="display: inline; padding:0px 5px 0px 5px;"> | </p>
                                <i class="fas fa-paperclip" style="display:inline;"><p style="display:inline;"> '.$row['tag'].'  </p></i>
                                <br><br>
                                <p>'.substr($row['body'], 0, min(strlen($row['body']), 300)).'</p>
                                <br>
                            </div>
                            <div class=post-mini3>
                                <a href="post.php?id='.$row['id'].'"> Đọc thêm </a>
                            </div>
                        </div>';
                    }
                }
                ?>
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