<?php
class UserProfile {
    private $conn;
    private $table_name = "user_profile";
    private $daily_extra_table = "daily_extra_amounts";

    public $id;
    public $user_id;
    public $designation;
    public $salary;
    public $extra_amount;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get user profile
    public function read($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->designation = $row['designation'];
            $this->salary = $row['salary'];
            $this->extra_amount = $row['extra_amount'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Create or update user profile
    public function createOrUpdate() {
        // Check if profile exists
        $check_query = "SELECT id FROM " . $this->table_name . " WHERE user_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(1, $this->user_id);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            // Update existing profile
            $query = "UPDATE " . $this->table_name . " 
                     SET designation = ?, salary = ?, extra_amount = ?, updated_at = CURRENT_TIMESTAMP 
                     WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->designation);
            $stmt->bindParam(2, $this->salary);
            $stmt->bindParam(3, $this->extra_amount);
            $stmt->bindParam(4, $this->user_id);
        } else {
            // Create new profile
            $query = "INSERT INTO " . $this->table_name . " 
                     (user_id, designation, salary, extra_amount) 
                     VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->user_id);
            $stmt->bindParam(2, $this->designation);
            $stmt->bindParam(3, $this->salary);
            $stmt->bindParam(4, $this->extra_amount);
        }

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Add daily extra amount
    public function addDailyExtra($amount, $description, $date) {
        $query = "INSERT INTO " . $this->daily_extra_table . " 
                 (user_id, amount, description, date) 
                 VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $amount);
        $stmt->bindParam(3, $description);
        $stmt->bindParam(4, $date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get daily extra amounts for a user
    public function getDailyExtras($limit = 30) {
        $query = "SELECT * FROM " . $this->daily_extra_table . " 
                 WHERE user_id = ? 
                 ORDER BY date DESC, created_at DESC 
                 LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete daily extra amount
    public function deleteDailyExtra($extra_id) {
        $query = "DELETE FROM " . $this->daily_extra_table . " 
                 WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $extra_id);
        $stmt->bindParam(2, $this->user_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get monthly extra amount total
    public function getMonthlyExtraTotal($year, $month) {
        $query = "SELECT COALESCE(SUM(amount), 0) as total 
                 FROM " . $this->daily_extra_table . " 
                 WHERE user_id = ? AND YEAR(date) = ? AND MONTH(date) = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $year);
        $stmt->bindParam(3, $month);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Get yearly extra amount total
    public function getYearlyExtraTotal($year) {
        $query = "SELECT COALESCE(SUM(amount), 0) as total 
                 FROM " . $this->daily_extra_table . " 
                 WHERE user_id = ? AND YEAR(date) = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $year);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Get profile analytics
    public function getProfileAnalytics() {
        $current_year = date('Y');
        $current_month = date('n');
        
        // Get monthly totals for current year
        $monthly_query = "SELECT 
                            MONTH(date) as month,
                            COALESCE(SUM(amount), 0) as total_extra
                         FROM " . $this->daily_extra_table . " 
                         WHERE user_id = ? AND YEAR(date) = ?
                         GROUP BY MONTH(date)
                         ORDER BY MONTH(date)";
        $monthly_stmt = $this->conn->prepare($monthly_query);
        $monthly_stmt->bindParam(1, $this->user_id);
        $monthly_stmt->bindParam(2, $current_year);
        $monthly_stmt->execute();
        
        $monthly_data = $monthly_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get current month total
        $current_month_total = $this->getMonthlyExtraTotal($current_year, $current_month);
        
        // Get yearly total
        $yearly_total = $this->getYearlyExtraTotal($current_year);
        
        return [
            'monthly_data' => $monthly_data,
            'current_month_total' => $current_month_total,
            'yearly_total' => $yearly_total,
            'salary' => $this->salary,
            'designation' => $this->designation
        ];
    }
}
?>