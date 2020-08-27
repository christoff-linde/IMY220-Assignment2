<?php
// See all errors and warnings
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

$server = "localhost";
$username = "root";
$password = "";
$database = "dbUser";
$mysqli = mysqli_connect($server, $username, $password, $database);

$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;
// if email and/or pass POST values are set, set the variables to those values, otherwise make them false

?>

<!DOCTYPE html>
<html>

<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Christoff Linde">
	<!-- Replace Name Surname with your name and surname -->
</head>

<body>
	<div class="container">
		<?php
		if ($email && $pass) {
			$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
			$res = $mysqli->query($query);
			if ($row = mysqli_fetch_array($res)) {
				$userID = $row['user_id'];

				echo "
					<table class='table table-bordered mt-3'>
						<tr>
							<td>Name</td>
							<td>" . $row['name'] . "</td>
						<tr>
						<tr>
							<td>Surname</td>
							<td>" . $row['surname'] . "</td>
						<tr>
						<tr>
							<td>Email Address</td>
							<td>" . $row['email'] . "</td>
						<tr>
						<tr>
							<td>Birthday</td>
							<td>" . $row['birthday'] . "</td>
						<tr>
					</table>
				";

				echo "
					<form action='login.php' method='POST' enctype='multipart/form-data'>
						<input type='hidden' name='loginEmail' value='" . $email . "'/>
						<input type='hidden' name='loginPass' value='" . $pass . "'/>
						<div class='form-group'>
							<input type='file' class='form-control' name='picToUpload[]' multiple='multiple' id='picToUpload' /><br/>
							<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
						</div>
					</form>
				";

				if (isset($_FILES["picToUpload"])) {
					$target_dir = "gallery/";
					$uploadFile = $_FILES["picToUpload"];

					$fileCount = count($uploadFile["name"]);

					for ($file = 0; $file < $fileCount; $file++) {
						$fileName = basename($uploadFile["name"][$file]);
						$target_file = $target_dir . basename($uploadFile["name"][$file]);

						$imageFileType = $uploadFile["type"][$file];
						$imageFileSize = $uploadFile["size"][$file];

						if (($imageFileType == "image/jpg" || $imageFileType == "image/jpeg") && ($imageFileSize < 1048576)) {
							if ($uploadFile["error"][$file] > 0)
								echo "Error: " . $uploadFile["error"][$file] . "<br/>";
							else {
								if (move_uploaded_file($uploadFile["tmp_name"][$file], $target_file)) {
									// Upload to DB
									$insertQuery = "INSERT INTO tbgallery (`image_id`, `user_id`, `filename`) VALUES (NULL, '$userID', '$fileName')";
								}
							}
						} else {
							// echo "Invalid image <br/>";
						}
					}
				}

				echo "<h1>Image Gallery</h1>";

				// Get images from db
				$query = "SELECT * from tbgallery WHERE `user_id` LIKE '$userID'";
				$result = $mysqli->query($query);
				if ($result->num_rows > 0) {
					echo "<div class='row imageGallery'>";
					while ($row = $result->fetch_assoc()) {
						echo "
							<div class='col-3' style='background-image: url(gallery/" . $row["filename"] . ")'></div>
						";
					}
					echo "</div>";
				}
			} else {
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
			}
		} else {
			echo 	'<div class="alert alert-danger mt-3" role="alert">
						Could not log you in
					</div>';
		}
		?>
	</div>
</body>

</html>