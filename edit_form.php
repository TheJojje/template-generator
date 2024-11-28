<?php
// Includes database and RBACK
include 'includes/db.php';
include 'includes/rbac.php';

// Gets the ID from the URL and does a SQL-query
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM forms WHERE id = ?");
$stmt->execute([$id]);
$variable = $stmt->fetch(PDO::FETCH_ASSOC);

// Function to validate JSON
function validate_json($json_string) {
    json_decode($json_string);
    return (json_last_error() == JSON_ERROR_NONE);
}

// Gets info from POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_name = $_POST['form_name'];
    $form = $_POST['form'];
    $access_group_id = $_SESSION['access_group_id'];
    $comment = $_POST['comment'];

    // Validate the JSON format
    if (!validate_json($form)) {
        // Return an error message if JSON is invalid
        $_SESSION['message'] = "Invalid JSON format for the form. Please check your syntax.";
		$_SESSION['message_type'] = "error";
    } else {
        // Updates the entry if JSON is valid
        $stmt = $pdo->prepare("UPDATE forms SET form_name = ?, form = ?, access_group_id = ?, comment = ? WHERE id = ?");
        $stmt->execute([$form_name, $form, $access_group_id, $comment, $id]);

        // Insert into activities
        $user = $_SESSION['username'];
        $activityMessage = "Edited the form $form_name.";
        $stmt2 = $pdo->prepare("INSERT INTO activities (activity_text, user, access_group_id) VALUES (?, ?, ?)");
        $stmt2->execute([$activityMessage, $user, $access_group_id]);

        // Redirect after successful update
		$_SESSION['message'] = "Saved!";
		$_SESSION['message_type'] = "success";
        header("Location: list_forms.php");
        exit;
    }
}
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

		<!-- Status message JS File -->
		<script src="assets/js/message.js"></script>

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
				<a href="index.php" class="logo d-flex align-items-center">
					<img src="assets/img/logo.png" alt="">
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
							<img src="assets/img/user.png" alt="Profile" class="rounded-circle">
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
								<a class="dropdown-item d-flex align-items-center" href="profile.php">
									<i class="bi bi-person"></i>
									<span>My Profile</span>
								</a>
							</li>
							<li>
								<hr class="dropdown-divider">
							</li>
							<li>
								<a class="dropdown-item d-flex align-items-center" href="logout.php">
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
					<a class="nav-link collapsed" href="index.php">
						<i class="bi bi-grid"></i>
						<span>Start</span>
					</a>
				</li><!-- End Dashboard Link -->
	
				<!-- Start Form nav -->
				<li class="nav-item">
					<a class="nav-link" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
						<i class="bi bi-menu-button-wide"></i><span>Forms</span><i class="bi bi-chevron-down ms-auto"></i>
					</a>
					<ul id="forms-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
						<li>
							<a href="list_forms.php">
								<i class="bi bi-circle"></i><span>List forms</span>
							</a>
						</li>
						<li>
							<a href="add_form.php">
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
							<a href="list_templates.php">
								<i class="bi bi-circle"></i><span>List templates</span>
							</a>
						</li>
						<li>
							<a href="add_template.php">
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
							<a href="admin/user_mgmt.php">
								<i class="bi bi-circle"></i><span>User management</span>
							</a>
						</li>
					</ul>
				</li>
				<?php endif; ?>
				
				<!-- Displays full set of admin-tools if you are a member of access_group_id 1 (usually called Site Admin) -->
				<?php if ($access_group_id == "1"): ?>
				<li class="nav-item">
					<a class="nav-link collapsed" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
						<i class="bi bi-person"></i><span>Administration</span><i class="bi bi-chevron-down ms-auto"></i>
					</a>
					<ul id="admin-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
						<li>
							<a href="admin/user_mgmt_admin.php">
								<i class="bi bi-circle"></i><span>User management</span>
							</a>
						</li>
						<li>
							<a href="admin/group_mgmt_admin.php">
								<i class="bi bi-circle"></i><span>Group management</span>
							</a>
						</li>
						<li>
							<a href="admin/activites_admin.php">
								<i class="bi bi-circle"></i><span>Activity</span>
							</a>
						</li>
						<li>
							<a href="admin/permission_admin.php">
								<i class="bi bi-circle"></i><span>Permissions</span>
							</a>
						</li>
						<li>
							<a href="admin/login_activities_admin.php">
								<i class="bi bi-circle"></i><span>Login attempts</span>
							</a>
						</li>
					</ul>
				</li>
				<?php endif; ?>
				<!-- End Admin tools nav -->
				
				<!-- Help nav -->
				<li class="nav-item">
					<a class="nav-link collapsed" href="help.php">
						<i class="bi bi-patch-question"></i>
						<span>Help</span>
					</a>
				</li>
				<!-- End help Nav -->
		
				<!-- Logout nav -->
				<li class="nav-item">
					<a class="nav-link collapsed" href="logout.php">
						<i class="bi bi-box-arrow-in-right"></i>
						<span>Logout</span>
					</a>
				</li>
				<!-- End Logout Nav -->
	
			</ul>
			<!-- End sidebar nav -->
			
		</aside>
		<!-- End Sidebar-->

		<!-- Main content -->
		<main id="main" class="main">

			<!-- Page title -->
			<div class="pagetitle">
				<h1>Forms</h1>

				<!-- Breadcrumb -->
				<nav>
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Forms</a></li>
						<li class="breadcrumb-item active">Edit</li>
					</ol>
				</nav>
				<!-- End Breadcrumb -->
				
			</div>
			<!-- End Page Title -->

			<section class="section dashboard">
				<div class="row">

					<!-- Left side columns -->
					<div class="col-lg-7">
						<div class="row">

							<!-- Title -->
							<div class="col-12">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title">Info</h5>
										<!-- Check if there's an error message -->
										<form method="POST" action="">
											<table class="table small table-borderless">
												<tr>
													<th width="20%">Form name</th>
													<td><input type="text" name="form_name" value="<?php echo htmlspecialchars($variable['form_name']); ?>" class="form-control" required></td>
												</tr>
												<tr>
													<th>Comment</th>
													<td><textarea name="comment" class="form-control"><?php echo htmlspecialchars($variable['comment']); ?></textarea>
												</tr>
												<tr>
													<th colspan="2">Form</th>
												</tr>
												<tr>
													<td colspan="2"><textarea name="form" class="form-control" rows="10" required><?php echo htmlspecialchars($variable['form']); ?></textarea>
												</tr>
												<tr>
													<td colspan="2"><button type="submit" class="btn btn-outline-success btn-sm">Update</button></td>
												</tr>
											</table>
										</form>
										<?php if (isset($error_message)): ?>
											<div class="alert alert-danger">
                                				<?php echo $error_message; ?>
                            				</div>
                        				<?php endif; ?>
									</div>
								</div>
							</div>
							<!-- End title -->

						</div>
					</div>
					<!-- End Left side columns -->

					<!-- Right side columns -->
					<div class="col-lg-5">

						<!-- Recent Activity -->
						<div class="card">
							<div class="card-body">
								<h5 class="card-title">Help</h5>
								Choose a name for your form and write a comment to know what its used for<br>
								Form should be formated in JSON-Format.
								<p></p>

								<strong>Supported Types:</strong><p>
								<li>Select</li>
								<li>Text</li>
								<p></p>
								<strong>Example form:</strong><p>
								
								<!-- Start code example -->
								<div class="code">
								[<br>
								{<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type": "select",<br>
        						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"label": "Choose a location",<br>
        						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"name": "snmp_site",<br>
        						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"options": [<br>
            					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{"value": "UPP", "text": "Uppsala"},<br>
            					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{"value": "ROS", "text": "Rosersberg"},<br>
            					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{"value": "BRU", "text": "Brunna"}<br>
        						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;]<br>
    							&nbsp;&nbsp;&nbsp;&nbsp;},<br>
    							&nbsp;&nbsp;&nbsp;&nbsp;{<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type": "text",&nbsp;<br>
        						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"label": "Switchnumber",<br>
        						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"name": "snmp_switchnumber"<br>
    							&nbsp;&nbsp;&nbsp;&nbsp;},<br>
    							&nbsp;&nbsp;&nbsp;&nbsp;{<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type": "text",&nbsp;<br>
        						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"label": "Uplink 1",<br>
        						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"name": "snmp_uplink1"<br>
    							&nbsp;&nbsp;&nbsp;&nbsp;},<br>
    							&nbsp;&nbsp;&nbsp;&nbsp;{<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type": "text",&nbsp;<br>
        						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"label": "Uplink 2",<br>
        						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"name": "snmp_uplink2"<br>
    							&nbsp;&nbsp;&nbsp;&nbsp;}<br>
								]
								</div>
								<!-- End code example -->

							</div>
						</div>
						<!-- End Recent Activity -->
					
					</div>
					<!-- End Right side columns -->
					
				</div>
				<!-- End row -->
					
			</section>
			
		</main>
		<!-- End main -->

		<!-- Footer -->
		<?php include 'includes/footer.php'; ?>

		<!-- Nav to top -->
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