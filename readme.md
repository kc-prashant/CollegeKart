# College Cart 🛒

**College Cart** is a simple student marketplace web application where students can **buy, sell, or donate items within their college community**. The platform allows users to list items, browse the marketplace, and book items posted by other students.

This project was built to practice **full-stack web development and basic web security concepts**.

---

# 📌 Features

### 👤 User Authentication

* User signup and login
* Session-based authentication
* Role-based access (Admin / User)

### 🛍 Marketplace

* View all available items
* Filter items by type (Sell / Donate)
* Book items listed by other users

### 📦 Item Management

* Post items for **selling**
* Post items for **donation**
* Edit or update listed items
* Track item status

### 🔄 Transaction System

Items move through different statuses:

* **available** → item listed
* **booked** → user reserved the item
* **completed** → transaction finished
* **cancelled** → transaction cancelled

### ⚙ Admin Features

* Manage users
* Monitor marketplace activity

---

# 🏗 Project Structure

```
college-cart/
│
├── app/
│   ├── config.php        # Configuration constants
│   ├── db.php            # Database connection
│   └── auth_check.php    # Authentication middleware
│
├── actions/
│   ├── edit_item.php
│   └── update_item.php
│
├── public/
│   ├── auth/
│   │   ├── login.php
│   │   └── signup.php
│   │
│   └── user/
│       ├── marketplace.php
│       ├── selling_items.php
│       └── donating_items.php
│
├── debug/
│   └── config_check.php  # System configuration check tool
│
└── README.md
```

---

# 🗄 Database Structure

### `users`

Stores registered users.

| Column   | Description     |
| -------- | --------------- |
| id       | user ID         |
| name     | user name       |
| email    | user email      |
| password | hashed password |
| role     | user/admin      |

---

### `items`

Stores marketplace items.

| Column      | Description          |
| ----------- | -------------------- |
| id          | item ID              |
| name        | item name            |
| description | item details         |
| price       | item price           |
| type        | sell / donate        |
| seller_id   | user who posted item |

---

### `transactions`

Tracks item booking.

| Column    | Description                                |
| --------- | ------------------------------------------ |
| id        | transaction ID                             |
| item_id   | item reference                             |
| buyer_id  | user who booked                            |
| seller_id | item owner                                 |
| type      | sell / donate                              |
| status    | available / booked / completed / cancelled |

---

# 🚀 Installation

### 1️⃣ Clone the repository

```bash
git clone https://github.com/yourusername/college-cart.git
```

---

### 2️⃣ Move project to server directory

If using XAMPP:

```
/xampp/htdocs/college-cart
```

---

### 3️⃣ Create database

Create a database:

```
college_cart
```

Import the SQL schema.

---

### 4️⃣ Configure database connection

Edit:

```
app/db.php
```

Example:

```php
$conn = new mysqli("localhost", "root", "", "college_cart");
```

---

### 5️⃣ Run the project

Start Apache and MySQL.

Then open:

```
http://localhost/college-cart
```

---

# 🔍 Debug Tool

The project includes a **configuration check page** that verifies:

* required files
* database connection
* required tables
* authentication functions
* PHP environment

Example page:

```
config_check.php
```

This helps quickly detect setup issues.

---

# 🛠 Technologies Used

* PHP
* MySQL
* HTML
* CSS
* JavaScript
* XAMPP

---

# 🔐 Security Considerations

The application uses:

* prepared statements for database queries
* session-based authentication
* access control checks

This project is also useful for practicing **web security testing** such as:

* SQL injection testing
* authentication testing
* access control validation

---

# 📚 Learning Purpose

This project was created to practice:

* full-stack web development
* database design
* authentication systems
* debugging and configuration checking
* basic web security concepts

---

# 👨‍💻 Author

**Prashant KC**

Cybersecurity and programming learner exploring web development, automation, and security testing.

---
