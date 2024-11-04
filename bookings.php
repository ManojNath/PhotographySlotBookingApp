<?php
session_start(); // Start the session

// Password protection
$password = 'password';
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['password']) && $_POST['password'] === $password) {
            $_SESSION['logged_in'] = true; // Set logged in status
        } else {
            $error_message = "Incorrect password. Please try again.";
        }
    }
}

// Database connection
$host = 'localhost';
$db = 'photography_bookings';
$user = 'root';
$pass = ''; // Replace with your actual database password

// Check for delete request
if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    
    // Connect to the database
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Delete booking from the database
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $success_message = "Booking deleted successfully.";
    } else {
        $error_message = "Error deleting booking. Please try again.";
    }
    
    $stmt->close();
    $conn->close();
}

// Fetch all bookings for display
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$result = $conn->query("SELECT * FROM bookings ORDER BY date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: #fff;
            margin: 0;
            padding: 20px;
            overflow-x: hidden; /* Prevent horizontal scrolling */
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5rem;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.7);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        table, th, td {
            border: 1px solid #555;
        }

        th, td {
            padding: 12px;
            text-align: left;
            transition: background 0.3s ease;
        }

        th {
            background-color: #34495e;
            color: #ffffff;
            font-size: 1.1rem;
        }

        tr:nth-child(even) {
            background-color: #2c3e50;
        }

        tr:hover {
            background-color: rgba(52, 152, 219, 0.8); /* Highlight row on hover */
            cursor: pointer; /* Change cursor to pointer */
        }

        .edit-button, .delete-button {
            color: #ffffff;
            background-color: #2980b9; /* Blue button color */
            cursor: pointer;
            text-decoration: none; /* Remove underline */
            padding: 5px 10px;
            border: 1px solid #2980b9; /* Matching border color */
            border-radius: 4px;
            transition: background 0.3s ease, color 0.3s ease;
            margin-right: 5px; /* Space between buttons */
        }

        .edit-button:hover, .delete-button:hover {
            background-color: #3498db; /* Darker blue on hover */
        }

        /* Message styles */
        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 1.2rem;
            padding: 15px;
            border-radius: 5px;
            width: 90%;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            transition: opacity 0.5s ease;
        }

        .success {
            background-color: #5dade2; /* Lighter blue for success */
            color: white;
            border: 1px solid #2980b9;
        }

        .error {
            background-color: #e74c3c; /* Keep red for errors to maintain distinction */
            color: white;
            border: 1px solid #c0392b;
        }

        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.5); /* Black w/ opacity */
        }

        .modal-content {
            background-color: #34495e;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 300px; /* Could be more or less, depending on screen size */
            color: #ffffff;
            text-align: center;
            border-radius: 8px;
        }

        .modal-content button {
            background-color: #e74c3c; /* Red color for delete */
            border: none;
            color: white;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 4px;
            cursor: pointer;
        }

        .modal-content button:hover {
            background-color: #c0392b; /* Darker red on hover */
        }
    </style>
</head>
<body>

<h1>View Bookings</h1>

<?php if (isset($error_message)): ?>
    <div class="message error"><?= $error_message ?></div>
<?php endif; ?>

<?php if (isset($success_message)): ?>
    <div class="message success"><?= $success_message ?></div>
<?php endif; ?>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Instagram</th>
        <th>Date</th>
        <th>Message</th>
        <th>Action</th>
    </tr>
    <?php while ($booking = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $booking['id'] ?></td>
            <td><?= $booking['name'] ?></td>
            <td><?= $booking['email'] ?></td>
            <td><?= $booking['mobile'] ?></td>
            <td><?= $booking['instagram'] ?></td>
            <td><?= date('Y-m-d', strtotime($booking['date'])) ?></td>
            <td><?= htmlspecialchars($booking['message']) ?></td>
            <td>
                <a class="edit-button" href="edit_booking.php?id=<?= $booking['id'] ?>">Edit</a>
                <button class="delete-button" onclick="openModal(<?= $booking['id'] ?>)">Delete</button>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<!-- Modal for delete confirmation -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h3>Confirm Deletion</h3>
        <p>Are you sure you want to delete this booking? This action cannot be undone.</p>
        <form id="deleteForm" method="POST" action="">
            <input type="hidden" name="delete_id" id="deleteId" value="">
            <button type="button" onclick="closeModal()">Cancel</button>
            <button type="submit">Delete</button>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById('deleteId').value = id; // Set the booking ID
        document.getElementById('deleteModal').style.display = "block"; // Show the modal
    }

    function closeModal() {
        document.getElementById('deleteModal').style.display = "none"; // Hide the modal
    }

    // Close the modal if the user clicks anywhere outside of it
    window.onclick = function(event) {
        var modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>

</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
