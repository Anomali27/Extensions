<?php
include 'config/config.php';

$query = "DESCRIBE users";
$result = $connection->query($query);

if ($result) {
    echo "Users table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Error: " . $connection->error;
}

$connection->close();
?>
