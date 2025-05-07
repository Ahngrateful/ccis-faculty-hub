<?php
// Database connection
require_once("dbconn.php");

// Check the actual column names in the faculty_compliance_status table
$query = "SHOW COLUMNS FROM faculty_compliance_status";
$result = mysqli_query($conn, $query);

echo "<h2>faculty_compliance_status Table Columns</h2>";
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
}
echo "</table>";

// Show a sample row from the table
$query = "SELECT * FROM faculty_compliance_status LIMIT 1";
$result = mysqli_query($conn, $query);

echo "<h2>Sample Row from faculty_compliance_status</h2>";
echo "<table border='1'>";
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo "<tr>";
    foreach ($row as $key => $value) {
        echo "<th>" . $key . "</th>";
    }
    echo "</tr>";
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>" . $value . "</td>";
    }
    echo "</tr>";
} else {
    echo "<tr><td>No data found</td></tr>";
}
echo "</table>";
?>
