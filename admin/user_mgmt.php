<?php
  // Includes database and RBAC
	include '../includes/db.php';
	include '../includes/rbac.php';
	
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['create'])) {
            // Create new user
            $username = $_POST['username'];
			$name = $_POST['name'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $admin = $_POST['admin'];

            $stmt = $pdo->prepare("INSERT INTO users (username, name, email, password, admin, access_group_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $name, $email, $password, $admin, $access_group_id]);
        } elseif (isset($_POST['update'])) {
            // Update existing user
            $id = (int)$_POST['id'];
            $username = $_POST['username'];
			$name = $_POST['name'];
            $email = $_POST['email'];
            $admin = $_POST['admin'];

            $stmt = $pdo->prepare("UPDATE users SET username = ?, name = ?, email = ?, admin = ? WHERE id = ? AND access_group_id = ?");
            $stmt->execute([$username, $name, $email, $admin, $id, $access_group_id]);
        } elseif (isset($_POST['delete'])) {
            // Delete user
              $id = $_POST['id'];
              $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
              $stmt->execute(['id' => $id]);
        }
    }

    // Fetch users with the same access_group_id
    $stmt = $pdo->prepare("SELECT * FROM users WHERE access_group_id = ?");
    $stmt->execute([$access_group_id]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Template generator</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="../assets/css/style.css" rel="stylesheet">

    <!-- =======================================================
    * Template Name: NiceAdmin
    * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
    * Updated: Apr 20 2024 with Bootstrap v5.3.3
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
  </head>
  <body>

    <!-- Header -->
		<header id="header" class="header fixed-top d-flex align-items-center">

      <!-- Logo -->
      <div class="d-flex align-items-center justify-content-between">
        <a href="../index.php" class="logo d-flex align-items-center">
          <img src="../assets/img/logo.png" alt="">
          <span class="d-none d-lg-block">Template Generator</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
      </div>
      <!-- End Logo -->

      <!-- Start nav -->
      <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">

          <!-- Profile Dropdown Items -->
          <li class="nav-item dropdown pe-3">
      
            <!-- Profile Icon -->
            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
              <img src="../assets/img/user.png" alt="Profile" class="rounded-circle">
              <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION['username'] ?></span>
            </a>
            <!-- End Profile Iamge Icon -->
  
            <!-- User dropdown links -->
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
              <li class="dropdown-header">
                <h6><?php echo $_SESSION['username'] ?></h6>
                <span><?php echo $_SESSION['access_group_name'] ?></span>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item d-flex align-items-center" href="../profile.php">
                  <i class="bi bi-person"></i>
                  <span>My Profile</span>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item d-flex align-items-center" href="../logout.php">
                  <i class="bi bi-box-arrow-right"></i>
                  <span>Sign Out</span>
                </a>
              </li>
            </ul>
            <!-- Ends User dropdown links -->

          </li>
          <!-- End Profile Dropdown Items -->

        </ul>
      </nav>
      <!-- End nav -->

    </header>
    <!-- End Header -->

		<!-- Sidebar -->
		<aside id="sidebar" class="sidebar">
		
			<!-- Start sidebar nav -->
			<ul class="sidebar-nav" id="sidebar-nav">

				<!-- Dashboard link -->
				<li class="nav-item">
					<a class="nav-link collapsed" href="../index.php">
						<i class="bi bi-grid"></i>
						<span>Dashboard</span>
					</a>
				</li><!-- End Dashboard Link -->

				<!-- Start Form nav -->
				<li class="nav-item">
					<a class="nav-link collapsed" data-bs-target="#variables-nav" data-bs-toggle="collapse" href="#">
						<i class="bi bi-menu-button-wide"></i><span>Forms</span><i class="bi bi-chevron-down ms-auto"></i>
					</a>
					<ul id="variables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
						<li>
							<a href="../list_forms.php">
								<i class="bi bi-circle"></i><span>List forms</span>
							</a>
						</li>
						<li>
							<a href="../add_form.php">
								<i class="bi bi-circle"></i><span>Add form</span>
							</a>
						</li>
					</ul>
				</li>
				<!-- End Form nav -->
	
				<!-- Start Templates Nav -->
				<li class="nav-item">
					<a class="nav-link collapsed" data-bs-target="#templates-nav" data-bs-toggle="collapse" href="#">
						<i class="bi bi-journal-text"></i><span>Templates</span><i class="bi bi-chevron-down ms-auto"></i>
					</a>
					<ul id="templates-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
						<li>
							<a href="../list_templates.php">
								<i class="bi bi-circle"></i><span>List templates</span>
							</a>
						</li>
						<li>
							<a href="../add_template.php">
								<i class="bi bi-circle"></i><span>Add new template</span>
							</a>
						</li>
					</ul>
				</li>
				<!-- End Templates Nav -->
	
				<!-- Generate config nav -->
				<li class="nav-item">
					<a class="nav-link collapsed" href="generate_config.php">
						<i class="bi bi-file-earmark-check"></i>
						<span>Generate config</span>
					</a>
				</li>
				<!-- End generate config Nav --> 
	
				<li class="nav-heading">System</li>
				
				<!-- Admin tools nav -->
				<!-- Displays links if you are an admin and not apart of access_group_id 1 (usually called Site Admin) -->
				<?php if ($admin === 'Yes' && $access_group_id != '1'): ?>
				<li class="nav-item">
					<a class="nav-link " data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
						<i class="bi bi-person"></i><span>Administration</span><i class="bi bi-chevron-down ms-auto"></i>
					</a>
					<ul id="admin-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
						<li>
							<a href="user_mgmt.php" class="active">
								<i class="bi bi-circle"></i><span>User management</span>
							</a>
						</li>
					</ul>
				</li>
				<?php endif; ?>
				
				<!-- Help nav -->
				<li class="nav-item">
					<a class="nav-link collapsed" href="../help.php">
						<i class="bi bi-patch-question"></i>
						<span>Help</span>
					</a>
				</li>
				<!-- End help Nav -->
		
				<!-- Logout nav -->
				<li class="nav-item">
					<a class="nav-link collapsed" href="../logout.php">
						<i class="bi bi-box-arrow-in-right"></i>
						<span>Logout</span>
					</a>
				</li>
				<!-- End Logout Nav -->
	
			</ul>
			<!-- End sidebar nav -->
			
		</aside>
		<!-- End Sidebar-->

    <main id="main" class="main">

      <!-- Page titel -->
      <div class="pagetitle">
        <h1>Administration</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
            <li class="breadcrumb-item active">Group management</li>
          </ol>
        </nav>
      </div>
      <!-- End Page Title -->

       <section class="section dashboard">
         <div class="row">

          <!-- Left side columns -->
          <div class="col-lg-9">
            <div class="row">

              <!-- User mgmt -->
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Add / Update user</h5>
			              <form method="POST">
				              <input type="hidden" name="password" value="password">
				              <table class="table small">
					              <tr>
						              <th>Username</th>
						              <th>Name</th>
						              <th>Email</th>
						              <th colspan="2">Admin</th>
					              </tr>
					              <tr>
						              <td><input type="text" name="username" placeholder="Username" class="form-control" required></td>
						              <td><input type="text" name="name" placeholder="Name" class="form-control" required></td>
						              <td><input type="text" name="email" placeholder="Email" class="form-control" required></td>
						              <td>
							              <select name="admin" class="form-select">
								              <option>Select</option>
								              <option value="Yes">Yes</option>
								              <option value="No">No</option>
							              </select>
						              </td>
						              <td><button type="submit" name="create" class="btn btn-outline-success btn-sm">Create User</button></td>
					              </tr>
				              </table>
				            </form>

                    <!-- Users List -->
				            <h5 class="card-title">Users list</h5>
				            <table class="table small">
					            <tr>
						            <th>Username</th>
						            <th>Name</th>
						            <th>Email</th>
						            <th>Admin</th>
						            <th>Actions</th>
					            </tr>
					            <?php foreach ($users as $user): ?>
						          <form method="POST" style="display:inline;">
						            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
							          <tr>
								          <td><input type="text" name="username" value="<?php echo $user['username']; ?>" class="form-control"></td>
								          <td><input type="text" name="name" value="<?php echo $user['name']; ?>" class="form-control"></td>
								          <td><input type="text" name="email" value="<?php echo $user['email']; ?>" class="form-control"></td>
								          <td>
									          <select name="admin" class="form-select">
										          <option><?php echo $user['admin']; ?></option>
										          <option>Yes</option>
										          <option>No</option>
									          </select>
								          </td>
								          <td align="right">
									          <button type="submit" name="update" class="btn btn-outline-primary btn-sm">Update</button>
									          <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this user?');" class="btn btn-outline-danger btn-sm">Delete</button>
								          </td>
							          </tr>
						          </form>
                       <?php endforeach; ?>
				            </table>
                  </div>
                </div>
              </div>
              <!-- End User mgmt -->

              </div>
            </div>
            <!-- End Left side columns -->

            <!-- Right side columns -->
            <div class="col-lg-3">

              <!-- Info -->
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Info</h5>
			            Text
                </div>
              </div>
              <!-- End Info -->

            </div>
            <!-- End Right side columns -->

          </div>
        </section>

    </main><!-- End #main -->

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/chart.js/chart.umd.js"></script>
    <script src="../assets/vendor/echarts/echarts.min.js"></script>
    <script src="../assets/vendor/quill/quill.js"></script>
    <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="../assets/js/main.js"></script>

  </body>
</html>