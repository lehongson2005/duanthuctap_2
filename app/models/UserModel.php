<?php
class UserModel {
    private $conn;
    private $table = 'users';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /* =========================
       GET USER
    ========================= */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /* =========================
       CHECK EXIST
    ========================= */
    public function isUsernameExists($username, $excludeId = null) {
        $sql = "SELECT id FROM {$this->table} WHERE username=?";
        if ($excludeId) $sql .= " AND id!=?";
        $stmt = $this->conn->prepare($sql);
        $excludeId ? $stmt->bind_param("si", $username, $excludeId)
                   : $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function isEmailExists($email, $excludeId = null) {
        $sql = "SELECT id FROM {$this->table} WHERE email=?";
        if ($excludeId) $sql .= " AND id!=?";
        $stmt = $this->conn->prepare($sql);
        $excludeId ? $stmt->bind_param("si", $email, $excludeId)
                   : $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    /* =========================
       CREATE
    ========================= */
    public function create(
        $username, $email, $password,
        $full_name, $gender, $phone, $address,
        $role = 0, $status = 1
    ) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
            (username,email,password,full_name,gender,phone,address,role,status,created_at)
            VALUES (?,?,?,?,?,?,?,?,?,NOW())
        ");

        $stmt->bind_param(
            "sssssssii",
            $username, $email, $hash,
            $full_name, $gender, $phone, $address,
            $role, $status
        );

        return $stmt->execute();
    }

    /* =========================
       UPDATE
    ========================= */
    public function update(
        $id, $username, $email,
        $full_name, $gender, $phone, $address,
        $role, $status, $password = null
    ) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "
                UPDATE {$this->table}
                SET username=?,email=?,password=?,
                    full_name=?,gender=?,phone=?,address=?,
                    role=?,status=?
                WHERE id=?
            ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                "sssssssiii",
                $username, $email, $hash,
                $full_name, $gender, $phone, $address,
                $role, $status, $id
            );
        } else {
            $sql = "
                UPDATE {$this->table}
                SET username=?,email=?,
                    full_name=?,gender=?,phone=?,address=?,
                    role=?,status=?
                WHERE id=?
            ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                "ssssssiii",
                $username, $email,
                $full_name, $gender, $phone, $address,
                $role, $status, $id
            );
        }

        return $stmt->execute();
    }

    /* =========================
       DELETE
    ========================= */
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* =========================
       SEARCH + FILTER + PAGINATION
    ========================= */
    public function getTotalUsers($keyword='', $role='', $status='') {
        $sql = "SELECT COUNT(*) total FROM {$this->table} WHERE 1=1";
        $params=[]; $types="";

        if ($keyword) {
            $sql .= " AND (username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
            $k="%$keyword%";
            $params[]=$k; $params[]=$k; $params[]=$k;
            $types.="sss";
        }
        if ($role!=='') { $sql.=" AND role=?"; $params[]=$role; $types.="i"; }
        if ($status!=='') { $sql.=" AND status=?"; $params[]=$status; $types.="i"; }

        $stmt=$this->conn->prepare($sql);
        if ($types) $stmt->bind_param($types,...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    public function searchAndFilter($keyword='', $role='', $status='', $limit=null, $offset=null) {
        $sql="SELECT * FROM {$this->table} WHERE 1=1";
        $params=[]; $types="";

        if ($keyword) {
            $sql.=" AND (username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
            $k="%$keyword%";
            $params[]=$k; $params[]=$k; $params[]=$k;
            $types.="sss";
        }
        if ($role!=='') { $sql.=" AND role=?"; $params[]=$role; $types.="i"; }
        if ($status!=='') { $sql.=" AND status=?"; $params[]=$status; $types.="i"; }

        $sql.=" ORDER BY id DESC";
        if ($limit!==null) {
            $sql.=" LIMIT ? OFFSET ?";
            $params[]=$limit; $params[]=$offset;
            $types.="ii";
        }

        $stmt=$this->conn->prepare($sql);
        if ($types) $stmt->bind_param($types,...$params);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function updateProfile($id, $full_name, $gender, $phone, $address)
{
    $sql = "UPDATE users 
            SET full_name = ?, gender = ?, phone = ?, address = ?, updated_at = NOW()
            WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("sissi", $full_name, $gender, $phone, $address, $id);
    return $stmt->execute();
}

}
