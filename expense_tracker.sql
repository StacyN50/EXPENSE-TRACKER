-- =========================================
-- DATABASE: expense_tracker
-- Import this file into phpMyAdmin
-- =========================================

CREATE DATABASE IF NOT EXISTS expense_tracker;
USE expense_tracker;

-- =========================================
-- TABLE: expenses
-- =========================================

CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    item VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    category VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- OPTIONAL SAMPLE DATA
-- =========================================

INSERT INTO expenses (date, item, amount, category) VALUES
('2026-04-01', 'Groceries', 2500.00, 'Food'),
('2026-04-02', 'Electricity', 1800.00, 'Utilities'),
('2026-04-03', 'Bus Ticket', 300.00, 'Transport'),
('2026-04-04', 'Internet', 2200.00, 'Utilities'),
('2026-04-05', 'Lunch', 750.00, 'Food'),
('2026-04-06', 'Taxi', 1200.00, 'Transport'),
('2026-04-07', 'Stationery', 500.00, 'Other');

-- =========================================
-- OPTIONAL INDEXES FOR FASTER FILTERING
-- =========================================

CREATE INDEX idx_expense_date ON expenses(date);
CREATE INDEX idx_expense_category ON expenses(category);
CREATE INDEX idx_expense_item ON expenses(item);
