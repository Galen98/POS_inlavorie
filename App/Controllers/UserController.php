<?php
namespace App\Controllers;

use App\Database\Database;
use App\Models\Subscribe;
use App\Models\Profile;
use Flight;
use PDO;
use Rakit\Validation\Validator;

Flight::map('renderWithUser', function($template, $data = []) {
    $session = Flight::session();
    $data['csrf'] = Flight::session()->get('csrf_token');
    $data['substatus'] = Flight::get('substatus');
    $data['username'] = Flight::get('username');
    $data['expsub'] = Flight::get('expsub');
    $data['expiredAt'] = Flight::get('expiredAt');
    $data['module_resto_manage'] = Flight::get('module_resto_manage');
    $data['errors'] =  $session->getFlashOrDefault('errors', null);
    $data['success'] =  $session->getFlashOrDefault('success', null);
    $data['eror'] =  $session->getFlashOrDefault('eror', null);
    $session->commit();
    Flight::latte()->render($template, $data);
});


class UserController {
    //dashboard index
    public function index_dashboard() {
        Flight::renderWithUser('dashboard_page/index.latte', [
            'title' => 'inLavorie Resto - Dashboard'
        ]);
    }

    //subscribe plan
    public function index_subscribe() {
        $userId = Flight::get('userId');
        $data = Subscribe::getSub($userId);

        Flight::renderWithUser('dashboard_page/subscribtion_plan/index.latte', [
            'title' => 'inLavorie Resto - Berlangganan',
            'data' => $data 
        ]);
    }

    //pusat pengguna
    public function pusat_pengguna() {
        $userId = Flight::get('userId');
        $userData = Profile::getUsers($userId);
        $userProfile = Profile::getProfile($userId);

        Flight::renderWithUser('dashboard_page/pusat_pengguna/index.latte', [
            'title' => 'inLavorie Resto - Pusat Pengguna',
            'userData' => $userData,
            'userProfile' => $userProfile
        ]);
    }

    public function update_account() {
        $request = Flight::request();
        $userId = Flight::get('userId');
        $name = $request->data->name;
        $noHp = $request->data->noHp;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE users SET name = :name, noHp = :noHp WHERE id = :userId");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':noHp', $noHp);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function update_profile() {
        $db = Database::getInstance()->getConnection();
        $request = Flight::request();
        $userId = Flight::get('userId');
        $alamat_lengkap = $request->data->alamat_lengkap;
        $kode_pos = $request->data->kode_pos;
        $file = $request->files['profile_pic']; 
        if($file && $file['error'] === UPLOAD_ERR_OK){
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('profile_pic_', true) . '.' . $fileExtension;
        $filePath = "public/uploads/img/profile_pic/" . $fileName;
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $stmt = $db->prepare("UPDATE profile_users SET profile_pict = :profile_pict, alamat_lengkap = :alamat_lengkap, kode_pos = :kode_pos WHERE users_id = :users_id");
        $stmt->bindParam(':users_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':alamat_lengkap', $alamat_lengkap);
        $stmt->bindParam(':kode_pos', $kode_pos);
        $stmt->bindParam(':profile_pict', $fileName);
        $stmt->execute();
        return Flight::redirect('/dashboard/profile');
        }
        } else {
        $stmt = $db->prepare("UPDATE profile_users SET alamat_lengkap = :alamat_lengkap, kode_pos = :kode_pos WHERE users_id = :users_id");
        $stmt->bindParam(':users_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':alamat_lengkap', $alamat_lengkap);
        $stmt->bindParam(':kode_pos', $kode_pos);
        $stmt->execute();
        return Flight::redirect('/dashboard/profile');
        }
    }

    public function reset_password() {
        Flight::renderWithUser('dashboard_page/pusat_pengguna/reset_password.latte', [
            'title' => 'inLavorie Resto - Reset Password',
        ]);
    }

    public function update_password() {
        $validator = new Validator;
        $session = Flight::session();
        $request = Flight::request();
        $userId = Flight::get('userId');
        $data = Profile::getUsers($userId);
        $password = $data[0]['password'];
        $password_old = $request->data->password_old;
        $password_new = $request->data->password_new;
        $confirm_password = $request->data->confirm_password;

        $validation = $validator->make($_POST, [
            'password_new' => 'required|min:6',
            'password_old' => 'required|min:6',
            'confirm_password' => 'required|same:password_new',
        ]);

        $validation->setMessages([
            'password_new:required' => 'Password baru wajib diisi',
            'password_new:min' => 'Password minimal harus memiliki 6 karakter',
            'password_old:required' => 'Password lama wajib diisi',
            'password_old:min' => 'Password minimal harus memiliki 6 karakter',
            'confirm_password' => 'Konfirmasi password salah',
        ]);
        
        $validation->validate();

        if ($validation->fails()) {
            $errors = $validation->errors()->all();
            $session->setFlash('eror', $errors);
            $session->commit(); 
            return Flight::redirect('/dashboard/profile/reset-password');
        } else {
        if(!password_verify($password_old, $password)) {
            $session->setFlash('errors', 'Password lama salah');
            $session->commit();
            return Flight::redirect('/dashboard/profile/reset-password');
        } else {
            $passwordHashed = password_hash($password_new, PASSWORD_DEFAULT);
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE users SET password = :password_new WHERE id = :users_id");
            $stmt->bindParam(':users_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':password_new', $passwordHashed);
            $stmt->execute();
            $stmt = $db->prepare("UPDATE users SET Status = 0 WHERE id = :users_id");
            $stmt->bindParam(':users_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $session->del('jwt_token');
            $session->setFlash('success', 'Password berhasil diupdate!');
            $session->commit();
            return Flight::redirect('/login');
        }
    }
 }
}
?>