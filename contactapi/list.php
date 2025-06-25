<?php
  require_once 'connect.php';

  $contacts = [];
  $sql = "SELECT imageName, typeID, contactID, firstName, lastName, emailAddress, phone, status, dob 
          FROM contacts";

  $result = mysqli_query($con, $sql);
  if ($result) {
    $count = 0;
    while ($row = mysqli_fetch_assoc($result)) {
      $contacts[$count]['imageName'] = $row['imageName'];
      $contacts[$count]['typeID'] = $row['typeID'];
      $contacts[$count]['contactID'] = $row['contactID'];
      $contacts[$count]['firstName'] = $row['firstName'];
      $contacts[$count]['lastName'] = $row['lastName'];
      $contacts[$count]['emailAddress'] = $row['emailAddress'];
      $contacts[$count]['phone'] = $row['phone'];
      $contacts[$count]['status'] = $row['status'];
      $contacts[$count]['dob'] = $row['dob'];
      $count++; 
    }
  }

  echo json_encode($contacts);
  else {
    http_response_code(404);
  }

?>