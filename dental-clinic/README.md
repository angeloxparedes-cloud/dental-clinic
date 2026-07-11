# BrightSmile Dental Clinic – Appointment System
## PHP + MySQL MVC Framework

---

## 📁 Project Structure

```
dental-clinic/
├── index.php                        ← Entry point / router
├── app/
│   ├── config.php                   ← DB config + helper functions
│   ├── controllers/
│   │   ├── AuthController.php       ← Login, Register, Logout
│   │   ├── AppointmentController.php← Book, Cancel appointments
│   │   └── AdminController.php      ← Dashboard, manage appointments & patients
│   └── views/
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       ├── patient/
│       │   └── dashboard.php
│       ├── admin/
│       │   ├── dashboard.php
│       │   ├── appointments.php
│       │   └── patients.php
│       └── shared/
│           ├── sidebar.php
│           └── helpers.php
├── public/
│   └── css/
│       └── style.css
└── database/
    └── schema.sql                   ← All tables + stored procedures + seed data
```

---

## ⚙️ Setup Instructions

### 1. Copy project to XAMPP

Place the `dental-clinic` folder inside:
```
C:/xampp/htdocs/dental-clinic
```

### 2. Import the database

1. Open **phpMyAdmin** → http://localhost/phpmyadmin
2. Click **Import**
3. Choose `database/schema.sql`
4. Click **Go**

### 3. Configure database (if needed)

Open `app/config.php` and update:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // your MySQL password
define('DB_NAME', 'dental_clinic');
define('APP_URL', 'http://localhost/dental-clinic');
```

### 4. Run the app

Visit: **http://localhost/dental-clinic**

---

## 🔑 Default Login Credentials

| Role    | Email                        | Password   |
|---------|------------------------------|------------|
| Admin   | admin@dentalclinic.com       | password   |

*(Register as a patient from the login page)*

---

## ✨ Features

### Patient
- Register & Login
- Book appointments (select dentist, service, date, time)
- View all personal appointments with status
- Cancel pending appointments

### Admin
- Dashboard with stats (pending, confirmed, completed, today's count)
- View & filter all appointments by status
- Update appointment status (pending → confirmed → completed → cancelled)
- View all registered patients with search

---

## 🗄️ Stored Procedures Used

| Procedure                    | Purpose                          |
|------------------------------|----------------------------------|
| `sp_find_user_by_email`      | Login authentication             |
| `sp_register_patient`        | New patient registration         |
| `sp_get_all_patients`        | Admin patient list               |
| `sp_book_appointment`        | Book with conflict checking      |
| `sp_get_patient_appointments`| Patient's appointment history    |
| `sp_get_all_appointments`    | Admin: all appointments          |
| `sp_update_appointment_status`| Admin: change appointment status |
| `sp_cancel_appointment`      | Patient: cancel own appointment  |
| `sp_get_dentists`            | List available dentists          |
| `sp_get_services`            | List available services          |
| `sp_get_dashboard_stats`     | Admin dashboard counters         |

---

## 🛠️ Tech Stack

- **Frontend:** Plain HTML, CSS, Vanilla JS (Google Fonts, inline SVG icons)
- **Backend:** PHP 8+ (MVC pattern, no frameworks)
- **Database:** MySQL with Stored Procedures
- **Auth:** PHP Sessions + password_hash / password_verify
- **Server:** XAMPP (Apache + MySQL)
