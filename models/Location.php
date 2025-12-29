<?php
class Location {
    private $conn;
    private $table_name = "user_locations";

    public $id;
    public $user_id;
    public $location_name;
    public $location_type;
    public $date;
    public $time_start;
    public $time_end;
    public $notes;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create location entry
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, location_name=:location_name, location_type=:location_type, 
                      date=:date, time_start=:time_start, time_end=:time_end, notes=:notes, created_at=NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->location_name = htmlspecialchars(strip_tags($this->location_name));
        $this->location_type = htmlspecialchars(strip_tags($this->location_type));
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->time_start = $this->time_start ? htmlspecialchars(strip_tags($this->time_start)) : null;
        $this->time_end = $this->time_end ? htmlspecialchars(strip_tags($this->time_end)) : null;
        $this->notes = $this->notes ? htmlspecialchars(strip_tags($this->notes)) : '';

        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":location_name", $this->location_name);
        $stmt->bindParam(":location_type", $this->location_type);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":time_start", $this->time_start);
        $stmt->bindParam(":time_end", $this->time_end);
        $stmt->bindParam(":notes", $this->notes);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Read user locations
    public function read() {
        $query = "SELECT id, user_id, location_name, location_type, date, time_start, time_end, notes, created_at 
                  FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  ORDER BY date DESC, time_start ASC, created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();

        return $stmt;
    }

    // Read locations for specific month
    public function readByMonth($year, $month) {
        $query = "SELECT id, user_id, location_name, location_type, date, time_start, time_end, notes, created_at 
                  FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  AND YEAR(date) = :year 
                  AND MONTH(date) = :month 
                  ORDER BY date DESC, time_start ASC, created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":year", $year);
        $stmt->bindParam(":month", $month);
        $stmt->execute();

        return $stmt;
    }

    // Update location
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET location_name=:location_name, location_type=:location_type, 
                      time_start=:time_start, time_end=:time_end, notes=:notes, updated_at=NOW() 
                  WHERE id=:id AND user_id=:user_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->location_name = htmlspecialchars(strip_tags($this->location_name));
        $this->location_type = htmlspecialchars(strip_tags($this->location_type));
        $this->time_start = $this->time_start ? htmlspecialchars(strip_tags($this->time_start)) : null;
        $this->time_end = $this->time_end ? htmlspecialchars(strip_tags($this->time_end)) : null;
        $this->notes = $this->notes ? htmlspecialchars(strip_tags($this->notes)) : '';
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        // Bind values
        $stmt->bindParam(":location_name", $this->location_name);
        $stmt->bindParam(":location_type", $this->location_type);
        $stmt->bindParam(":time_start", $this->time_start);
        $stmt->bindParam(":time_end", $this->time_end);
        $stmt->bindParam(":notes", $this->notes);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);

        return $stmt->execute();
    }

    // Delete location
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE id=:id AND user_id=:user_id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);

        return $stmt->execute();
    }

    // Get location statistics
    public function getStats($year, $month = null) {
        if ($month) {
            $query = "SELECT location_type, location_name, COUNT(*) as count 
                      FROM " . $this->table_name . " 
                      WHERE user_id = :user_id 
                      AND YEAR(date) = :year 
                      AND MONTH(date) = :month 
                      GROUP BY location_type, location_name 
                      ORDER BY count DESC";
        } else {
            $query = "SELECT location_type, location_name, COUNT(*) as count 
                      FROM " . $this->table_name . " 
                      WHERE user_id = :user_id 
                      AND YEAR(date) = :year 
                      GROUP BY location_type, location_name 
                      ORDER BY count DESC";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":year", $year);
        
        if ($month) {
            $stmt->bindParam(":month", $month);
        }
        
        $stmt->execute();
        return $stmt;
    }
}
?>