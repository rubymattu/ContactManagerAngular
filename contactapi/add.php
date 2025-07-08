<?php
require 'connect.php';

// Set header to return JSON
header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get raw POST data
$postdata = file_get_contents("php://input");
error_log('Raw post data: ' . $postdata);

if (isset($postdata) && !empty($postdata)) {
    $request = json_decode($postdata);
    error_log('Decoded request data: ' . json_encode($request));

    if (!$request || !isset($request->data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON or missing data']);
        exit();
    }

    // Validate required fields
    if (
        trim($request->data->firstName) === '' ||
        trim($request->data->lastName) === '' ||
        trim($request->data->emailAddress) === '' ||
        trim($request->data->phone) === '' ||
        trim($request->data->status) === '' ||
        trim($request->data->dob) === ''
    ) {
        http_response_code(400);
        echo json_encode(['error' => 'One or more required fields are empty']);
        exit();
    }

    // Sanitize input
    $firstName = mysqli_real_escape_string($con, trim($request->data->firstName));
    $lastName = mysqli_real_escape_string($con, trim($request->data->lastName));
    $emailAddress = mysqli_real_escape_string($con, trim($request->data->emailAddress));
    $phone = mysqli_real_escape_string($con, trim($request->data->phone));
    $status = mysqli_real_escape_string($con, trim($request->data->status));
    $dob = mysqli_real_escape_string($con, trim($request->data->dob));
    $imageName = mysqli_real_escape_string($con, trim($request->data->imageName));

    // Handle optional image name
    $origimg = str_replace('\\', '/', $imageName);
    $new = basename($origimg);
    if (empty($new)) {
        $new = 'placeholder_100.jpg';
    }

    // Validate and sanitize typeID
    $typeID = isset($request->data->typeID) && intval($request->data->typeID) > 0
        ? intval($request->data->typeID)
        : 1;  // default to 1 if not provided or 0

    // Final insert
    $sql = "INSERT INTO `contacts`(`contactID`, `firstName`, `lastName`, `emailAddress`, `phoneNumber`, `status`, `dob`, `imageName`, `typeID`) 
            VALUES (null, '{$firstName}', '{$lastName}', '{$emailAddress}', '{$phone}', '{$status}', '{$dob}', '{$new}', '{$typeID}')";

    if (mysqli_query($con, $sql)) {
        http_response_code(201);

        $contact = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'emailAddress' => $emailAddress,
            'phone' => $phone,
            'status' => $status,
            'dob' => $dob,
            'imageName' => $new,
            'typeID' => $typeID,
            'contactID' => mysqli_insert_id($con)
        ];

        echo json_encode(['data' => $contact]);
    } else {
        http_response_code(422);
        echo json_encode(['error' => mysqli_error($con)]);
    }

} else {
    http_response_code(400);
    echo json_encode(['error' => 'No data posted']);
}
?>
