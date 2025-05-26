<?php
namespace App\Models;

class User {
    public static function all() {
        // contoh return dummy data
        return [
            ['id'=>1, 'name'=>'Insan'],
            ['id'=>2, 'name'=>'Developer'],
        ];
    }
}
