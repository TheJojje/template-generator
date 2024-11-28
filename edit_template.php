<?php
	// Includes database and RBACK
	include 'includes/db.php';
	include 'includes/rbac.php';
	
	// Gets the ID from the URL and does a SQL-query
	$id = $_GET['id'];
	$access_group_id = $_SESSION['access_group_id'];

	// Fetch template and form_name based on form_id
	$stmt = $pdo->prepare("SELECT t.*, f.form_name FROM templates t 
        JOIN forms f ON t.form_id = f.id 
        WHERE t.id = :id AND t.access_group_id = :access_group_id");
	$stmt->execute(['id' => $id, 'access_group_id' => $access_group_id]);
	$template = $stmt->fetch(PDO::FETCH_ASSOC);

	// Gets info from POST
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	    $template_name = $_POST['template_name'];
	    $form_id = $_POST['form_id'];
	    $template_comment = $_POST['template_comment'];
    	$template_content = $_POST['template_content'];
    	$author = $_SESSION['username'];

		// Updates the entry
    	$stmt = $pdo->prepare("UPDATE templates SET template_name = :template_name, form_id = :form_id, template_comment = :template_comment, template_content = :template_content, author = :author WHERE id = :id AND access_group_id = :access_group_id");
    	$stmt->execute([
        	'template_name' => $template_name,
        	'form_id' => $form_id,
        	'template_comment' => $template_comment,
        	'template_content' => $template_content,
        	'author' => $author,
        	'id' => $id,
        	'access_group_id' => $access_group_id
    	]);

		// Insert into activities
		$access_group_id = $_SESSION['access_group_id'];
		$activityMessage = "Edited the template $template_name.";
		$stmt2 = $pdo->prepare("INSERT INTO activities (activity_text, user, access_group_id) VALUES (?, ?, ?)");
    	$stmt2->execute([$activityMessage, $author, $access_group_id]);
	
		$_SESSION['message'] = "Saved!";
		$_SESSION['message_type'] = "success";
    	header("Location: list_templates.php");
    	exit();
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
					<a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
						<i class="bi bi-menu-button-wide"></i><span>Forms</span><i class="bi bi-chevron-down ms-auto"></i>
					</a>
					<ul id="forms-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
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
					<a class="nav-link" data-bs-target="#templates-nav" data-bs-toggle="collapse" href="#">
						<i class="bi bi-journal-text"></i><span>Templates</span><i class="bi bi-chevron-down ms-auto"></i>
					</a>
					<ul id="templates-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
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
						<li class="breadcrumb-item"><a href="index.php">Form</a></li>
						<li class="breadcrumb-item active">Edit</li>
					</ol>
				</nav>
				<!-- End Breadcrumb -->
				
			</div>
			<!-- End Page Title -->

			<section class="section dashboard">
				<div class="row">

					<!-- Left side columns -->
					<div class="col-lg-8">
						<div class="row">

							<!-- Title -->
							<div class="col-12">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title">Info</h5>
										<form method="POST">
											<table class="table small table-borderless">
												<tr>
													<th width="15%">Template name</th>
													<td><input type="text" name="template_name" value="<?php echo $template['template_name']; ?>" class="form-control" required></td>
												</tr>
												<tr>
													<th>Form</th>
													<td>
														<select name="form_id" class="form-select" required>
														<?php
														// Fetch all forms for selection
														$forms_stmt = $pdo->query("SELECT * FROM forms");
														$forms = $forms_stmt->fetchAll(PDO::FETCH_ASSOC);
														foreach ($forms as $form) {
														$selected = ($form['id'] == $template['form_id']) ? 'selected' : '';
															echo "<option value='{$form['id']}' $selected>{$form['form_name']}</option>";
														}
														?>
														</select>
													</td>
												</tr>
												<tr>
													<th>Comment</th>
													<td><textarea name="template_comment" class="form-control"><?php echo $template['template_comment']; ?></textarea>
												</tr>
												<tr>
													<th colspan="2">Configuration</th>
												</tr>
												<tr>
													<td colspan="2"><textarea name="template_content" class="form-control" rows="15" required><?php echo $template['template_content']; ?></textarea></td>
												</tr>
												<tr>
													<td colspan="2"><button type="submit" class="btn btn-outline-success btn-sm">Update</button></td>
												</tr>
											</table>
										</form>
									</div>
								</div>
							</div>
							<!-- End title -->

						</div>
					</div>
					<!-- End Left side columns -->

					<!-- Right side columns -->
					<div class="col-lg-4">

						<!-- Help -->
						<div class="card">
							<div class="card-body">
								<h5 class="card-title">Help</h5>
								When you defined the form you entered a name of your input, for example "snmp_site"
								<p></p>
								<strong>Example</strong><br>
								<div class="code">
								[<br>
								{<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type": "text",&nbsp;<br>
        						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"label": "Hostname",<br>
        						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"name": "snmp_hostname"<br>
    							&nbsp;&nbsp;&nbsp;&nbsp;},<br>
								</div>
								<p></p>
								<strong>Usage</strong><br>
								When you want to use the variable in your template you enter {{input_name}}
								<p></p>
								<strong>Example</strong><br>
								{{snmp_hostname}} would be:
								<div class="code">
									!<br>
									hostname {{snmp_hostname}}<br>
									!
								</div>
								<p></p>
								That would generate:
								<div class="code">
									hostname SWITCHNAME
								</div>
							</div>
						</div>
						<!-- End Help -->
					
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