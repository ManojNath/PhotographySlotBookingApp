<?php
session_start(); // Start the session

// Database configuration
$host = 'localhost';
$db = 'photography_bookings';
$user = 'root';
$pass = ''; // Replace with your actual database password

// Initialize variables
$error_message = '';
$success_message = '';
$captcha_question = '';
$num1 = 0; // Initialize num1
$num2 = 0; // Initialize num2

// Function to generate a simple CAPTCHA question
function generateCaptcha() {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    return [$num1, $num2, $num1 + $num2];
}

// Check if the CAPTCHA is already generated
if (!isset($_SESSION['captcha_answer'])) {
    // Generate initial CAPTCHA
    list($num1, $num2, $captcha_answer) = generateCaptcha();
    $_SESSION['captcha_answer'] = $captcha_answer; // Store the answer in session
    $_SESSION['captcha_num1'] = $num1; // Store num1 in session
    $_SESSION['captcha_num2'] = $num2; // Store num2 in session
}

// Retrieve num1 and num2 from the session
if (isset($_SESSION['captcha_num1']) && isset($_SESSION['captcha_num2'])) {
    $num1 = $_SESSION['captcha_num1'];
    $num2 = $_SESSION['captcha_num2'];
    $captcha_question = "What is $num1 + $num2?";
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $mobile = htmlspecialchars(trim($_POST['mobile']));
    $instagram = htmlspecialchars(trim($_POST['instagram']));
    $date = $_POST['date'];
    $message = htmlspecialchars(trim($_POST['message']));
    $captcha_response = intval(trim($_POST['captcha']));

    // Check CAPTCHA
    if ($captcha_response !== $_SESSION['captcha_answer']) {
        $error_message = 'CAPTCHA is incorrect. Please try again.';
    } elseif (strtotime($date) < time()) {
        // Check if the date is in the past
        $error_message = 'You cannot book for a past date. Please select a valid date.';
    } elseif (!is_numeric($mobile)) {
        // Check if mobile is numeric
        $error_message = 'Please enter a valid mobile number.';
    } else {
        // Connect to the database
        $conn = new mysqli($host, $user, $pass, $db);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Insert booking into the database
        $stmt = $conn->prepare("INSERT INTO bookings (name, email, mobile, instagram, date, message) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $mobile, $instagram, $date, $message);
        if ($stmt->execute()) {
            // Set success message for modal
            $_SESSION['success_message'] = 'Your photoshoot booking has been confirmed! We can\'t wait to capture your moments.';

            // Send confirmation email
            $subject = "Booking Confirmation - Photoshoot with KC Space Paris";
            $email_body = "
            <html>
            <head>
                <title>Booking Confirmation</title>
            </head>
            <body>
                <h1>Thank You for Your Booking!</h1>
                <p>Dear $name,</p>
                <p>Your booking has been confirmed!</p>
                <p><strong>Details:</strong></p>
                <ul>
                    <li>Name: $name</li>
                    <li>Email: $email</li>
                    <li>Mobile: $mobile</li>
                    <li>Instagram: $instagram</li>
                    <li>Date: $date</li>
                    <li>Message: $message</li>
                </ul>
                <p>We look forward to capturing your moments!</p>
                <p>Best regards,<br>KC Space Paris</p>
            </body>
            </html>
            ";
            
            // Set content-type header for sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: noreply@kcspaceparis.com" . "\r\n"; // Replace with your sender email

            // Send email
            if (!mail($email, $subject, $email_body, $headers)) {
                // Log error or handle failure to send email
                error_log("Email not sent to $email");
            }

            // Regenerate CAPTCHA after successful booking
            list($num1, $num2, $captcha_answer) = generateCaptcha();
            $_SESSION['captcha_answer'] = $captcha_answer; // Store the new answer in session
            $_SESSION['captcha_num1'] = $num1; // Store the new num1 in session
            $_SESSION['captcha_num2'] = $num2; // Store the new num2 in session
            $captcha_question = "What is $num1 + $num2?"; // Update the question

            // Redirect to the same page to display the modal
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error_message = 'Error occurred while making booking. Please try again.';
        }
        $stmt->close();
        $conn->close();
    }
}

// Retrieve success message if set
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear the message after displaying it
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photography Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('background.jpg') no-repeat center center fixed; /* Replace with your background image path */
            background-size: cover;
            color: #f0f0f0; /* Light gray for better readability */
            margin: 0;
            padding: 20px;
        }

        h1, .message, label {
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.7); /* Adding text shadow */
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #ffffff; /* Set color to white for better contrast */
            font-size: 3rem; /* Increased font size for visibility */
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.8); /* Stronger shadow for better readability */
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 150px; /* Adjust the size as needed */
            height: auto;
        }

        form {
            background: rgba(34, 34, 34, 0.8); /* Semi-transparent background for the form */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            max-width: 600px;
            margin: 0 auto;
            backdrop-filter: blur(10px); /* Adding blur effect */
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #e0e0e0; /* Light gray for labels */
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #555;
            border-radius: 4px;
            background: #333; /* Dark background for inputs */
            color: #fff; /* White text for inputs */
        }

        .form-group button {
            padding: 10px 15px;
            background-color: #3498db; /* Blue for buttons */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .form-group button:hover {
            background-color: #2980b9; /* Darker blue on hover */
        }

        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 1.2rem;
        }

        .error {
            color: #e74c3c; /* Red for error messages */
        }

        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="logo.png" alt="KC Space Paris Logo"> <!-- Replace with your logo path -->
    </div>
    <h1>Book Your Photoshoot</h1>

    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Your Name:</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Your Email:</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="mobile">Your Mobile:</label>
            <input type="text" name="mobile" required>
        </div>
        <div class="form-group">
            <label for="instagram">Instagram Handle:</label>
            <input type="text" name="instagram">
        </div>
        <div class="form-group">
            <label for="date">Select Date:</label>
            <input type="date" name="date" required>
        </div>
        <div class="form-group">
            <label for="message">Additional Message:</label>
            <textarea name="message" rows="4"></textarea>
        </div>
        <div class="form-group">
            <label for="captcha"><?php echo $captcha_question; ?></label>
            <input type="text" name="captcha" required>
        </div>
        <div class="form-group">
            <button type="submit">Book Now</button>
        </div>
        <?php if ($error_message): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </form>

    <?php if ($success_message): ?>
        <div class="modal" id="myModal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <p class="message"><?php echo $success_message; ?></p>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // Get the modal
        var modal = document.getElementById("myModal");

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Display the modal if there is a success message
        <?php if ($success_message): ?>
            modal.style.display = "block";
        <?php endif; ?>
    </script>
</body>
</html>
