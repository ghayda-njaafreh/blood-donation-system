# Blood Donation System (PHP + MySQL)

## Features
- Donor registration (public) + JavaScript validation
- Find compatible donors by recipient blood type (+ optional city)
- Create blood requests (public) + list/filter requests
- Admin login/register (hashed passwords) + sessions + logout
- Admin panel to manage donors and requests
- Prepared statements (SQL injection protection) + CSRF token on POST

## Setup (XAMPP / WAMP)
1) Copy the folder to your web root:
   - XAMPP: C:\xampp\htdocs\blood_donation_project
2) Create DB & tables:
   - Open phpMyAdmin -> Import -> `database/schema.sql`
3) Update DB credentials:
   - `config/config.php`
4) Open:
   - http://localhost/blood_donation_project/index.php

## Admin Account
- Create your first admin account by opening: `/register.php`
- Then login via: `/login.php`

> Tip: If you deploy this publicly, consider disabling `register.php` after creating your admin account.

## Notes
- Blood compatibility is implemented in `includes/functions.php`.
