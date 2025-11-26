<?php
// Include database configuration
include "config.php";

// Get search parameters
$fromDate = $_GET['fromDate'];
$toDate = $_GET['toDate'];

// Create a new database connection
$DBC = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

// Check if the connection was successful
if ($DBC->connect_errno) {
    echo "Error: Unable to connect to MySQL. " . $DBC->connect_error;
    exit; // Stop processing the page further
}

// Prepare to search SQL query for available rooms
$query = "SELECT *
FROM room
WHERE roomID NOT IN (
    SELECT roomID FROM booking
    WHERE checkindate < ? AND checkoutdate > ?
)";

// the statement is prepared
$stmt = $DBC->prepare($query);
if ($stmt === false) {
    echo "Error preparing the statement: " . $DBC->error;
    exit;
}

// Bind parameters
$stmt->bind_param("ss", $toDate, $fromDate); // Bind the parameters to the placeholders

// Execute the query
if (!$stmt->execute()) {
    echo "Error executing the query: " . $stmt->error;
    exit;
}

// Get the result set
$result = $stmt->get_result();

// Check the query is successful
if ($result) {
    // Display search result
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['roomID']) . "</td>";
            echo "<td>" . htmlspecialchars($row['roomname']) . "</td>";
            echo "<td>" . htmlspecialchars($row['roomtype']) . "</td>";
            echo "<td>" . htmlspecialchars($row['beds']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "No available rooms found for the selected date range.";
    }
} else {
    // Handle query error
    echo "Error executing the query: " . $DBC->error;
}

// Close the statement
$stmt->close();

// Close database connection
$DBC->close();
?>