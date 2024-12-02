<?php 
namespace App\Models\Module\RestoManage;

use App\Database\Database;
use Flight;
use PDO;
use React\EventLoop\Factory;
use React\MySQL\Factory as MySQLFactory;
use React\Promise\Promise;


class RestoMaster {
    private $nama_resto;
    private $alamat;
    private $keterangan;
    private $thumbnails;
    private $contact;

    public function __construct($id, $nama_resto, $alamat, $keterangan, $thumbnails, $contact)
    {
        $this->nama_resto = $nama_resto;
        $this->alamat = $alamat;
        $this->keterangan = $keterangan;
        $this->thumbnails = $thumbnails;
        $this->contact = $contact;
    }

    public function save() {
        $userId = Flight::get('userId');
        $uid = uniqid();
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO resto_masters (uid, users_id, nama_resto, alamat, thumbnails, keterangan, contact, status) VALUES (:uid, :userId, :nama_resto, :alamat, :thumbnails, :keterangan, :contact, 1)");
        $stmt->bindParam(':uid', $uid);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':nama_resto', $this->nama_resto);
        $stmt->bindParam(':alamat', $this->alamat);
        $stmt->bindParam(':thumbnails', $this->thumbnails);
        $stmt->bindParam(':keterangan', $this->keterangan);
        $stmt->bindParam(':contact', $this->contact);

        $stmt->execute();
    }

    public static function getList() {
        $userId = Flight::get('userId');
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, nama_resto, thumbnails, status FROM resto_masters WHERE users_id = :userId");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id, nama_resto, thumbnails, alamat, keterangan, contact, status FROM resto_masters WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id) {

    }
}
?>