BudgetWise (PHP OOP, no login)
1. Install XAMPP (Apache + MySQL) and start both.
2. Copy this folder to C:\xampp\htdocs\budgetwise
3. Open http://localhost/phpmyadmin, create/select the budgetwise database, choose Import, and import schema.sql.
4. Open http://localhost/budgetwise/
The default database settings are for a local XAMPP MySQL installation (host 127.0.0.1, database budgetwise, user root, no password). For InfinityFree, open Control Panel > MySQL Databases and use the exact MySQL hostname, database name, and username shown there. Create config.local.php beside config.php on the server using config.local.php.example as a template, and enter the database password you set in the control panel. Do not use 127.0.0.1 or the MySQL root user on InfinityFree. Import schema.sql into that database with phpMyAdmin. Alternatively, set BUDGETWISE_DB_HOST, BUDGETWISE_DB_NAME, BUDGETWISE_DB_USER, and BUDGETWISE_DB_PASS in the PHP environment. On an older installation, follow the one-time migration in migrate_to_budgetwise.sql to move BudgetWise app data from budgetwise_app. That migration preserves the original users and categories tables under legacy names and leaves budgetwise_app intact as a backup. Run the migration only once.

PHP sessions are used for CSRF tokens and flash messages. Database transactions wrap record saves, record deletes, and budget updates; the tables use InnoDB so those operations can be rolled back if they fail.
