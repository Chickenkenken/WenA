<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];

    // Check if a file is selected
    if (isset($_FILES["pdf"])) {
        $pdfFile = $_FILES["pdf"];

        // Set the uploads directory
        $uploadsDirectory = 'uploads/';

        // Check if the directory exists; if not, create it
        if (!file_exists($uploadsDirectory)) {
            mkdir($uploadsDirectory, 0755, true);
        }

        // Move the uploaded file to the uploads directory
        $uploadedFilePath = $uploadsDirectory . basename($pdfFile["name"]);
        move_uploaded_file($pdfFile["tmp_name"], $uploadedFilePath);

        // Database connection (replace with your database credentials)
        $dbHost = "localhost";
        $dbUser = "root";
        $dbPass = "";
        $dbName = "pdf";

        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare and execute SQL query
        $query = "INSERT INTO tb_pdf (name, pdf) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $pdfContent = file_get_contents($uploadedFilePath);
        $stmt->bind_param("sb", $name, $pdfContent);

        if ($stmt->execute()) {
            echo "PDF uploaded successfully.";

            // Display the uploaded PDF

            echo "<br><embed src='data:application/pdf;base64," . base64_encode($pdfContent) . "' width='600' height='400' type='application/pdf'>";
        } else {
            echo "Error uploading PDF.";
        }

        // Close database connection
        $stmt->close();
        $conn->close();
    } else {
        echo "Please select a PDF file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Upload</title>
</head>
<body>
    <h1>Upload PDF</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        <br>
        <label for="pdf">PDF File:</label>
        <input type="file" name="pdf" accept=".pdf" required>
        <br>
        <button type="submit">Upload</button>
        <h1>PDF FILE</h1>
    </form>
</body>
</html>
