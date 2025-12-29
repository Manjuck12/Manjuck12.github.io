-- Create user_locations table for location tracking (Updated for multiple entries per day)
CREATE TABLE IF NOT EXISTS user_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    location_name VARCHAR(255) NOT NULL,
    location_type VARCHAR(100) NOT NULL,
    date DATE NOT NULL,
    time_start TIME DEFAULT NULL,
    time_end TIME DEFAULT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraint
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Index for better query performance (removed unique constraint)
    INDEX idx_user_date (user_id, date),
    INDEX idx_user_date_time (user_id, date, created_at)
);

-- Insert sample data with multiple entries per day
INSERT INTO user_locations (user_id, location_name, location_type, date, time_start, time_end, notes) VALUES
(1, 'Home', 'home', '2024-12-26', '00:00:00', '09:00:00', 'Morning routine'),
(1, 'Office - IT Department', 'office', '2024-12-26', '09:30:00', '18:00:00', 'Working on React Native project'),
(1, 'Gym', 'gym', '2024-12-26', '19:00:00', '20:30:00', 'Evening workout'),
(1, 'Home', 'home', '2024-12-26', '21:00:00', '23:59:59', 'Evening at home'),
(1, 'Off Day', 'off_day', '2024-12-25', NULL, NULL, 'Christmas holiday'),
(1, 'Client Office - Tech Solutions', 'client_office', '2024-12-24', '10:00:00', '16:00:00', 'Project meeting and demo'),
(1, 'Sick Leave', 'sick_leave', '2024-12-23', NULL, NULL, 'Not feeling well');