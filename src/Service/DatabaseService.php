<?php

namespace App\Service;

use Exception;
use mysqli;

class DatabaseService {

    protected $conn;

    public function __construct($host, $username, $password, $name)
    {
        $this->conn = new mysqli(
            $host,
            $username,
            $password,
            $name
        );

        if ($this->conn->connect_error) {
            throw new Exception("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function updateSoloRank($summonerId, $rank) {
        $stmt = $this->conn->prepare("UPDATE summoners SET rank_solo=? WHERE id=?");
        $stmt->bind_param("ss", $rank, $summonerId);
        $stmt->execute();
    }

    public function updateFlexRank($summonerId, $rank) {
        $stmt = $this->conn->prepare("UPDATE summoners SET rank_flex=? WHERE id=?");
        $stmt->bind_param("ss", $rank, $summonerId);
        $stmt->execute();
    }

    public function addSummoner($summonerId, $accountId, $puuid, $name, $region) {
        // Check if summoner already exists
        $stmt = $this->conn->prepare("SELECT * FROM summoners WHERE id=?");
        $stmt->bind_param("s", $summonerId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            throw new Exception("Summoner already exists in database.");
        }

        // Insert
        $stmt = $this->conn->prepare("INSERT INTO summoners (id, account_id, puuid, name, region) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $summonerId, $accountId, $puuid, $name, $region);
        $stmt->execute();
    }

    public function getSummoners() {
        $query = "SELECT * FROM summoners";
        $result = $this->conn->query($query);

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    

    public function __destruct()
    {
        $this->conn->close();
    }
}