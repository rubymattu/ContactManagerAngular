<?php
require 'connect.php';
header('Content-Type: application/json');

$sql = "SELECT typeID, typeName FROM contactTypes ORDER BY typeID ASC";

if ($result = mysqli_query($con, $sql)) {
    $types = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $types[] = [
            'typeID' => $row['typeID'],
            'typeName' => $row['typeName'] 
        ];
    }
    echo json_encode($types);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load types']);
}
?>
