<?php
include 'includes/db.php';
include 'includes/rbac.php';

// Check if the ID is provided in the URL
if (!isset($_GET['id'])) {
    die("No template ID provided.");
}

$templateId = (int)$_GET['id'];

// Step 1: Fetch the template_name based on the provided ID
$stmt = $pdo->prepare("SELECT template_name FROM templates WHERE id = :id");
$stmt->bindParam(':id', $templateId, PDO::PARAM_INT);
$stmt->execute();
$template = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$template) {
    die("Form not found.");
}

$templateName = $template['template_name'];

// Step 2: Insert an activity log entry into the activities table
$activityText = "Deleted the template $templateName";
$user = $_SESSION['username'] ?? 'Unknown User';
$accessGroupId = $_SESSION['access_group_id'] ?? null;

$activityStmt = $pdo->prepare("INSERT INTO activities (activity_text, user, access_group_id, timestamp) VALUES (:activity_text, :user, :access_group_id, NOW())");
$activityStmt->bindParam(':activity_text', $activityText);
$activityStmt->bindParam(':user', $user);
$activityStmt->bindParam(':access_group_id', $accessGroupId, PDO::PARAM_INT);
$activityStmt->execute();

// Step 3: Delete the form from the forms table
$deleteStmt = $pdo->prepare("DELETE FROM templates WHERE id = :id");
$deleteStmt->bindParam(':id', $templateId, PDO::PARAM_INT);
$deleteStmt->execute();

$_SESSION['message'] = "Deleted!";
$_SESSION['message_type'] = "error";
header("Location: list_templates.php");
exit;
?>
