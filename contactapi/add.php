<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connect.php';
header('Content-Type: application/json');

// Sanitize and validate inputs
$firstName = trim($_POST['firstName'] ?? '');
$lastName = trim($_POST['lastName'] ?? '');
$emailAddress = trim($_POST['emailAddress'] ?? '');
$phone = trim($_POST['phoneNumber'] ?? '');
$status = trim($_POST['status'] ?? '');
$dob = trim($_POST['dob'] ?? '');
$imageName = $_POST['imageName'] ?? 'placeholder_100.jpg';
$typeID = isset($_POST['typeID']) ? (int)$_POST['typeID'] : 0;

$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
$allowedMimeTypes  = ['image/jpeg', 'image/png', 'image/gif'];

// Validate required fields
if ($firstName === '' || $lastName === '' || $emailAddress === '' || $phone === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields.']);
    exit;
}

// Sanitize for DB
$firstName = mysqli_real_escape_string($con, $firstName);
$lastName = mysqli_real_escape_string($con, $lastName);
$emailAddress = mysqli_real_escape_string($con, $emailAddress);
$phone = mysqli_real_escape_string($con, $phone);
$status = mysqli_real_escape_string($con, $status);
$dob = mysqli_real_escape_string($con, $dob);
$typeID = mysqli_real_escape_string($con, $typeID);

// Check if a new image is uploaded
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = $_FILES['image']['name'];
    $fileSize = $_FILES['image']['size'];

    $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $fileType = mime_content_type($fileTmpPath);
    $uploadFileDir = './uploads/';

    // Ensure upload directory exists
    if (!is_dir($uploadFileDir)) {
        mkdir($uploadFileDir, 0755, true);
    }

    // Optional: Limit file size to 2MB
    if ($fileSize > 2 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['error' => 'File size exceeds 2MB limit.']);
        exit;
    }

    // Validate file format
    if (!in_array($fileExt, $allowedExtensions) || !in_array($fileType, $allowedMimeTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid image format. Only JPG, PNG, and GIF are allowed.']);
        exit;
    }

    $dest_path = $uploadFileDir . $fileName;

    if (move_uploaded_file($fileTmpPath, $dest_path)) {
        $imageName = $fileName;
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to move uploaded file.']);
        exit;
    }
}

// Escape image name
$imageName = mysqli_real_escape_string($con, $imageName);

// Insert into database
$sql = "INSERT INTO `contacts` 
        (`firstName`, `lastName`, `emailAddress`, `phoneNumber`, `status`, `dob`, `imageName`, `typeID`) 
        VALUES 
        ('$firstName', '$lastName', '$emailAddress', '$phone', '$status', '$dob', '$imageName', '$typeID')";

if (mysqli_query($con, $sql)) {
    http_response_code(201);
    echo json_encode(['message' => 'Contact created successfully']);
} else {
    http_response_code(422);
    echo json_encode(['error' => 'Database insert failed']);
}
