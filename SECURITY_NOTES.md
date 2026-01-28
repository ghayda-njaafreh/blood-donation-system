# Security / Publishing Notes

Before publishing this repository:

- **Do not commit real database credentials.**
  - Update `config/config.php` locally with your environment values.
- **Do not publish real personal data.**
  - The provided `database/schema.sql` contains **schema only** (no sample donors/requests/users).
- **Admin registration**
  - Use `/register.php` to create the first admin.
  - If deploying publicly, consider disabling `/register.php` after the first admin is created.

