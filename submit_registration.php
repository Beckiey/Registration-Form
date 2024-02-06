<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "registration_db";

$response = array("success" => false, "message" => "");

try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $surname = validateInput($_POST["surname"]);
        $other_names = validateInput($_POST["other_names"]);
        $county = validateInput($_POST["county"]);
        $ministry = validateInput($_POST["ministry"]);
        $department = validateInput($_POST["department"]);
        $designation = validateInput($_POST["designation"]);
        $personal_number = validateInput($_POST["personal_number"]);
        $phone_number = validateInput($_POST["phone_number"]);
        $signature_data = validateInput($_POST["signatureData"]);

        $stmt = $conn->prepare("INSERT INTO registrations (surname, `Other Names`, county, ministry, department, designation, `Personal Number`, `Phone Number`, signature )
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sssssssss", $surname, $other_names, $county, $ministry, $department, $designation, $personal_number, $phone_number, $signature_data);

        $stmt->execute();

        if ($stmt->error) {
            $response["message"] = "An error occurred: " . $stmt->error;
            error_log("Execute failed: " . $stmt->error);
        } else {
            $response["success"] = true;
            $response["message"] = "Registration successful!";
        }

        $stmt->close();
    }

    $conn->close();

} catch (Exception $e) {
    $response["message"] = "An error occurred: " . $e->getMessage();
    error_log("Exception caught: " . $e->getMessage());
}

echo json_encode($response);

function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
