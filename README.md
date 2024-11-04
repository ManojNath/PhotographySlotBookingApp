📸 Photography Slot Booking Web Application

Welcome to the Photography Slot Booking Web App, crafted by Manoj Nath. This application is a simple yet effective tool designed for photographers to manage their client bookings with ease. Built using PHP and MySQL, it features secure, real-time database handling, user-friendly booking, and edit functionalities, ensuring a smooth and reliable experience.

🌟 Project Overview

This project includes three core PHP files essential for booking, editing, and managing photography sessions:

📅 bookings.php – Processes new bookings and stores them in the database.
✏️ edit_booking.php – Allows clients to modify their existing bookings.
🏠 index.php – The main entry point where clients can initiate their booking requests.

✨ Features

Client-Side Booking 🖊️: Clients can book sessions by providing their name, email, mobile number, Instagram handle, and preferred date.
Captcha Verification 🔒: A simple math captcha protects against bots.
Database Management 💾: Integrates with MySQL to securely handle booking data.
Booking Confirmation Email 📧: Clients receive a confirmation email once their booking is successfully registered.
Editable Bookings 🔄: Users can revisit and edit their booking details if needed.

📂 Project Structure

Code Structure

🏠 index.php: The landing page for booking appointments, where clients can fill in their information and submit the form.
📅 bookings.php: Handles booking form submissions, validates input and captcha, and saves data in the MySQL database. Sends a confirmation email on success.
✏️ edit_booking.php: Allows clients to retrieve and modify booking information, updating the database with any changes.

Database Structure

The app uses a MySQL database named photography_bookings with a table (bookings) structured as follows:

🆔 id: Unique identifier for each booking (Primary Key).
👤 name: Client's name.
📧 email: Client's email address.
📱 mobile: Contact number.
📸 instagram: Optional Instagram handle.
📅 date: Desired photoshoot date.
📝 message: Additional message or request from the client.

🛠️ Setup Instructions

Clone the Repository 📂: Download the repository to your local environment.

Database Setup:

Create a MySQL database named photography_bookings.
Set up a table as outlined in the "Database Structure" section.
Database Configuration ⚙️:
Update the database credentials ($host, $user, $pass, $db) in both bookings.php and edit_booking.php with your own MySQL details.
Run the Application 🚀: Use XAMPP or WAMP to host the application locally, then access index.php to start booking sessions.

Configure Email ✉️: Set up an email server (e.g., using PHPMailer) to send booking confirmations to clients.

🔎 Additional Information

This app is ideal for photographers or studios needing an online booking system, showcasing key skills in PHP, MySQL, and basic cybersecurity. Secure form validation, captcha protection, and organized database handling make it a reliable solution for small businesses.

👨‍💻 About the Developer

Developed by Manoj Nath, an experienced software developer and cybersecurity consultant with over 8 years of expertise in PHP, MySQL, Python, and secure web applications.
