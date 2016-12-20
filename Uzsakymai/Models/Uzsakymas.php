<?php
    class Uzsakymas {
        protected $database = null;

        function Uzsakymas($dbc) {
            $this->database = $dbc;
        }

        function getOrders($page = null, $count = null){
            $query = "  SELECT uzsakymas.*, padavejas.vardas AS padVardas, padavejas.pavarde AS padPavarde
						FROM uzsakymas
						INNER JOIN staliukas
							ON staliukas.staliuko_indentifikatorius=uzsakymas.fk_staliukas
						INNER JOIN padavejas
							ON staliukas.fk_padavejas=padavejas.id
                        ORDER BY data ASC";

            //echo $query;

            if ($page && $count) {
                $offset = ($page - 1) * $count;
                $query .= " LIMIT {$count} OFFSET {$offset}";
            }

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

        function getOrderCount() {
            $query = "  SELECT COUNT(id) AS count
                        FROM uzsakymas";

            $result = mysqli_query($this->database, $query);
            /* Error occurred, return given name by default */
            if (!$result || (mysqli_num_rows($result) < 1)) {
                return 0;
            }

            $count = mysqli_fetch_assoc($result);

            return $count['count'];
        }

        function newOrder($table) {
            $query = "  INSERT INTO uzsakymas (data, busena, fk_staliukas) 
                        VALUES(NOW(), 1, ?)";
            $stmt = mysqli_prepare($this->database, $query);
            
            mysqli_stmt_bind_param($stmt, 's', $table);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

		function getLastInsertId(){
			return mysqli_insert_id($this->database);
		}

    }