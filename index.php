<?php
// Includes database and RBACK
include 'includes/db.php';
include 'includes/rbac.php';

// Fetch rows from the activities table where access_group_id matches
$stmt = $pdo->prepare("SELECT activity_text, user, timestamp FROM activities WHERE access_group_id = :access_group_id ORDER BY timestamp DESC LIMIT 5");
$stmt->bindParam(':access_group_id', $access_group_id, PDO::PARAM_INT);
$stmt->execute();

// Fetch all the matching rows
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch forms based on access_group_id
try {
    $stmt = $pdo->prepare("SELECT id, form_name FROM forms WHERE access_group_id = :access_group_id");
    $stmt->bindParam(':access_group_id', $access_group_id, PDO::PARAM_INT);
    $stmt->execute();
    $forms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    exit;
}

// Initialize variables
$form_html = '';
$form_variables = [];
$templates = [];
$template_content = '';

// Handle form selection
if (isset($_POST['form_id']) && $_POST['form_id'] !== "") {
    $form_id = $_POST['form_id'];

    // Fetch form content based on form_id
    try {
        $stmt = $pdo->prepare("SELECT form FROM forms WHERE id = :form_id");
        $stmt->bindParam(':form_id', $form_id, PDO::PARAM_INT);
        $stmt->execute();
        $form_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($form_data) {
            $form_json = json_decode($form_data['form'], true);
            
            foreach ($form_json as $field) {
                $variable_name = '{{' . $field['name'] . '}}';
                $form_html .= "<tr>";
                $form_html .= "<th><label for='{$field['name']}'>{$field['label']}</label></th>";

                // Check for the input type and render accordingly
                if ($field['type'] === 'select' && isset($field['options'])) {
                    // Render a <select> dropdown
                    $form_html .= "<td><select name='{$field['name']}' class='form-select' id='{$field['name']}'>";
                    
                    // Iterate over options for the select dropdown
                    foreach ($field['options'] as $option) {
                        $selected = (isset($_POST[$field['name']]) && $_POST[$field['name']] == $option['value']) ? 'selected' : '';
                        $form_html .= "<option value='{$option['value']}' $selected>{$option['text']}</option>";
                    }
                    
                    $form_html .= "</select></td>";
                } else {
                    // Default to text input if no other type is specified
                    $form_html .= "<td><input type='{$field['type']}' name='{$field['name']}' class='form-control' id='{$field['name']}' value='" . (isset($_POST[$field['name']]) ? htmlspecialchars($_POST[$field['name']]) : '') . "' /></td>";
                }

                $form_html .= "</tr>";

                // Collect variables for placeholder replacement
                $form_variables[$variable_name] = isset($_POST[$field['name']]) ? $_POST[$field['name']] : '';
            }
        }

        // Fetch templates related to this form
        $stmt = $pdo->prepare("SELECT id, template_name, template_content FROM templates WHERE form_id = :form_id");
        $stmt->bindParam(':form_id', $form_id, PDO::PARAM_INT);
        $stmt->execute();
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        exit;
    }
}

// Handle template selection and content processing
if (isset($_POST['template_id']) && $_POST['template_id'] !== "") {
    $template_id = $_POST['template_id'];
    
    try {
        $stmt = $pdo->prepare("SELECT template_content FROM templates WHERE id = :template_id");
        $stmt->bindParam(':template_id', $template_id, PDO::PARAM_INT);
        $stmt->execute();
        $template_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($template_data) {
            $template_content = $template_data['template_content'];
            
            // Replace placeholders in template_content with form variables
            foreach ($form_variables as $placeholder => $value) {
                if (!empty($value)) {
                    // Replace only if the value exists
                    $template_content = str_replace($placeholder, htmlspecialchars($value), $template_content);
                }
            }
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
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
					<a class="nav-link " href="index.php">
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
				<h1>Start</h1>

				<!-- Breadcrumb -->
				<nav>
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Start</a></li>
						<li class="breadcrumb-item active">Generate config</li>
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
							<div class="col-12">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title">Info</h5>
                                        <form method="POST">
                                            <table class="table small">
                                            
                                                <!-- Form Selection Dropdown -->
                                                <tr>
                                                    <th><label for="form_id">Select Form:</label></th>
                                                    <td>
                                                        <select name="form_id" id="form_id" class="form-select" onchange="this.form.submit()">
                                                            <option value="">Select a Form</option>
                                                            <?php foreach ($forms as $form): ?>
                                                                <option value="<?= $form['id']; ?>" <?= isset($form_id) && $form_id == $form['id'] ? 'selected' : ''; ?>>
                                                                    <?= htmlspecialchars($form['form_name']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <!-- Display Form Fields only if form_id is selected -->
                                                <?php if (!empty($form_html)): ?>
                                                <div>
                                                <?= $form_html; ?>
                                                </div>
                                                <?php endif; ?>

                                                <!-- Template Selection Dropdown -->
                                                <?php if (!empty($templates)): ?>
                                                <tr>
                                                    <th><label for="template_id">Select Template:</label></th>
                                                    <td>
                                                        <select name="template_id" id="template_id" class="form-select" onchange="this.form.submit()">
                                                            <option value="">Select a Template</option>
                                                            <?php foreach ($templates as $template): ?>
                                                                <option value="<?= $template['id']; ?>" <?= isset($template_id) && $template_id == $template['id'] ? 'selected' : ''; ?>>
                                                                    <?= htmlspecialchars($template['template_name']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <?php endif; ?>

                                                <!-- Display Processed Template Content -->
                                                <?php if (!empty($template_content)): ?>
												<tr>
													<td colspan="2"><button id="copyButton" class="btn btn-success" style="width: 100%;">Copy</button></td>
												</tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <div>
                                                            <h2>Processed Template Content:</h2>
                                                            <p class="code" id="textToCopy"><?= nl2br(htmlspecialchars($template_content)); ?></p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endif; ?>
                                        	</table>
										</form>
                                    </div>
								</div>
							</div>
						</div>
					</div>
					<!-- End Left side columns -->

					<!-- Right side columns -->
					<div class="col-lg-4">

						<!-- Info -->
						<div class="card">
							<div class="card-body">
								<h5 class="card-title">Info</h5>
								If you dont see your template when you select a form, its most likely because you haven´t selected the proper form when creating the template.<p></p>
							</div>
						</div>
						<!-- End info -->	
					
						<!-- Recent activity -->
						<div class="card">
							<div class="card-body">
							<h5 class="card-title">Recent Activity</h5>
								<?php
								// Display the activities in a table
								if ($activities) {
									echo "<table class='table small'>";
									echo "<thead><tr><th>Activity Text</th><th>Created At</th></tr></thead>";
									echo "<tbody>";
									foreach ($activities as $activity) {
										echo "<tr>";
										echo "<td>" . htmlspecialchars($activity['activity_text']) . "</td>";
										echo "<td>" . htmlspecialchars($activity['timestamp']) . "</td>";
										echo "</tr>";
									}
									echo "</tbody>";
									echo "</table>";
								} else {
									echo "No activities found for this access group.";
								}
								?>
							</div>
						</div>
						<!-- End recent activity -->
					
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

		<!-- Copy config script -->
		<script>
        document.getElementById("copyButton").addEventListener("click", function() {
            // Hämta texten från <p>-elementet
            const textToCopy = document.getElementById("textToCopy").textContent;

            // Skapa ett temporärt element för att kopiera texten
            const tempInput = document.createElement("textarea");
            tempInput.value = textToCopy;
            document.body.appendChild(tempInput);

            // Markera och kopiera texten
            tempInput.select();
            document.execCommand("copy");

            // Ta bort det temporära elementet
            document.body.removeChild(tempInput);

            // Bekräftelse
            alert("Texten har kopierats till urklipp!");
        });
    	</script>

	</body>
</html>
