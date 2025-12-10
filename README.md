**Overview**

Blood-Bank-Management-System is a web-based application built with PHP, MySQL, HTML, and CSS to help manage blood donation, donor and recipient information, and blood stock efficiently. The system allows donors to register, recipients to request blood, and administrators (or authorized users) to manage donors, recipients, and blood inventory.

**Problem Statement**

Many small clinics, local blood banks, or charitable blood-donation drives still maintain donor/recipient information manually — often using paper records or basic spreadsheets. This leads to inefficiencies: lost records, difficulty searching for donors with a particular blood group, tracking donations or requests, and managing blood stock. In emergency situations, delays in finding compatible donors can have serious consequences.

**Our Solution**

We propose a unified, easy-to-use web application that digitizes the entire blood bank workflow:

Allow prospective donors to register themselves with necessary details (blood group, contact info, location, etc.).

Maintain a database of registered donors and recipients.

Allow recipients (or hospital staff) to request blood of a specific type.

Track current blood stock levels by blood group.

Provide secure login for authorized access (for donor, recipient or admin).

Enable quick search/filtering by blood group, location or donor availability — making it faster to find suitable donors.

This system ensures better record-keeping, faster access to information, and easier management of blood donations and requests.

**Features**

Donor Registration & Management

Recipient or Request Submission for Blood

Blood Stock Management (track inventory by blood group)

Authentication / Login portal

Search and Filter donors / recipients by blood group, location, availability

Request / Donate history (if implemented)

Admin / User separation (optional — if you choose to add different roles)

Project Structure (folder layout)

Based on the folder listing in your GitHub repo, the structure is roughly:

/ (root)

  ├── admin/             ← backend admin-panel files (if any)  
  ├── css/               ← CSS files for styling  
  ├── db/                ← Database connection / config / scripts  
  ├── donor/             ← donor-specific pages / functionality  
  ├── recipient/         ← recipient-specific pages / functionality  
  ├── videos/            ← maybe media or demo videos  
  ├── index.php  
  ├── login_portal.php   ← login page  
  ├── register_portal.php ← registration page  
   


You may adjust/fill this based on your actual file-structure.

**Technologies Used**

Front-end: HTML, CSS

Back-end: PHP

Database: MySQL (managed via phpMyAdmin)

Server (local dev): XAMPP (Apache + MySQL)

**Installation & Usage**

Here are steps to run the project locally using XAMPP and phpMyAdmin:

Install XAMPP on your machine. 
Gist
+1

Start Apache and MySQL services using the XAMPP control panel. 
GitHub
+1

Copy the project folder (i.e. your project directory) into XAMPP’s web root directory — by default:

C:\xampp\htdocs\


Open a web browser and go to http://localhost/phpmyadmin to open phpMyAdmin. 
PHP
+1

Create a new database (for example blood_bank_db or any name you choose).

Import the provided SQL file (if your project includes one) to create necessary tables. (If you don’t have an SQL file, manually create required tables via SQL or phpMyAdmin.) Steps similar to other PHP-MySQL blood bank projects. 
GitHub
+1

Once the database is ready, open your browser and navigate to the project entry point. For example:

http://localhost/YourProjectFolder/index.php


Use the login or registration portal (e.g. login_portal.php / register_portal.php) to begin using the system.

**Team**

Varshini J

Sandhiya S 

**Future Enhancements**

Here are some possible improvements / additional features you could consider for future versions:

Role-based user management: separate roles for admin, donor, recipient, hospital staff.

Donation / request history with timestamps, status tracking, approval workflow.

Email / SMS notifications to donors when blood is needed.

Search / filter by location or city, to match donors and recipients in nearby areas.

Better UI/UX: responsive design, user-friendly dashboards, maybe using a front-end framework (Bootstrap, etc.).

Logging and audit trails for donor/recipient registration and blood requests.

Security enhancements: input validation, password hashing, protection against SQL injection, session management.

Reports / analytics: total donations, blood-group wise stock, request fulfilment stats.

Option to upload donor medical reports, blood-group certificates.

Support for multiple blood banks / branches (if scaling).
