<?php
namespace App\Controllers\ModuleController;
use Exception;
use Flight;
use App\Database\Database;
use PDO;
use Ghostff\Session\Session;
use App\Models\Module\RestoManage\RestoMaster;
use Rakit\Validation\Validator;
use App\ActionsModule\App\RestoManage\SaveResto;

Flight::map('renderWithUser', function($template, $data = []) {
    $session = Flight::session();
    $data['subscribeId'] = Flight::get('subscribeId');
    $data['username'] = Flight::get('username');
    $data['userId'] = Flight::get('userId');
    $data['module_resto_manage'] = Flight::get('module_resto_manage');
    $data['errors'] =  $session->getFlashOrDefault('errors', null);
    $data['success'] =  $session->getFlashOrDefault('success', null);
    $data['eror'] =  $session->getFlashOrDefault('eror', null);
    $session->commit();
    Flight::latte()->render($template, $data);
});

class RestoManageController {
    //daftar resto
    public function list_daftar_resto() {
        Flight::renderWithUser('dashboard_page/resto_manage/daftar_resto.latte', [
            'title' => 'inLavorie Resto - Daftar Resto',
            'list' => RestoMaster::getList()
        ]);
    }

    //get resto limit
    public function resto_limit() {
        $userId = Flight::get('userId');
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT subscribe_id FROM subcribed_users WHERE users_id = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $getSubscribe = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT COUNT(*) FROM resto_masters WHERE users_id = :userId ");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $countResto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ((int)$getSubscribe === 1 && (int)$countResto < 4) {
            Flight::json(['status' => 'success', 'data' => true]);
        } else if((int)$getSubscribe === 2 && (int)$countResto < 4) {
            Flight::json(['status' => 'success', 'data' => true]);
        } else if((int)$getSubscribe === 3 && (int)$countResto < 2) {
            Flight::json(['status' => 'success', 'data' => true]);
        } else {
            Flight::json(['status' => 'error', 'data' => false], 404);
        }
    }

    public function add_tambah_resto() {
        $session = Flight::session(); 
        $userId = Flight::get('userId');
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT subscribe_id FROM subcribed_users WHERE users_id = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        $getSubscribe = (int)$results['subscribe_id'];

        $stmt = $db->prepare("SELECT COUNT(*) FROM resto_masters WHERE users_id = :userId ");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $countResto = (int)$result['COUNT(*)'];

        if ($getSubscribe === 1 && $countResto < 3) {
            Flight::renderWithUser('dashboard_page/resto_manage/tambah_resto.latte', [
                'title' => 'inLavorie Resto - Add Resto Page'              
            ]);
        } else if($getSubscribe === 2 && $countResto < 3) {
            Flight::renderWithUser('dashboard_page/resto_manage/tambah_resto.latte', [
                'title' => 'inLavorie Resto - Add Resto Page'               
            ]);
        } else if($getSubscribe === 3 && $countResto < 1) {
            Flight::renderWithUser('dashboard_page/resto_manage/tambah_resto.latte', [
                'title' => 'inLavorie Resto - Add Resto Page'               
            ]);
        } else {
            $session->setFlash('errors', 'Jumlah resto sudah mencapai batas.');
            $session->commit(); 
            Flight::redirect('/dashboard/daftar-resto');
        }
    }

    //post resto
    public function post_resto() {
        $session = Flight::session();
        $request = Flight::request();
        $nama_resto = $request->data->nama_resto;
        $alamat = $request->data->alamat;
        $keterangan = $request->data->keterangan;
        $contact = $request->data->contact;
        $file = $request->files['thumbnails']; 
        $validator = new Validator;
        $validation = $validator->make($_POST, [
            'nama_resto' => 'required',
            'alamat' => 'required',
            'contact' => 'required|numeric'
        ]);

        $validation->setMessages([
            'nama_resto:required' => 'Nama Resto wajib diisi.',
            'alamat:required' => 'Alamat wajib diisi.',
            'contact:numeric' => 'Nomor Kontak harus berupa angka.',
            'contact:required' => 'Nomor Kontak wajib diisi.'
        ]);

        $validation->validate();
        if ($validation->fails()) {
            $errors = $validation->errors()->all();
            $session->setFlash('eror', $errors);
            $session->commit(); 
            return Flight::redirect('/dashboard/add-daftar-resto');
        } else {
            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileName = uniqid('thumbnail_', true) . '.' . $fileExtension;
                $filePath = "public/uploads/img/resto_photo/" . $fileName;
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    SaveResto::execute([
                        'nama_resto' => $nama_resto,
                        'alamat' => $alamat,
                        'keterangan' => $keterangan,
                        'thumbnails' => $fileName,
                        'contact' => $contact
                    ]);
                    $session->setFlash('success', 'Penambahan data resto berhasil.');
                    $session->commit();
                    return Flight::redirect('/dashboard/daftar-resto');
                }
            } else {
                SaveResto::execute([
                    'nama_resto' => $nama_resto,
                    'alamat' => $alamat,
                    'thumbnails' => null,
                    'keterangan' => $keterangan,
                    'contact' => $contact
                ]);
                $session->setFlash('success', 'Penambahan data resto berhasil.');
                $session->commit();
                return Flight::redirect('/dashboard/daftar-resto');
            }
        }
    }

    //view resto
    public function view_resto($id){
        $session = Flight::session();
        $itemresto = RestoMaster::getById($id);
        if($itemresto) {
        Flight::renderWithUser('dashboard_page/resto_manage/view_resto.latte', [
            'title' => 'inLavorie Resto - Lihat Resto',
            'itemresto' => $itemresto
        ]);
    } else {
        $session->setFlash('errors', 'Item tidak tersedia.');
        $session->commit();
        Flight::redirect('/dashboard/daftar-resto');
    }
    }


    // $url = 'https://cataas.com/api/cats?tags=cute';
    // $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_HEADER, false)
    // $response = curl_exec($ch);
    // if(curl_errno($ch)) {
    //     echo 'Error:' . curl_error($ch);
    // }
    // curl_close($ch);
    // Flight::json(json_decode($response, true));
}