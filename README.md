# FTW-Connect
A social media platform project for FTW.

> **Note:** Modular component design is used. Components are included where needed (`include`).

---

## .gitignore
- The `config` file is ignored because it contains sensitive database connection data.

---
```
## Folder Structure
FTW-Connect/
│
├── config/
│ └── config.php # Database credentials and other config
│
├── public/
│ ├── index.php # Example page
│ ├── login.php
│ ├── register.php
│ └── assets/
│ ├── img/ # Logos, backgrounds, icons, etc.
│ └── css/ # Optional: additional CSS files
│
├── database/
│ └── ftw_connect.sql # SQL file to import database
│
└── src/
├── db/
│ └── connect.php # DB connection script
│
└── includes/
├── header.php # Reusable header component
├── footer.php # Reusable footer component
└── navbar.php # Reusable navigation bar
```

---

### Notes:
- All **PHP pages** live in `public/`.
- All **assets** like images, backgrounds, and optional CSS/JS go into `public/assets/`.
- All **modular components** (header, footer, navbar) are stored in `src/includes/`.
- **Database scripts** live in `database/`.
- **DB connection logic** is centralized in `src/db/connect.php` for reuse across the site.