 <?php
	$dbc = mysqli_connect('localhost', 'root', '','restoranai-db');
	if(!$dbc ){
		die('Negaliu prisijungti: '.mysqli_error($dbc));
	}
	if (mysqli_connect_errno()) {
	die('Connect failed: '.mysqli_connect_errno().' : '.mysqli_connect_error());
	}
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$username = $_POST['username']; // required
		$password = $_POST['password']; // required
    $passwordhash = md5($password);
		$sql = "SELECT * FROM vartotojas WHERE vartotojo_vardas = '$username' AND slaptazodis = '$passwordhash'";
    $result = mysqli_query($dbc, $sql);
		$row = mysqli_fetch_row($result);

	  if (mysqli_num_rows($result) == 0 || $row[6]) {
      $dbc->close();
      header('Location: ../index.php');
    }
	  else {
      $dbc->close();
      session_start();
      $_SESSION["userType"] = $row[3];
      $_SESSION["userId"] = $row[0];
			$_SESSION["userRefId"] = $row[4];
      header('Location: ../mainMenu.php');
	  }
  }
?>
