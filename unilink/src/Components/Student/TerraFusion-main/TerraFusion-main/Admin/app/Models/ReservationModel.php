<?php

namespace App\Models;

use PDO;

class ReservationModel extends BaseModel
{
    protected $table = 'reservations';
    protected $primaryKey = 'reservation_id'; // Override default 'id'

    // Inherits __construct, getById, delete from BaseModel

    public function getAll()
    {
        // Override for specific sorting
        $sql = "SELECT * FROM {$this->table} ORDER BY reservation_date DESC, reservation_time DESC";
        $stmt = $this->db->query($sql); // $this->db is available from BaseModel
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        // Defaults
        $status = $data['status'] ?? 'Confirmed'; 
        $notes = $data['notes'] ?? '';

        $sql = "INSERT INTO {$this->table} (customer_name, contact_phone, reservation_date, reservation_time, party_size, status, notes, created_at) 
                VALUES (:customer_name, :contact_phone, :reservation_date, :reservation_time, :party_size, :status, :notes, NOW())";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':customer_name', $data['customer_name']);
        $stmt->bindValue(':contact_phone', $data['contact_phone']);
        $stmt->bindValue(':reservation_date', $data['reservation_date']);
        $stmt->bindValue(':reservation_time', $data['reservation_time']);
        $stmt->bindValue(':party_size', $data['party_size']);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':notes', $notes);
        
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} 
                SET customer_name = :customer_name, 
                    contact_phone = :contact_phone, 
                    reservation_date = :reservation_date, 
                    reservation_time = :reservation_time, 
                    party_size = :party_size,
                    status = :status,
                    notes = :notes
                WHERE {$this->primaryKey} = :id";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':customer_name', $data['customer_name']);
        $stmt->bindValue(':contact_phone', $data['contact_phone']);
        $stmt->bindValue(':reservation_date', $data['reservation_date']);
        $stmt->bindValue(':reservation_time', $data['reservation_time']);
        $stmt->bindValue(':party_size', $data['party_size']);
        $stmt->bindValue(':status', $data['status'] ?? 'Confirmed'); // maintain existing or allow update
        $stmt->bindValue(':notes', $data['notes'] ?? '');
        $stmt->bindValue(':id', $id);
        
        return $stmt->execute();
    }
    
    public function getTodayReservationsCount()
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE reservation_date = CURDATE()";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
}