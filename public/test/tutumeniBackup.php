<?php

$servername = "localhost"; // Assuming your MySQL server is on the same machine
$username = "tutuetsl_ttmnoc";
$password = "y)xVp@)S]@Fdl089";
$dbname = "tutuetsl_ttmnoc"; // **IMPORTANT: Replace with your actual database name**

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to get all table names
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Tables in database '{$dbname}':</h2>";
    echo "<ul>";
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        // The key for the table name will be dynamic, so we get the first value
        foreach ($row as $tableName) {
            echo "<li>" . $tableName . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "0 results - No tables found in database '{$dbname}'.";
}

$conn->close();

?>