<?php
namespace App\ActionsModule\App\RestoManage;

use App\Models\Module\RestoManage\RestoMaster;
use Exception;

class SaveResto {
    public static function execute(array $data) {
        try {
            $resto = new RestoMaster(null, $data['nama_resto'], $data['alamat'], $data['keterangan'], $data['thumbnails'], $data['contact']);
            $resto->save();
            return true;
        } catch(Exception $e) {
            throw $e;
        }
    }
}
