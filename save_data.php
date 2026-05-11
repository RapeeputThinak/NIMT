<?php
$conn = new mysqli("localhost", "username", "password", "nimt_training");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม (ตัวอย่างบางส่วน)
    $company = $_POST['company_name'];
    $course = $_POST['course_title'];
    $t_date = $_POST['training_date'];
    $email = $_POST['email'];

    $sql = "INSERT INTO bookings (company_name, course_title, training_date, email) 
            VALUES ('$company', '$course', '$t_date', '$email')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index2.html"); // ส่งไปหน้า 2 เมื่อสำเร็จ
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>