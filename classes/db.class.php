<?php
	class DB{
		private $dbservername = "localhost";
		private $dbusername = "root";
		private $dbpassword = "";
		// CHÚ Ý: Dùng đúng database của dự án là "car_dealership"
		private $dbname = "car_ecommerce";

		// ====== SỬA ĐỔI ======
		// Thêm một thuộc tính để LƯU TRỮ kết nối
		protected $conn; 
		// ======================

		protected function connect(){
			// ====== SỬA ĐỔI ======
			// Chỉ kết nối NẾU CHƯA CÓ KẾT NỐI
			if ($this->conn == null) {
				$this->conn = new mysqli($this->dbservername, $this->dbusername, $this->dbpassword, $this->dbname);
				
				if($this->conn->connect_error){
					// echo 'connection error'; // Không nên echo
					die("Kết nối thất bại: " . $this->conn->connect_error);
				}
			}
			// ======================
			
			// Luôn trả về kết nối ĐÃ CÓ (hoặc vừa tạo)
			return $this->conn;
		}
		
		public function printer($value){
			echo "<pre>" , print_r($value, true) , "</pre>";
		}
	}
?>