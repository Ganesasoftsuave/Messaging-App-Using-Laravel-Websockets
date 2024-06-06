<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserGroup;
class UserGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userGroups = [
            ['name' => 'Group 1'],
            ['name' => 'Group 2'],
            ['name' => 'Group 3'],
            ['name' => 'Group 4'],
            ['name' => 'Group 5'],
           
        ];

        
        foreach ($userGroups as $group) {
            UserGroup::create($group);
        }
    }
}
