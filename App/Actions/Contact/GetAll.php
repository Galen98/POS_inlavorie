<?php 
namespace App\Actions\Contact;

use App\Models\ContactUs;

class GetAll {
    public static function execute() {
        return ContactUs::getAll();
    }
}
?>