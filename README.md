📚 Course Booking System – Laravel API

A complete backend API for a Course Booking System built using Laravel 11, featuring authentication, course management, booking logic, instructors, and more.

The system allows users to register, browse courses, book seats, and manage their profiles.
Administrators and instructors can manage courses, seats, and bookings.

🚀 Features
👤 User & Auth

User Registration & Login (Laravel Sanctum)

Token-based Authentication

Logout & Token Refresh

Retrieve authenticated user data

🎓 Courses & Instructors

Create, update, delete courses

Assign instructor to each course

Seat management (available_seats)

Course details & listing

📝 Booking System

Book any available course

Prevent overbooking

Track booking status

Payment simulation (optional)

⚙️ System Capabilities

Global API response formatter

Validation layer for all requests

Database migrations and Eloquent relationships

Middleware protection for private routes

📄 API Documentation

The API is fully documented using Swagger (L5-Swagger).
You can view the interactive documentation here:

👉 http://localhost:8000/api/documentation

Documentation includes:

🔐 Authentication endpoints

👤 User endpoints

🎓 Courses endpoints

📘 Bookings endpoints

🧑‍🏫 Instructors endpoints

❗ Errors & validation responses

Swagger supports live testing directly from the browser.

🛠️ Tech Stack
Technology	Description
Laravel 11	Main backend framework
MySQL	Database
Swagger (L5-Swagger)	API documentation
Sanctum	Token authentication
Eloquent ORM	Data modeling
PHP 8.2+	Language requirements
📂 Project Structure (Important Modules)
app/
 ├── Http/
 │    ├── Controllers/
 │    │     ├── Api/
 │    │     │     ├── AuthController.php
 │    │     │     ├── CourseController.php
 │    │     │     ├── BookingController.php
 │    │     │     └── InstructorController.php
 │    ├── Middleware/
 ├── Models/
 ├── Helper/
 └── Resources/

🔧 Installation
1️⃣ Clone the project
git clone https://github.com/your-username/course-booking-api.git
cd course-booking-api

2️⃣ Install dependencies
composer install

3️⃣ Create environment file
cp .env.example .env

4️⃣ Generate app key
php artisan key:generate

5️⃣ Setup database

Configure .env:

DB_DATABASE=booking_courses
DB_USERNAME=root
DB_PASSWORD=


Run migrations:

php artisan migrate --seed

6️⃣ Serve the project
php artisan serve


Project will run on:
👉 http://localhost:8000

📘 Swagger Documentation Setup

Generate docs:

php artisan l5-swagger:generate


Open docs:
👉 http://localhost:8000/api/documentation

🔐 Authentication

Use Sanctum token:

Authorization: Bearer {token}


Login endpoint:

POST /auth/login

🙌 Author

أسامة يحيى عبد الغني إبراهيم
Backend Developer — Laravel & API Architecture

⭐ Contributions

Pull requests are welcome.
For bugs or issues → Open an issue.
