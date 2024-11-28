<?php
	// Includes database and RBACK
  include 'includes/db.php';
  include 'includes/rbac.php';

// Handle search query
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}

// Pagination variables
$perPage = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Add new topic
        $stmt = $pdo->prepare("INSERT INTO help_topics (question, answer, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute([$_POST['question'], $_POST['answer']]);
    } elseif (isset($_POST['edit'])) {
        // Edit topic
        $stmt = $pdo->prepare("UPDATE help_topics SET question = ?, answer = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$_POST['question'], $_POST['answer'], $_POST['id']]);
    } elseif (isset($_POST['delete'])) {
        // Delete topic
        $stmt = $pdo->prepare("DELETE FROM help_topics WHERE id = ?");
        $stmt->execute([$_POST['id']]);
    }
}

// Get topics for display (with pagination and search filter)
$sql = "SELECT * FROM help_topics WHERE question LIKE :searchQuery ORDER BY created_at DESC LIMIT :offset, :perPage";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':searchQuery', "%$searchQuery%", PDO::PARAM_STR);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of topics for pagination
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM help_topics WHERE question LIKE :searchQuery");
$totalStmt->bindValue(':searchQuery', "%$searchQuery%", PDO::PARAM_STR);
$totalStmt->execute();
$totalTopics = $totalStmt->fetchColumn();
$totalPages = ceil($totalTopics / $perPage);
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
					<ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
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
					<a class="nav-link " href="help.php" class="active">
						<i class="bi bi-patch-question"></i>
						<span>Help</span>
					</a>
				</li>
				<!-- End help Nav -->
		
				<!-- Logout nav -->
				<li class="nav-item">
					<a class="nav-link collapsed" href="logout.php" class="active">
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
				<h1>Help</h1>

				<!-- Breadcrumb -->
				<nav>
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Help</a></li>
						<li class="breadcrumb-item active">List</li>
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
										<h5 class="card-title">Helpsection</h5>
										<?php foreach ($topics as $topic): ?>
											<?php if ($_SESSION['access_group_id'] == 1): ?>
											<!-- Edit Form -->
											<form method="post" style="display:inline;">
											<input type="hidden" name="id" value="<?= $topic['id'] ?>">
											<table class="table small table-bordered">
												<tr>
													<th colspan="2"><h2><?= htmlspecialchars($topic['question']) ?></h2></td>
												</tr>
												<tr>
													<th>Question</th>
													<td><textarea name="question" class="form-control"><?= htmlspecialchars($topic['question']) ?></textarea></td>
												</tr>
												<tr>
													<th>Answer</th>
													<td><textarea name="answer"  class="form-control"><?= htmlspecialchars($topic['answer']) ?></textarea></td>
												</tr>
												<tr>
													<td colspan="2">
														<button type="submit" name="edit">Edit</button>
													</form>
													<form method="post" style="display:inline;">
														<input type="hidden" name="id" value="<?= $topic['id'] ?>">
														<button type="submit" name="delete">Delete</button>
													</form>
													</td>
												</tr>
											</table>
											<?php else: ?>
												<div style="border: 1px solid #ddd; padding: 10px; margin: 10px 0;">
													<h2><?php echo htmlspecialchars($topic['question']); ?></h2>
													<?php echo nl2br(htmlspecialchars($topic['answer'])); ?>
												</div>
											<?php endif; ?>
										<?php endforeach; ?>

											<!-- Pagination Links -->
											<div>
											<?php for ($i = 1; $i <= $totalPages; $i++): ?>
												<a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchQuery); ?>"><?php echo $i; ?></a>
											<?php endfor; ?>
										</div>
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
								<h5 class="card-title">Search</h5>
							
							    <!-- Search Form -->
								<form method="get" action="">
								<input type="text" name="search" placeholder="Search topics..." value="<?php echo htmlspecialchars($searchQuery); ?>">
								<button type="submit">Search</button>
								</form>
							
							
								<?php if ($_SESSION['access_group_id'] == 1): ?>
								<h5 class="card-title">Create new question</h5>
								<!-- Add New Topic Form -->

								<form method="post">
									<table class="table small">
										<tr>
											<th>Question</th>
											<td><textarea type="text" name="question" required placeholder="Question" class="form-control"></textarea></td>
										</tr>
										<tr>
											<th>Answer</th>
											<td><textarea name="answer" required placeholder="Answer" class="form-control"></textarea></td>
										</tr>
									</table>
									<button type="submit" name="add">Add Topic</button>
								</form>
								<?php endif; ?>
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