<?php
    session_start();
	include 'includes/admin.inc.php';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.7.2/css/all.min.css" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/admin.css">
		<title>NC CARS | Quản trị</title>
	</head>
	<body>
		<header>
			<div class="heading">
				<h1><a href="index.php"><i class="fas fa-angle-double-left"></i></a>NC CARS <span> Quản trị </span> </h1>
			</div>
			<nav>
				<h3>ĐƠN HÀNG</h3>
				<h3>NGƯỜI DÙNG</h3>
				<h3>CÂU CHUYỆN</h3>
				<h3>BÀI VIẾT</h3>
				<h3>XE CỘ</h3>
				<h3>THƯ VIỆN</h3>
                <h3>SẢN PHẨM</h3>
			</nav>
		</header>
		<?php
			if(isset($_SESSION['message'])){
                if($_SESSION['message'] == "Đã thêm xe thành công" || $_SESSION['message'] == "Đã thêm sản phẩm thành công"){
                    echo '<h6 class="success">' . $_SESSION["message"] . '</h6>';
                } else {
                    echo '<h6 class="error">' . $_SESSION["message"] . '</h6>';
                }
                unset($_SESSION['message']);
			}
		?>
		<div id='tab' class="orders">
            <div class="container">
				<div class="orders-flex-box">
					<div class="orders-table">
						<h1>Đơn hàng hiện tại</h1>
						<table>
							<thead>
								<th>id</th>
								<th>user id</th>
                                <th>Chi tiết đơn</th>
								<th>Xóa</th>
							</thead>
							<tbody>
								<?php
                                $i=1;
                                while($row = $orders->fetch_assoc())
                                {
                                    echo '<tr>
    									<td>'.$i.'</td>
    									<td>'.$row["user_id"].'</td>
    									<td class="user-btn">
    											<a href="orders.php?order_id='.$row["id"].'"><i class="fas fa-address-card"></i>
    									</td>
    									<td class="delete-btn">
    										<form action="admin.php?del_id='.$row['id'].'" method="post">
                                                <button type="submit" name="delete-submit-cart" style="cursor:pointer; border:none; background: transparent; color: #f54c4c; width:100%;"> <i class="fas fa-trash-alt"></i> </button>
    										</form>
    									</td>
    								</tr>';
                                    $i++;
                                }
								?>
							</tbody>
						</table>
                    </div>
                </div>
            </div>
		</div>
		<div id='tab' class="users">
            <div class="container">
				<div class="users-flex-box">
					<div class="users-table">
						<h1>Người dùng</h1>
						<table>
							<thead>
								<th>id</th>
								<th>Tên</th>
								<th>Email</th>
								<th>Quản trị</th>
								<th>Xóa</th>
							</thead>
							<tbody>
								<?php
                                $i=1;
                                while($row = $users->fetch_assoc())
                                {
                                    echo '<tr>
    									<td>'.$i.'</td>
    									<td>'.$row["username"].'</td>
    									<td>'.$row["email"].'</td>';
                                        if($row['admin'] == 0){
                                            echo '<td style="text-align: center; color: #db3737;"><i class="fas fa-times"></i></td>';
                                        }else{
                                            echo '<td style="text-align: center; color: #0ac910;"><i class="fas fa-check"></i></td>';
                                        }
    									echo '<td class="delete-btn">
    										<form action="admin.php?del_user='.$row['id'].'" method="post">
                                                <button type="submit" name="delete-submit-user" style="cursor:pointer; border:none; background: transparent; color: #f54c4c; width:100%;"> <i class="fas fa-trash-alt"></i> </button>
    										</form>
    									</td>
                                        <td style="display: none;">'.$row['id'].'</td>
    								</tr>';
                                    $i++;
                                }
								?>
							</tbody>
						</table>
                    </div>
                </div>
            </div>
		</div>
		<div id='tab' class="stories">
            <div class="container">
				<div class="stories-flex-box">
					<div class="stories-table">
						<h1>Quản lý câu chuyện</h1>
						<table>
							<thead>
								<th>id</th>
								<th>Tiêu đề</th>
                                <th>Hiển thị</th>
                                <th>Sửa</th>
								<th>Xóa</th>
							</thead>
							<tbody>
								<?php
                                $i=1;
                                if ($stories->num_rows > 0) {
                                    $stories->data_seek(0);
                                }
                                while($row = $stories->fetch_assoc())
                                {
                                    echo '<tr id="story_row_'.$row['id'].'" data-title="'.htmlspecialchars($row['title']).'" data-body="'.htmlspecialchars($row['body']).'">
                                            <td>'.$i.'</td>
                                            <td>'.$row['title'].'</td>
                                            <td id="show-hide-table">';
                                        if($row['showing'] == 1){
                                            echo '<form action="admin.php?story_show='.$row['id'].'" method="post">
                                                    <button class="story-btn" type="submit" name="story-show-submit"><i style="text-align: center; color: #0ac910;" class="fas fa-check"></i></button>
                                                </form>';
                                        } else {
                                            echo '<form action="admin.php?story_show='.$row['id'].'" method="post" >
                                                    <button class="story-btn" type="submit" name="story-show-submit"><i style="text-align: center; color: #db3737;" class="fas fa-times"></i></button>
                                                </form>';
                                        }
                                        echo '</td>
                                            <td class="edit-btn" onclick="update_story('.$row['id'].')">
                                                <i class="fas fa-edit"></i>
                                            </td>
                                            <td>
                                                <form action="admin.php?delete_story='.$row['id'].'" method="post">
                                                    <button class="story-delete-btn" type="submit" name="story-delete-submit"><i style="text-align: center; color: #db3737;" class="fas fa-trash-alt"></i></button>
                                                </form>
                                            </td>
                                        </tr>';
                                    $i++;
                                }
								?>
							</tbody>
						</table>
					</div>
                    <div class="edit">
						<h1>Thêm mới</h1>
						<div class="add">
							<form action="admin.php" method="post" enctype="multipart/form-data">
								<p>Tiêu đề</p>
								<input type="text" name="title">
								<p>Nội dung</p>
								<textarea name="body"></textarea>
								<p class="mini">Hình ảnh</p>
								<input type="file" name="story-image">
								<input class="add-story-submit" type="submit" name="add-story-submit" value="Thêm">
							</form>
						</div>
					</div>

                    <div class="update-stories">
						<h1>Cập nhật Câu chuyện</h1>
						<div class="add">
							<form action="admin.php" method="post" enctype="multipart/form-data">
                                <input class="id_holder" type="hidden" name="id" value="">
								<p>Tiêu đề</p>
								<input type="text" name="title">
								<p>Nội dung</p>
								<textarea name="body"></textarea>
								<p class="mini">Ảnh mới (Để trống nếu không đổi)</p>
								<input type="file" name="story-image">
								<input class="add-story-submit" type="submit" name="update-story-submit" value="Cập nhật">
                                <div class="add-car-decline" onclick="decline_story()"> Hủy </div>
							</form>
						</div>
					</div>
                </div>
            </div>
		</div>
		<div id='tab' class="my-thoughts">
            <div class="container">
				<div class="thoughts-flex-box">
					<div class="thoughts-table">
						<h1>Bài viết</h1>
						<table>
							<thead>
								<th>id</th>
								<th>Tiêu đề</th>
                                <th>Thời gian</th>
                                <th>Sửa</th>
								<th>Xóa</th>
							</thead>
							<tbody>
								<?php
                                $i=1;
                                if ($thoughts->num_rows > 0) {
                                    $thoughts->data_seek(0);
                                }
                                while($row = $thoughts->fetch_assoc())
                                {
                                    echo '<tr id="thought_row_'.$row['id'].'" data-title="'.htmlspecialchars($row['title']).'" data-tag="'.htmlspecialchars($row['tag']).'" data-body="'.htmlspecialchars($row['body']).'">
                                            <td>'.$i.'</td>
                                            <td>'.$row['title'].'</td>
                                            <td>'.$row['create_time'].'</td>
                                            <td class="edit-btn" onclick="update_thought('.$row['id'].')">
                                                <i class="fas fa-edit"></i>
                                            </td>
                                            <td>
                                                <form action="admin.php?delete_thought='.$row['id'].'" method="post">
                                                    <button class="thoughts-delete-btn" type="submit" name="thoughts-delete-submit"><i style="text-align: center; color: #db3737;" class="fas fa-trash-alt"></i></button>
                                                </form>
                                            </td>
                                        </tr>';
                                    $i++;
                                }
								?>
							</tbody>
						</table>
					</div>
                    <div class="edit-thoughts">
						<h1>Thêm mới</h1>
						<div class="add-thoughts">
							<form action="admin.php" method="post" enctype="multipart/form-data">
								<p>Tiêu đề</p>
								<input type="text" name="title">
								<p>Thẻ</p>
								<input type="text" name="tag">
                                <p>Nội dung</p>
								<textarea name="body"></textarea>
								<p class="mini">Hình ảnh</p>
								<input type="file" name="image">
								<input class="add-thoughts-submit" type="submit" name="add-thoughts-submit" value="Thêm">
							</form>
						</div>
					</div>
                    
                    <div class="update-thoughts">
						<h1>Cập nhật Bài viết</h1>
						<div class="add-thoughts">
							<form action="admin.php" method="post" enctype="multipart/form-data">
                                <input class="id_holder" type="hidden" name="id" value="">
								<p>Tiêu đề</p>
								<input type="text" name="title">
								<p>Thẻ</p>
								<input type="text" name="tag">
                                <p>Nội dung</p>
								<textarea name="body"></textarea>
								<p class="mini">Ảnh mới (Để trống nếu không đổi)</p>
								<input type="file" name="image">
								<input class="add-thoughts-submit" type="submit" name="update-thought-submit" value="Cập nhật">
                                <div class="add-car-decline" onclick="decline_thought()"> Hủy </div>
							</form>
						</div>
					</div>
                </div>
            </div>
		</div>
		</div>
		<div id='tab' class="cars">
			<div class="container">
				<div class="flex-box">
					<div class="cars-table">
						<h1>Xe đang bán</h1>
						<table>
							<thead>
								<th>id</th>
								<th>Hãng sản xuất</th>
								<th>Mẫu xe</th>
								<th>Giá</th>
								<th>Loại xe</th>
								<th>Sửa</th>
								<th>Xóa</th>
							</thead>
							<tbody>
								<?php
                                $i=1;
                                if ($cars->num_rows > 0) {
                                    $cars->data_seek(0);
                                }
                                while($row = $cars->fetch_assoc())
                                {
                                    echo '<tr id='.$row['id'].'>
    									<td>'.$i.'</td>
    									<td>'.$row["manufacturer"].'</td>
    									<td>'.$row["model"].'</td>
                                        <td style="display:none;">'.$row["condition"].'</td>
                                        <td style="display:none;">'.$row["phone"].'</td>
                                        <td style="display:none;">'.$row["email"].'</td>
    									<td>'.number_format($row["price"]).' VNĐ</td>
                                        <td style="display:none;">'.$row["battery"].'</td>
                                        <td style="display:none;">'.$row["fuel"].'</td>
                                        <td style="display:none;">'.$row["total_run"].'</td>
                                        <td style="display:none;">'.$row["gear"].'</td>
                                        <td>'.$row["car_type"].' </td>
    									<td class="edit-btn" onclick="update('.$row['id'].')">
    										<form method="post">
    											<i class="fas fa-edit"></i>
    										</form>
    									</td>
    									<td class="delete-btn">
    										<form action="admin.php?id='.$row['id'].'" method="post">
                                                <button type="submit" name="delete-submit" style="cursor:pointer; border:none; background: transparent; color: #f54c4c; width:100%;"> <i class="fas fa-trash-alt"></i> </button>
    										</form>
    									</td>
                                        <td style="display: none;">'.$row['id'].'</td>
    								</tr>';
                                    $i++;
                                }
								?>
							</tbody>
						</table>
					</div>
					<div class="edit-cars">
						<h1>Thêm mới</h1>
						<div class="add">
							<form action="admin.php" method="post" enctype="multipart/form-data">
								<p>Hãng sản xuất</p>
								<input type="text" name="manufacturer">
								<p>Mẫu xe</p>
								<input type="text" name="model">
								<p>Tình trạng</p>
								<textarea name="condition"></textarea>
								<p>Điện thoại</p>
								<input type="text" name="phone">
								<p>Email</p>
								<input type="text" name="email">

								<div class="add-mini">
									<div class="add-mini-1">
										<p class="mini">Giá</p>
										<input class="mini" type="text" name="price">
										<p class="mini">Tốc độ</p>
										<input class="mini" type="text" name="speed">
										<p class="mini">Tiêu hao (km/l)</p>
										<input class="mini" type="text" name="mileage">
										<p class="mini">Ắc quy</p>
										<input class="mini" type="text" name="battery">
										<p class="mini">Nhiên liệu</p>
										<input class="mini" type="text" name="fuel">
									</div>
									<div class="add-mini-2">
										<p class="mini">Số KM đã đi</p>
										<input class="mini" type="text" name="total_run">
										<p class="mini">Hộp số</p>
										<input class="mini" type="text" name="gear">
										<p class="mini">Loại xe</p>
										<input class="mini" type="text" name="car_type">
										<p class="mini">Hình ảnh</p>
										<input type="file" name="image">
										<input class="add-car-submit" type="submit" name="add-car-submit" value="Thêm">
									</div>
								</div>
							</form>
						</div>
					</div>

                    <div class="update-cars">
						<h1>Cập nhật</h1>
						<div class="add">
							<form action="admin.php" method="post" enctype="multipart/form-data">
                                <input class="id_holder" type="hidden" name="id" value="">
                                
                                <p class="mini">Ảnh mới (Để trống nếu không đổi)</p>
                                <input type="file" name="image">

								<p>Tình trạng</p>
								<textarea name="condition"></textarea>
								<p>Điện thoại</p>
								<input type="text" name="phone">
								<p>Email</p>
								<input type="text" name="email">

								<div class="update-mini">
									<div class="update-mini-1">
										<p class="mini">Giá</p>
										<input class="mini" type="text" name="price">
										<p class="mini">Ắc quy</p>
										<input class="mini" type="text" name="battery">
										<p class="mini">Nhiên liệu</p>
										<input class="mini" type="text" name="fuel">
										<p class="mini">Số KM đã đi</p>
										<input class="mini" type="text" name="total_run">
										<p class="mini">Hộp số</p>
										<input class="mini" type="text" name="gear">
										<input class="add-car-submit" type="submit" name="update-car-submit" value="Cập nhật">
                                        <div class="add-car-decline" onclick="decline()"> Hủy </div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id='tab' class="gallary">
            <section class="gallary">
    			<h1>THƯ VIỆN CỦA TÔI</h1>
    			<div class="gallary-img">
    				<div class="row">
    				  <div class="column">
    					  <div class="overlay">
								<p>Ảnh hiện tại (Slot 1):</p>
								<img src="assets/Gallary_images/<?php echo $gallery_row['image1']; ?>" class="admin-gallery-thumb">
								<hr>
								<p>Cập nhật ảnh mới:</p>
                                <form action="admin.php" method="post"  enctype="multipart/form-data">
                                    <input type="file" name="image">
                                    <button type="submit" name="image1"> Cập nhật</button>
                                </form>
    					  </div>
    					  <div class="overlay">
								<p>Ảnh hiện tại (Slot 2):</p>
								<img src="assets/Gallary_images/<?php echo $gallery_row['image2']; ?>" class="admin-gallery-thumb">
								<hr>
								<p>Cập nhật ảnh mới:</p>
                                <form action="admin.php" method="post" enctype="multipart/form-data">
                                    <input type="file" name="image">
                                    <button type="submit" name="image2"> Cập nhật</button>
                                </form>
    					 </div>
    				  </div>
    				  <div class="column">
    					  <div class="overlay-port"> <p>Ảnh hiện tại (Slot 3):</p>
								<img src="assets/Gallary_images/<?php echo $gallery_row['image3']; ?>" class="admin-gallery-thumb-port"> <hr>
								<p>Cập nhật ảnh mới:</p>
                                <form action="admin.php" method="post"  enctype="multipart/form-data">
                                    <input type="file" name="image">
                                    <button type="submit" name="image3"> Cập nhật</button>
                                </form>
    					 </div>
    				  </div>
    				  <div class="column">
    					  <div class="overlay">
								<p>Ảnh hiện tại (Slot 4):</p>
								<img src="assets/Gallary_images/<?php echo $gallery_row['image4']; ?>" class="admin-gallery-thumb">
								<hr>
								<p>Cập nhật ảnh mới:</p>
                                <form action="admin.php" method="post"  enctype="multipart/form-data">
                                    <input type="file" name="image">
                                    <button type="submit" name="image4"> Cập nhật</button>
                                </form>
    					 </div>
    					 <div class="overlay">
								<p>Ảnh hiện tại (Slot 5):</p>
								<img src="assets/Gallary_images/<?php echo $gallery_row['image5']; ?>" class="admin-gallery-thumb">
								<hr>
								<p>Cập nhật ảnh mới:</p>
                               <form action="admin.php" method="post"  enctype="multipart/form-data">
                                   <input type="file" name="image">
                                   <button type="submit" name="image5"> Cập nhật</button>
                               </form>
    					 </div>
    				  </div>
    				  <div class="column">
    					  <div class="overlay">
								<p>Ảnh hiện tại (Slot 6):</p>
								<img src="assets/Gallary_images/<?php echo $gallery_row['image6']; ?>" class="admin-gallery-thumb">
								<hr>
								<p>Cập nhật ảnh mới:</p>
                                <form action="admin.php" method="post"  enctype="multipart/form-data">
                                    <input type="file" name="image">
                                    <button type="submit" name="image6"> Cập nhật</button>
                                </form>
    					 </div>
    					 <div class="overlay">
								<p>Ảnh hiện tại (Slot 7):</p>
								<img src="assets/Gallary_images/<?php echo $gallery_row['image7']; ?>" class="admin-gallery-thumb">
								<hr>
								<p>Cập nhật ảnh mới:</p>
                               <form action="admin.php" method="post"  enctype="multipart/form-data">
                                   <input type="file" name="image">
                                   <button type="submit" name="image7"> Cập nhật</button>
                               </form>
    					 </div>
    				  </div>
    				</div>
    			</div>
    		</section>
		</div>

        <div id='tab' class="products">
			<div class="container">
				<div class="flex-box">
					<div class="product-table">
						<h1>Sản phẩm đang bán</h1>
						<table>
							<thead>
								<th>id</th>
								<th>Hãng sản xuất</th>
								<th>Mẫu</th>
								<th>Giá</th>
								<th>Loại</th>
								<th>Sửa</th>
								<th>Xóa</th>
							</thead>
							<tbody>
								<?php
                                $i=1;
                                if ($products->num_rows > 0) {
                                    $products->data_seek(0);
                                }
                                while($row = $products->fetch_assoc())
                                {
                                    echo '<tr id='.$row['id'].'>
    									<td>'.$i.'</td>
    									<td>'.$row["manufacturer"].'</td>
    									<td>'.$row["model"].'</td>
                                        <td style="display:none;">'.$row["condition"].'</td>
                                        <td style="display:none;">'.$row["phone"].'</td>
                                        <td style="display:none;">'.$row["email"].'</td>
    									<td>'.number_format($row["price"]).' VNĐ</td>
                                        <td>'.$row["type"].' </td>
    									<td class="edit-btn" onclick="update_product('.$row['id'].')">
    										<form method="post">
    											<i class="fas fa-edit"></i>
    										</form>
    									</td>
    									<td class="delete-btn">
    										<form action="admin.php?product_del_id='.$row['id'].'" method="post">
                                                <button type="submit" name="delete-submit" style="cursor:pointer; border:none; background: transparent; color: #f54c4c; width:100%;"> <i class="fas fa-trash-alt"></i> </button>
    										</form>
    									</td>
                                        <td style="display: none;">'.$row['id'].'</td>
    								</tr>';
                                    $i++;
                                }
								?>
							</tbody>
						</table>
					</div>
					<div class="edit-products">
						<h1>Thêm mới</h1>
						<div class="add">
							<form action="admin.php" method="post" enctype="multipart/form-data">
								<p>Hãng sản xuất</p>
								<input type="text" name="manufacturer">
								<p>Mẫu</p>
								<input type="text" name="model">
								<p>Tình trạng</p>
								<textarea name="condition"></textarea>
								<p>Điện thoại</p>
								<input type="text" name="phone">
								<p>Email</p>
								<input type="text" name="email">

								<div class="add-mini">
									<div class="add-mini-1">
										<p class="mini">Giá</p>
										<input class="mini" type="text" name="price">
                                        <p class="mini">Loại</p>
										<input class="mini" type="text" name="type">
									</div>
									<div class="add-mini-2">
										<p class="mini">Hình ảnh</p>
										<input type="file" name="image">
										<input class="add-car-submit" type="submit" name="add-product-submit" value="Thêm">
									</div>
								</div>
							</form>
						</div>
					</div>

                    <div class="update-products">
						<h1>Cập nhật</h1>
						<div class="add">
							<form action="admin.php" method="post" enctype="multipart/form-data">
                                <input class="id_holder" type="hidden" name="id" value="">
                                
                                <p class="mini">Ảnh mới (Để trống nếu không đổi)</p>
                                <input type="file" name="image">

								<p>Tình trạng</p>
								<textarea name="condition"></textarea>
								<p>Điện thoại</p>
								<input type="text" name="phone">
								<p>Email</p>
								<input type="text" name="email">

								<div class="update-mini">
									<div class="update-mini-1">
										<p class="mini">Giá</p>
										<input class="mini" type="text" name="price">
										<input class="add-car-submit" type="submit" name="update-product-submit" value="Cập nhật">
                                        <div class="add-car-decline" onclick="decline_product()"> Hủy </div>
									</div>
								</div>
							</form>
						</div>
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
        <script type="text/javascript" src="javaScript/admin.js"></script>
	</body>
</html>