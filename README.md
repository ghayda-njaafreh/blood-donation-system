# Blood Donation System (PHP + MySQL)

A lightweight web application for managing **blood donors** and **blood requests**.  
Built with **PHP (MySQLi prepared statements)** and a clean, simple UI — suitable for learning, university projects, or small demos.

---

## Features

### Donors Management
- Add donors (name, phone, city, blood type, availability).
- Optional info: age, last donation date.
- Filter/search by **blood type** and **city**.
- Availability flag to quickly show active donors.

### Blood Requests Management
- Create requests (requester info, needed blood type, units, date, notes).
- Manage & view requests list.
- Indexed fields for faster filtering by blood type and city.

### Admin Access
- Admin registration & login.
- Passwords stored securely using **hashing** (no plaintext passwords).

---

## Tech Stack
- **Backend:** PHP
- **Database:** MySQL
- **DB Access:** MySQLi + Prepared Statements
- **Frontend:** HTML / CSS
- **Local Server:** XAMPP / WAMP / MAMP (recommended)

---

## Project Structure

```bash
.
├── admin/                 # Admin pages & actions
├── assets/                # CSS + images
├── config/                # DB configuration & connection
├── database/              # SQL schema (tables only)
├── includes/              # Shared components (header/footer/functions)
├── donor_add.php
├── index.php
├── login.php
├── logout.php
├── match.php
├── register.php
├── request_add.php
├── requests_list.php
├── README.md
└── SECURITY_NOTES.md
````

---

## Getting Started (Local Setup)

### 1) Requirements

* PHP 7.4+ (recommended)
* MySQL 5.7+ / MariaDB
* XAMPP / WAMP / MAMP

### 2) Create Database

1. Open **phpMyAdmin**
2. Create a new database (recommended name):
   `blood_donation`

### 3) Import Schema

Import:

* `database/schema.sql`

> This schema contains **tables only** (no pre-inserted data).

### 4) Configure Database Connection

Open:

* `config/config.php`

Set:

* `DB_HOST`
* `DB_NAME`
* `DB_USER`
* `DB_PASS`

### 5) Run the App

Place the project folder inside your local server directory:

* XAMPP: `htdocs/`
* WAMP: `www/`

Then open in your browser:

```text
http://localhost/blood-donation-system/
```

---

## Admin Usage

### Create Admin Account

* Go to: `/register.php`
* Create your admin user

### Login

* Go to: `/login.php`

> After creating your admin on a production server, it’s recommended to **disable/remove** `register.php` to prevent unauthorized registrations.

---

## Security Notes

This repository is prepared for public sharing:

* No real database credentials included.
* No seeded admin user or sensitive data inside SQL.
* Uses password hashing (no plaintext passwords).

See:

* `SECURITY_NOTES.md`

---

## Screenshots (Optional)

Add screenshots inside `assets/screenshots/` and reference them:

```md
![Home](assets/screenshots/home.png)
![Admin](assets/screenshots/admin.png)
```

---

## Future Improvements (Ideas)

* Role-based access (Admin / Staff)
* Export donors list (CSV / Excel)
* Enhanced search & filtering UI
* Email/SMS notifications
* Docker support

---

## Contact

* LinkedIn: [https://linkedin.com/in/ghayda-n-jaafreh](https://linkedin.com/in/ghayda-n-jaafreh)
