<?php
session_start();

// Include database connection file
require 'db-connect.php';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Check if the form has been submitted for deletion
if (isset($_POST['deleteEvent'])) {
    $recid = $_POST['event_id'];

    // Delete the event from the database
    $deleteStmt = $pdo->prepare("DELETE FROM wdv341_events WHERE events_id = ?");
    if ($deleteStmt->execute([$recid])) {
        header("Location: deleteEvents.php?message=Event deleted successfully!");
        exit();
    } else {
        header("Location: deleteEvents.php?message=Error deleting event.");
        exit();
    }
}

// Display any messages
if (isset($_GET['message'])) {
    echo '<p>' . htmlspecialchars($_GET['message']) . '</p>';
}

// Link back to homepage.php
echo '<a href="homepage.php">Back to Homepage</a>';

// Fetch events from the database
$query = "SELECT * FROM wdv341_events";
$events = $pdo->query($query);

// Display the table of events
echo '<table border="1">';
echo '<tr><th>Event Name</th><th>Delete</th></tr>';
foreach ($events as $event) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($event['events_name']) . '</td>';
    echo '<td>';
    echo '<form action="deleteEvents.php" method="POST" onsubmit="return confirmDelete(this);">';
    echo '<input type="hidden" name="event_id" value="' . htmlspecialchars($event['events_id']) . '"/>';
    echo '<input type="submit" name="deleteEvent" value="Delete"/>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';
}
echo '</table>';
?>

<script>
function confirmDelete(form) {
    if (confirm("Are you sure you want to delete this event?")) {
        return true;
    } else {
        return false;
    }
}
</script>
