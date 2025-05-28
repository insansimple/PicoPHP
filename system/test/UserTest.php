<?php

use PHPUnit\Framework\TestCase;
use App\Models\User;
use System\Library\Database;

class UserTest extends TestCase
{
    public function testDataReturnsArray()
    {
        $users = new User();
        $this->assertIsArray($users->data());
    }

    public function testCountIsInt()
    {
        $users = new User();
        $this->assertIsInt($users->count());
    }

    public function testFindReturnsUser()
    {
        $users = new User();
        $user = $users->find(3);
        $this->assertIsArray($user);
        $this->assertArrayHasKey('id', $user);
    }

    public function testInsertUser()
    {
        $data_user = [
            'username' => 'testuser',
            'password' => 'testpassword',
        ];
        $db = Database::getInstance();
        $id_user = $db->insert('users', $data_user);
        $this->assertIsInt($id_user);
        $this->assertGreaterThan(0, $id_user);
    }

    public function testUpdateUser()
    {
        $data_user = [
            'username' => 'updateduser',
            'password' => 'updatedpassword',
        ];
        $db = Database::getInstance();
        $result = $db->update('users', $data_user, "id = 3");
        $this->assertIsInt($result);

        $result = $db->update('users', ['username' => 'updateuser'], "id = ?", [4]);
    }

    public function testDeleteUser()
    {
        $db = Database::getInstance();
        $result = $db->delete('users', "id = 3");
        $this->assertIsInt($result);
        $this->assertEquals(1, $result); // Assuming delete returns number of rows affected
    }
}
