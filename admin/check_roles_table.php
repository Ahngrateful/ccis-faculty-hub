<?php
// Database connection
require_once("dbconn.php");

// Check the roles table structure
$query = "SHOW COLUMNS FROM roles";
$result = mysqli_query($conn, $query);

echo "<h2>Roles Table Structure</h2>";
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

// Show a sample row from the roles table
$query = "SELECT * FROM roles LIMIT 1";
$result = mysqli_query($conn, $query);

echo "<h2>Sample Row from Roles Table</h2>";
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

// Now let's check the faculty_compliance_status table again
$query = "SELECT * FROM faculty_compliance_status LIMIT 1";
$result = mysqli_query($conn, $query);

echo "<h2>Sample Row from faculty_compliance_status Table</h2>";
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
