# Dental Appointment System

A web-based appointment system for a dental clinic. Patients register, view available time slots, book appointments, and cancel them. An admin manages appointments, users, and time slots from a dashboard.

## Technologies

- PHP (PDO)
- MySQL / MariaDB
- HTML and CSS
- Chart.js (admin dashboard chart)

## Features

**Patient**
- Register, login, logout
- View available slots by date with remaining capacity
- Book a slot with an optional note (status starts as Pending)
- My Appointments page (Pending, Confirmed, Rejected, Cancelled)
- Cancel own appointment before the slot starts

**Admin**
- Dashboard statistics and a Chart.js chart of appointments by status
- Appointments table with search (name or email), status filter, and confirm / reject / cancel
- Activate or deactivate users (deactivated users cannot log in)
- Add, edit, and delete time slots (slots with pending or confirmed appointments cannot be deleted)

**Business rules enforced on the server**
- No booking slots in the past
- Slot capacity cannot be exceeded
- One appointment per user per slot
- Cancelled and rejected appointments do not count toward capacity
- Only admins can confirm or reject
- Users can only cancel their own appointments
- Deactivated accounts cannot log in

## Installation

1. Install XAMPP (or any PHP + MySQL setup) and start Apache and MySQL.
2. Copy the `dental` folder into `htdocs`.
3. Open phpMyAdmin, go to Import, and import `database.sql`. This creates the `dental_db` database, the tables, demo accounts, and sample slots.
4. If your MySQL username or password is different, edit them in `config.php`.
5. Open `http://localhost/dental/` in the browser.

## Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@dental.com | Admin123 |
| Patient | patient@dental.com | User1234 |

## Project Structure

```
dental/
  config.php               database connection, session, helper functions
  database.sql             database structure and demo data
  header.php / footer.php  shared page layout
  style.css                styling
  index.php                available slots
  register.php, login.php, logout.php
  book.php                 create appointment
  my_appointments.php      patient appointments
  cancel.php               cancel own appointment
  admin/
    index.php              dashboard and chart
    appointments.php       manage appointments
    appointment_action.php confirm / reject / cancel
    users.php              activate / deactivate users
    slots.php              add / edit / delete slots
```

## Testing Checklist

- Register and login work
- Invalid inputs show error messages
- A patient opening `/admin/index.php` gets "Access denied"
- A patient cannot cancel another patient's appointment
- Past slots cannot be booked
- Booking the same slot twice is rejected
- A full slot cannot be booked
- Status changes made by the admin show on My Appointments
- A deactivated user cannot log in
- A slot with pending or confirmed appointments cannot be deleted
