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

// Initialize booking variable
$booking = null;

// Handle booking edit
if (isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);
    $conn = new mysqli($host, $user, $pass, $db);

    // Fetch the booking details
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc(); // Fetch booking data
    } else {
        $error_message = "No booking found with that ID.";
    }
    $stmt->close();
    $conn->close();
}

// Handle booking update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_booking'])) {
    $booking_id = intval($_POST['booking_id']);
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $mobile = htmlspecialchars(trim($_POST['mobile']));
    $instagram = htmlspecialchars(trim($_POST['instagram']));
    $date = $_POST['date'];
    $message = htmlspecialchars(trim($_POST['message']));

    // Update the booking in the database
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("UPDATE bookings SET name = ?, email = ?, mobile = ?, instagram = ?, date = ?, message = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $name, $email, $mobile, $instagram, $date, $message, $booking_id);
    
    if ($stmt->execute()) {
        // Redirect to view bookings after update
        header("Location: bookings.php");
        exit();
    } else {
        $error_message = 'Error occurred while updating booking. Please try again.';
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking</title>
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

        .form-container {
            background: rgba(34, 34, 34, 0.9); /* Dark semi-transparent background for the form */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #e0e0e0;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #555;
            border-radius: 4px;
            background: #333;
            color: #fff;
        }

        .form-group button {
            padding: 10px 15px;
            background-color: #2980b9; /* Blue button color */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%; /* Full width for the button */
        }

        .form-group button:hover {
            background-color: #3498db; /* Darker blue on hover */
        }

        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 1.2rem;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        /* Style for the back link */
        .link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: white; /* Changed to white */
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: bold; /* Make it bold */
        }

        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h1>Edit Booking</h1>

<?php if (isset($error_message)): ?>
    <div class="message error"><?= $error_message ?></div>
<?php endif; ?>

<?php if ($booking): ?>
    <div class="form-container">
        <form method="POST" action="">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($booking['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($booking['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="mobile">Mobile</label>
                <input type="text" name="mobile" id="mobile" value="<?= htmlspecialchars($booking['mobile']) ?>" required>
            </div>
            <div class="form-group">
                <label for="instagram">Instagram Username</label>
                <input type="text" name="instagram" id="instagram" value="<?= htmlspecialchars($booking['instagram']) ?>" required>
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" name="date" id="date" value="<?= date('Y-m-d', strtotime($booking['date'])) ?>" required>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea name="message" id="message" required><?= htmlspecialchars($booking['message']) ?></textarea>
            </div>
            <div class="form-group">
                <button type="submit" name="update_booking">Update Booking</button>
            </div>
        </form>
    </div>
<?php else: ?>
    <p class="message">No booking found for editing.</p>
<?php endif; ?>

<!-- Back link -->
<a href="bookings.php" class="link">Back to Bookings</a>

</body>
</html>
