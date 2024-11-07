<?php
// Include database connection
include 'db_config.php';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form data
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $date = isset($_POST['date']) ? $_POST['date'] : '';
    $time = isset($_POST['time']) ? $_POST['time'] : '';
    $service = isset($_POST['service']) ? $_POST['service'] : '';

    // Validate appointment time (08:00 - 15:00)
    $opening_time = "08:00:00";
    $closing_time = "15:00:00";

    if ($time < $opening_time || $time > $closing_time) {
        echo "Appointments can only be made between 08:00 and 15:00.";
        exit;
    }

    // Check if an appointment already exists for the selected date and time
    $sql = "SELECT * FROM appointments WHERE appointment_date = ? AND appointment_time = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Error in preparing statement: ' . $conn->error);
    }

    $stmt->bind_param("ss", $date, $time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Sorry, the selected time slot is already booked. Please choose another time.";
    } else {
        // Insert new appointment into the database
        $sql = "INSERT INTO appointments (name, email, phone, appointment_date, appointment_time, service) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die('Error in preparing insert statement: ' . $conn->error);
        }

        $stmt->bind_param("ssssss", $name, $email, $phone, $date, $time, $service);

        if ($stmt->execute()) {
            echo "Appointment booked successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
