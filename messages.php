<?php
include 'includes/db.php'; // Includes the file responsible for database connection
include 'includes/header.php'; // Includes the header file
include "includes/config.php"; // Includes the configuration file

// Fetch and display messages
$query = "SELECT * FROM messages";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Database query failed."); // Terminates the script if the database query fails
}

// Display the messages in a table
echo '<table class="table">';
echo '<tr><th>Name</th><th>Email</th><th>Message</th><th>Date</th></tr>';

while ($row = mysqli_fetch_assoc($result)) {
    $name = $row['name'];
    $email = $row['email'];
    $message = $row['message'];
    $date = $row['date'];

    echo "<tr><td>$name</td><td>$email</td><td>$message</td><td>$date</td></tr>"; // Outputs the message details in each row of the table
}

echo '</table>';
?>