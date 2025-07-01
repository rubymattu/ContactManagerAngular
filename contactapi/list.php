<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'connect.php';

$contacts = [];
$sql = "SELECT imageName, typeID, contactID, firstName, lastName, emailAddress, phoneNumber, status, dob FROM contacts";

$result = mysqli_query($con, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $contacts[] = [
            'imageName' => $row['imageName'],
            'typeID' => $row['typeID'],
            'contactID' => $row['contactID'],
            'firstName' => $row['firstName'],
            'lastName' => $row['lastName'],
            'emailAddress' => $row['emailAddress'],
            'phone' => $row['phoneNumber'],
            'status' => $row['status'],
            'dob' => $row['dob']
        ];
    }
    echo json_encode($contacts);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Database query failed."]);
}
?>
