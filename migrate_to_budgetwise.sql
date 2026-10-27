-- Run once on the existing installation to merge budgetwise_app into budgetwise.
-- This preserves the original budgetwise users/categories tables as legacy tables
-- and leaves budgetwise_app intact as a backup.

RENAME TABLE
  budgetwise.users TO budgetwise.legacy_users,
  budgetwise.categories TO budgetwise.legacy_categories;

CREATE TABLE budgetwise.users LIKE budgetwise_app.users;
CREATE TABLE budgetwise.categories LIKE budgetwise_app.categories;
CREATE TABLE budgetwise.allowance LIKE budgetwise_app.allowance;
CREATE TABLE budgetwise.expense LIKE budgetwise_app.expense;
CREATE TABLE budgetwise.budget LIKE budgetwise_app.budget;
CREATE TABLE budgetwise.savings LIKE budgetwise_app.savings;
CREATE TABLE budgetwise.reports LIKE budgetwise_app.reports;

ALTER TABLE budgetwise.allowance
  ADD CONSTRAINT fk_bw_allowance_user
  FOREIGN KEY (user_id) REFERENCES budgetwise.users(user_id) ON DELETE CASCADE;
ALTER TABLE budgetwise.expense
  ADD CONSTRAINT fk_bw_expense_user
  FOREIGN KEY (user_id) REFERENCES budgetwise.users(user_id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_bw_expense_category
  FOREIGN KEY (category_id) REFERENCES budgetwise.categories(category_id);
ALTER TABLE budgetwise.budget
  ADD CONSTRAINT fk_bw_budget_user
  FOREIGN KEY (user_id) REFERENCES budgetwise.users(user_id) ON DELETE CASCADE;
ALTER TABLE budgetwise.savings
  ADD CONSTRAINT fk_bw_savings_user
  FOREIGN KEY (user_id) REFERENCES budgetwise.users(user_id) ON DELETE CASCADE;
ALTER TABLE budgetwise.reports
  ADD CONSTRAINT fk_bw_reports_user
  FOREIGN KEY (user_id) REFERENCES budgetwise.users(user_id) ON DELETE CASCADE;

START TRANSACTION;
INSERT INTO budgetwise.users (user_id, full_name, email, phone_number, profile_image)
  SELECT user_id, full_name, email, phone_number, profile_image FROM budgetwise_app.users;
INSERT INTO budgetwise.categories (category_id, category_name, description)
  SELECT category_id, category_name, description FROM budgetwise_app.categories;
INSERT INTO budgetwise.allowance (allowance_id, amount, source, date_received, user_id)
  SELECT allowance_id, amount, source, date_received, user_id FROM budgetwise_app.allowance;
INSERT INTO budgetwise.expense (expense_id, amount, description, expense_date, user_id, category_id)
  SELECT expense_id, amount, description, expense_date, user_id, category_id FROM budgetwise_app.expense;
INSERT INTO budgetwise.budget (budget_id, monthly_limit, remaining_balance, user_id)
  SELECT budget_id, monthly_limit, remaining_balance, user_id FROM budgetwise_app.budget;
INSERT INTO budgetwise.savings (savings_id, savings_amount, savings_date, goal_name, user_id)
  SELECT savings_id, savings_amount, savings_date, goal_name, user_id FROM budgetwise_app.savings;
INSERT INTO budgetwise.reports (report_id, report_type, generated_date, user_id)
  SELECT report_id, report_type, generated_date, user_id FROM budgetwise_app.reports;
COMMIT;
