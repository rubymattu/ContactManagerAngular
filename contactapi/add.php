<?php
require 'connect.php';
header('Content-Type: application/json');

// Get the posted JSON data and decode it as associative array
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data) && !empty($data)) {
    // Validate
    if (
        trim($data['firstName']) === '' || trim($data['lastName']) === '' ||
        trim($data['emailAddress']) === '' || trim($data['phoneNumber']) === '' ||
        trim($data['status']) === '' || trim($data['dob']) === '' ||
        trim($data['typeID']) === ''
    ) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing required fields']);
        exit;
    }

    // Sanitize
    $firstName = mysqli_real_escape_string($con, trim($data['firstName']));
    $lastName = mysqli_real_escape_string($con, trim($data['lastName']));
    $emailAddress = mysqli_real_escape_string($con, trim($data['emailAddress']));
    $phoneNumber = mysqli_real_escape_string($con, trim($data['phoneNumber']));
    $status = mysqli_real_escape_string($con, trim($data['status']));
    $dob = mysqli_real_escape_string($con, trim($data['dob']));
    $imageName = mysqli_real_escape_string($con, trim($data['imageName']));
    $typeID = (int) $data['typeID'];

    // Fix image name
    $origimg = str_replace('\\', '/', $imageName);
    $new = basename($origimg);
    if (empty($new)) {
        $new = 'placeholder_100.jpg';
    }

    // Store the contact
    $sql = "INSERT INTO `contacts`
            (`contactID`, `firstName`, `lastName`, `emailAddress`, `phoneNumber`, `status`, `dob`, `imageName`, `typeID`) 
            VALUES 
            (null, '{$firstName}', '{$lastName}', '{$emailAddress}', '{$phoneNumber}', '{$status}', '{$dob}', '{$new}', {$typeID})";

    if (mysqli_query($con, $sql)) {
        http_response_code(201);
        $contact = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'emailAddress' => $emailAddress,
            'phoneNumber' => $phoneNumber,
            'status' => $status,
            'dob' => $dob,
            'imageName' => $new,
            'typeID' => $typeID,
            'contactID' => mysqli_insert_id($con)
        ];
        echo json_encode(['data' => $contact]);
    } else {
        http_response_code(422);
        echo json_encode(['message' => 'Failed to insert contact']);
    }
} else {
    http_response_code(400);
    echo json_encode(['message' => 'No data received']);
}
?>
