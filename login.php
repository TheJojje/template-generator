<?php
  // Starts session
  session_start();

  // Adds databse connection
  require 'includes/db.php';

  // Checks if the info comes from POST
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Executes the login
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // If credentials are correct, set session variables
    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
		  $_SESSION['admin'] = $user['admin'];
		  $_SESSION['access_group_id'] = $user['access_group_id'];

	    // Fetches access_group_name and sets it as session variable
      if (isset($_SESSION['access_group_id'])) {
        $access_group_id = $_SESSION['access_group_id'];

        // Prepare a query to fetch the group name
        $stmt2 = $pdo->prepare("SELECT access_group_name FROM access_groups WHERE id = ?");
        $stmt2->execute([$access_group_id]);
        $group = $stmt2->fetch(PDO::FETCH_ASSOC);

        // Checks if user is apart of a group
        if ($group) {
          $_SESSION['access_group_name'] = $group['access_group_name'];
        } else {
          echo "Access group not found.";
        }
        } else {
          echo "No access group ID found in session.";
        }
        	// Insert into activities
	      	$activityMessage = "$username logged in";
	        $stmt2 = $pdo->prepare("INSERT INTO activities (activity_text, user, access_group_id) VALUES (?, ?, ?)");
          $stmt2->execute([$activityMessage, $username, $access_group_id]);
          header("Location: index.php");
          exit;
      } else {
        // Capture the IP address
        $ip_address = $_SERVER['REMOTE_ADDR'];

        // Insert into failed logins
	      $stmt2 = $pdo->prepare("INSERT INTO login_attempts (attempted_username, ip_address) VALUES (?, ?)");
        $stmt2->execute([$username, $ip_address]);
        echo "Invalid username or password.";
      }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Template Generator</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">

    <!-- =======================================================
    * Template Name: NiceAdmin
    * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
    * Updated: Apr 20 2024 with Bootstrap v5.3.3
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
  </head>
  <body>

    <main>
      <!-- Main container -->
      <div class="container">

        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                <!-- Logo -->
                <div class="d-flex justify-content-center py-4">
                  <a href="index.html" class="logo d-flex align-items-center w-auto">
                    <img src="assets/img/logo.png" alt="">
                    <span class="d-none d-lg-block">Template Generator</span>
                  </a>
                </div>
                <!-- End Logo -->

                <!-- Login div -->
                <div class="card mb-3">
                  <div class="card-body">
                    <div class="pt-4 pb-2">
                      <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                      <p class="text-center small">Enter your username & password to login</p>
                    </div>

                    <!-- Login form -->
                    <form method="POST" class="row g-3 needs-validation" action="">
                      <div class="col-12">
                        <label for="yourUsername" class="form-label">Username</label>
                        <div class="input-group has-validation">
                          <input type="text" name="username" class="form-control" id="yourUsername" required>
                          <div class="invalid-feedback">Please enter your username.</div>
                        </div>
                      </div>

                      <div class="col-12">
                        <label for="yourPassword" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="yourPassword" required>
                        <div class="invalid-feedback">Please enter your password!</div>
                      </div>

                      <div class="col-12">
                        <button class="btn btn-primary w-100" type="submit">Login</button>
                      </div>
                    </form>
                    <!-- End login form -->
                  </div>
                </div>

                <!-- Footer -->
                <?php include 'includes/footer.php'; ?>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main><!-- End #main -->

   <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

  </body>
</html>