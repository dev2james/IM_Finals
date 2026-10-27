-- BudgetWise tables (based on the ERD). Select/create the database before importing.

CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  phone_number VARCHAR(15) NOT NULL,
  profile_image VARCHAR(255) NULL
) ENGINE=InnoDB;
CREATE TABLE categories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(50) NOT NULL,
  description VARCHAR(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB;
CREATE TABLE allowance (
  allowance_id INT AUTO_INCREMENT PRIMARY KEY,
  amount DECIMAL(10,2) NOT NULL,
  source VARCHAR(50) NOT NULL,
  date_received DATE NOT NULL,
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE expense (
  expense_id INT AUTO_INCREMENT PRIMARY KEY,
  amount DECIMAL(10,2) NOT NULL,
  description VARCHAR(255) NOT NULL,
  expense_date DATE NOT NULL,
  user_id INT NOT NULL,
  category_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(category_id)
) ENGINE=InnoDB;
CREATE TABLE budget (
  budget_id INT AUTO_INCREMENT PRIMARY KEY,
  monthly_limit DECIMAL(10,2) NOT NULL DEFAULT 0,
  remaining_balance DECIMAL(10,2) NOT NULL DEFAULT 0,
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE savings (
  savings_id INT AUTO_INCREMENT PRIMARY KEY,
  savings_amount DECIMAL(10,2) NOT NULL,
  savings_date DATE NOT NULL,
  goal_name VARCHAR(100) NOT NULL,
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE reports (
  report_id INT AUTO_INCREMENT PRIMARY KEY,
  report_type VARCHAR(50) NULL,
  generated_date DATE NULL,
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Starter data: one default user (no login) and four categories.
INSERT INTO users (user_id, full_name, email, phone_number) VALUES (1, 'Student', 'student@example.com', '');
INSERT INTO budget (monthly_limit, remaining_balance, user_id) VALUES (0, 0, 1);
INSERT INTO categories (category_name, description) VALUES
 ('Food','Meals, snacks and drinks'),('Transportation','Fare, fuel and commuting'),
 ('School supplies','Books, printing and materials'),('Personal','Load, hygiene and other needs');
