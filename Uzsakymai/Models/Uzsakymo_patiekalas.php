<?php
    class Uzsakymo_patiekalas {
        protected $database = null;

        function Uzsakymo_patiekalas($dbc) {
            $this->database = $dbc;
        }

		function getOrderDishes($order){
            $query = "  SELECT uzsakymo_patiekalas.*, patiekalas.pavadinimas,
                            patiekalo_tipas.pavadinimas AS tipoPavadinimas
                        FROM uzsakymo_patiekalas
                        INNER JOIN patiekalas
                            ON fk_patiekalas=patiekalas.id
                        INNER JOIN patiekalo_tipas
                            ON fk_tipas=patiekalo_tipas.id
						WHERE uzsakymo_patiekalas.fk_uzsakymas=$order
                        ORDER BY uzsakymo_patiekalas.id";

            //echo $query;

            $result = mysqli_query($this->database, $query);

            if (!$result || (mysqli_num_rows($result) < 1)) {
                return NULL;
            }

            $dbarray = array();
            while ($order = mysqli_fetch_assoc($result)){
                $dbarray[] = $order;
            }

            return $dbarray;
        }

		function insertOrderDishes($orderId, $dishes, $comments) {
			if (count($dishes) > 0) {
				foreach ($dishes as $key => $dish) {
					$this->insertOrderDish($orderId, $dish, $comments[$key]);
				}
			}
		}

		function insertOrderDish($orderId, $dish, $comment) {
			$query = "  INSERT INTO uzsakymo_patiekalas (komentaras, fk_uzsakymas, fk_patiekalas, busena) 
                        VALUES(?, ?, ?, 1)";
            $stmt = mysqli_prepare($this->database, $query);
            
            mysqli_stmt_bind_param($stmt, 'sii', $comment, $orderId, $dish);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
		}

        function cancelDish($dishId) {
            $query = "  UPDATE uzsakymo_patiekalas SET busena = 3 WHERE id = ?";
            $stmt = mysqli_prepare($this->database, $query);
            
            mysqli_stmt_bind_param($stmt, 'i', $dishId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        function cancelDishes($orderId) {
            $query = "  UPDATE uzsakymo_patiekalas SET busena = 3 WHERE fk_uzsakymas = ? AND busena = 1";
            $stmt = mysqli_prepare($this->database, $query);
            
            mysqli_stmt_bind_param($stmt, 'i', $orderId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }