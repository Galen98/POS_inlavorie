<?php 
namespace App\Actions\Contact;

use App\Models\ContactUs;

class GetAllContact {
    public static function execute() {
        return ContactUs::getAll();
    }
}
?>