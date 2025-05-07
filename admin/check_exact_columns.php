<?php
// Database connection
require_once("dbconn.php");

// Get the exact column names from the faculty_compliance_status table
$query = "SELECT * FROM faculty_compliance_status LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result) {
    // Get field information
    $fields = mysqli_fetch_fields($result);
    
    echo "<h2>Exact Column Names in faculty_compliance_status Table</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Column Name</th><th>Type</th></tr>";
    
    foreach ($fields as $field) {
        echo "<tr>";
        echo "<td>" . $field->name . "</td>";
        echo "<td>" . $field->type . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Show a sample row
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        echo "<h2>Sample Row Data</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Column</th><th>Value</th></tr>";
        
        foreach ($row as $column => $value) {
            echo "<tr>";
            echo "<td>" . $column . "</td>";
            echo "<td>" . $value . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No data found in the table.</p>";
    }
} else {
    echo "<p>Error: " . mysqli_error($conn) . "</p>";
}

// Also check the structure of the table
$query = "SHOW CREATE TABLE faculty_compliance_status";
$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "<h2>Table Structure</h2>";
    echo "<pre>" . htmlspecialchars($row['Create Table']) . "</pre>";
}
?>
