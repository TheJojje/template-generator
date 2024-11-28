<?php
	// Includes database and RBACK
	include '../includes/db.php';
	include '../includes/rbac_admin.php';
	
	// CRUD operations

	// CREATE
	if (isset($_POST['create'])) {
		$name = $_POST['access_group_name'];
		$description = $_POST['access_group_description'];
		$stmt = $pdo->prepare("INSERT INTO access_groups (access_group_name, access_group_description) 
                           VALUES (:name, :description)");
		$stmt->execute(['name' => $name, 'description' => $description]);
		$_SESSION['message'] = "Created!";
		$_SESSION['message_type'] = "success";
		header("Location: group_mgmt_admin.php");
	}

	// UPDATE
	if (isset($_POST['update'])) {
		$id = $_POST['id'];
		$name = $_POST['access_group_name'];
		$description = $_POST['access_group_description'];
		$stmt = $pdo->prepare("UPDATE access_groups SET access_group_name = :name, access_group_description = :description WHERE id = :id");
		$stmt->execute(['id' => $id, 'name' => $name, 'description' => $description]);
		$_SESSION['message'] = "Saved!";
		$_SESSION['message_type'] = "success";
		header("Location: group_mgmt_admin.php");
	}

	// DELETE
	if (isset($_POST['delete'])) {
		$id = $_POST['id'];
		$stmt = $pdo->prepare("DELETE FROM access_groups WHERE id = :id");
		$stmt->execute(['id' => $id]);
		$_SESSION['message'] = "Deleted!";
		$_SESSION['message_type'] = "error";
		header("Location: group_mgmt_admin.php");
	}

	// Fetch all groups
	$stmt = $pdo->prepare("SELECT * FROM access_groups");
	$stmt->execute();
	$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

	<!-- Functions CSS -->
	<link href="../assets/css/functions.css" rel="stylesheet">

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
						<span>Start</span>
					</a>
				</li><!-- End Dashboard Link -->
	
				<!-- Start Form nav -->
				<li class="nav-item">
					<a class="nav-link collapsed" data-bs-target="#variables-nav" data-bs-toggle="collapse" href="#">
						<i class="bi bi-menu-button-wide"></i><span>Forms</span><i class="bi bi-chevron-down ms-auto"></i>
					</a>
					<ul id="variables-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
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
	
				<li class="nav-heading">System</li>
				
				<!-- Admin tools nav -->
				<!-- Displays links if you are an admin and not apart of access_group_id 1 (usually called Site Admin) -->
				<?php if ($admin === 'Yes' && $access_group_id != '1'): ?>
				<li class="nav-item">
					<a class="nav-link collapsed" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
						<i class="bi bi-person"></i><span>Administration</span><i class="bi bi-chevron-down ms-auto"></i>
					</a>
					<ul id="admin-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
						<li>
							<a href="user_mgmt.php">
								<i class="bi bi-circle"></i><span>User management</span>
							</a>
						</li>
					</ul>
				</li>
				<?php endif; ?>
				
				<!-- Displays full set of admin-tools if you are a member of access_group_id 1 (usually called Site Admin) -->
				<?php if ($access_group_id == "1"): ?>
				<li class="nav-item">
					<a class="nav-link" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
						<i class="bi bi-person"></i><span>Administration</span><i class="bi bi-chevron-down ms-auto"></i>
					</a>
					<ul id="admin-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
						<li>
							<a href="user_mgmt_admin.php">
								<i class="bi bi-circle"></i><span>User management</span>
							</a>
						</li>
						<li>
							<a href="group_mgmt_admin.php" class="active">
								<i class="bi bi-circle"></i><span>Group management</span>
							</a>
						</li>
						<li>
							<a href="activites_admin.php">
								<i class="bi bi-circle"></i><span>Activity</span>
							</a>
						</li>
						<li>
							<a href="login_activities_admin.php">
								<i class="bi bi-circle"></i><span>Login attempts</span>
							</a>
						</li>
					</ul>
				</li>
				<?php endif; ?>
				<!-- End Admin tools nav -->
				
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

      <div class="pagetitle">
        <h1>Administration</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
            <li class="breadcrumb-item active">Group management</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->

      <section class="section dashboard">
        <div class="row">

          <!-- Left side columns -->
          <div class="col-lg-12">
            <div class="row">

              <!-- Title -->
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
		  	            <h5 class="card-title">Add group</h5>
			              
                    		<!-- Form group-create -->
			              	<form method="post" action="">
					            <table class="table small">
						            <tr>
							            <th>Group name</th>
							            <th colspan="2">Description</th>
						            </tr>
						            <tr>
							            <td><input type="text" name="access_group_name" placeholder="Group Name" class="form-control" required></td>
							            <td><input type="text" name="access_group_description" placeholder="Group description" class="form-control"></td>
							            <td align="right"><button type="submit" name="create" class="btn btn-outline-success btn-sm">Create</button></td>
						            </tr>
					            </table>
				            </form>
                    		<!-- End Form group-create -->

				            <!-- Group list -->
			              <h5 class="card-title">Group list</h5>
			              <table class="table small" >
						            <tr>
					              <th>Group Name</th>
						            <th>Description</th>
					              <th align="right">Actions</th>
				              </tr>
				              <?php foreach ($groups as $group): ?>
					            <form method="POST" style="display:inline;">
				                <input type="hidden" name="id" value="<?php echo $group['id']; ?>">
					              <tr>
							            <td><input type="text" name="access_group_name" value="<?php echo $group['access_group_name']; ?>" class="form-control"></td>
						              <td><input type="text" name="access_group_description" value="<?php echo $group['access_group_description']; ?>" class="form-control"></td>
						              <td align="right">
							              <button type="submit" name="update" class="btn btn-outline-primary btn-sm">Update</button>
							              <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this group?');" class="btn btn-outline-danger btn-sm">Delete</button>
						              </td>
					              </tr>
				              </form>
                      <?php endforeach; ?>
  		              </table>
                    <!-- End group list -->
                      
                  </div>
                </div>
              </div>
              <!-- End Title -->

            </div>
          </div>
          <!-- End Left side columns -->

        </div>
      </section>
    </main>
    <!-- End main -->

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

	<!-- Message -->
	<?php include '../includes/message.php'; ?>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

	<!-- Status message JS File -->
	<script>
  		document.addEventListener("DOMContentLoaded", function () {
      		const message = document.getElementById("message");
      		if (message) {
          		setTimeout(() => {
              		message.classList.add("hide");
          		}, 4000);
           		setTimeout(() => {
              		if (message && message.parentNode) {
                   	message.parentNode.removeChild(message);
              		}
          		}, 4500);
      		}
  		});
	</script>

    <!-- Vendor JS Files -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/quill/quill.js"></script>
    <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="../assets/js/main.js"></script>

  </body>
</html>