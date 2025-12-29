<?php
class Expense {
    private $conn;
    private $table_name = "expenses";

    public $id;
    public $user_id;
    public $title;
    public $amount;
    public $category;
    public $description;
    public $expense_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ? ORDER BY expense_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET user_id=:user_id, title=:title, amount=:amount, category=:category, description=:description, expense_date=:expense_date";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":expense_date", $this->expense_date);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET title=:title, amount=:amount, category=:category, description=:description, expense_date=:expense_date WHERE id=:id AND user_id=:user_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":expense_date", $this->expense_date);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $this->user_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAnalytics() {
        $query = "SELECT 
                    COUNT(*) as total_expenses,
                    SUM(amount) as total_amount,
                    AVG(amount) as avg_amount,
                    MAX(amount) as max_amount,
                    MIN(amount) as min_amount,
                    YEAR(expense_date) as year,
                    MONTH(expense_date) as month
                  FROM " . $this->table_name . " 
                  WHERE user_id = ? 
                  GROUP BY YEAR(expense_date), MONTH(expense_date)
                  ORDER BY year DESC, month DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();
        return $stmt;
    }
}
?>