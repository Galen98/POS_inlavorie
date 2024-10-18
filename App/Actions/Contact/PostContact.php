<?php 
namespace App\Actions\Contact;

use App\Models\ContactUs;
use Exception;

class PostContact {
    public static function execute(array $data){
        try {
            $data = new ContactUs(null, $data['name'], $data['email'], $data['message']);
            $data->save();
            return true;
        } catch(Exception $e) {
            throw $e;
        }
    }
}
?>