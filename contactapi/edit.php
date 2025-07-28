<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connect.php';
header('Content-Type: application/json');

$contactID = isset($_POST['contactID']) ? (int)$_POST['contactID'] : 0;
$firstName = trim($_POST['firstName'] ?? '');
$lastName = trim($_POST['lastName'] ?? '');
$emailAddress = trim($_POST['emailAddress'] ?? '');
$phone = trim($_POST['phoneNumber'] ?? '');
$status = trim($_POST['status'] ?? '');
$dob = trim($_POST['dob'] ?? '');
$originalImageName = $_POST['originalImageName'] ?? '';
$typeID = isset($_POST['typeID']) ? (int)$_POST['typeID'] : 0;

// Allowed formats
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
$allowedMimeTypes  = ['image/jpeg', 'image/png', 'image/gif'];

// Required field check
if (
    $contactID < 1 || $firstName === '' || $lastName === '' ||
    $emailAddress === '' || $phone === ''
) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields.']);
    exit;
}

// Sanitize for DB
$contactID = mysqli_real_escape_string($con, $contactID);
$firstName = mysqli_real_escape_string($con, $firstName);
$lastName = mysqli_real_escape_string($con, $lastName);
$emailAddress = mysqli_real_escape_string($con, $emailAddress);
$phone = mysqli_real_escape_string($con, $phone);
$status = mysqli_real_escape_string($con, $status);
$dob = mysqli_real_escape_string($con, $dob);
$typeID = mysqli_real_escape_string($con, $typeID);

$imageName = $originalImageName;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = $_FILES['image']['name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $uploadFileDir = './uploads/';


    // Extension check
    if (!in_array($fileExt, $allowedExtensions)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file extension. Only JPG, PNG, and GIF are allowed.']);
        exit;
    }

    // MIME check using getimagesize
    $imageInfo = getimagesize($fileTmpPath);
    if (!$imageInfo || !in_array($imageInfo['mime'], $allowedMimeTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid image MIME type. Only JPG, PNG, and GIF are allowed.']);
        exit;
    }

    $dest_path = $uploadFileDir . basename($fileName);
    if (move_uploaded_file($fileTmpPath, $dest_path)) {
        $imageName = $fileName;

        // Delete old image if it's not placeholder
        if ($originalImageName !== 'placeholder_100.jpg') {
            $oldImagePath = $uploadFileDir . $originalImageName;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to move uploaded file.']);
        exit;
    }
}

// Escape image name
$imageName = mysqli_real_escape_string($con, $imageName);

// Update DB
$sql = "UPDATE `contacts` SET 
        `firstName` = '$firstName',
        `lastName` = '$lastName',
        `emailAddress` = '$emailAddress',
        `phoneNumber` = '$phone',
        `status` = '$status',
        `dob` = '$dob',
        `imageName` = '$imageName',
        `typeID` = '$typeID'
        WHERE `contactID` = '$contactID'
        LIMIT 1";

if (mysqli_query($con, $sql)) {
    http_response_code(200);
    echo json_encode(['message' => 'Contact updated']);
} else {
    http_response_code(422);
    echo json_encode(['error' => 'Database update failed']);
}
?>
