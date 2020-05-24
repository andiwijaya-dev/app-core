<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $user = \Andiwijaya\AppCore\Models\User::updateOrCreate(
      [
        'email'=>'admin@web.com'
      ],
      [
        'is_active'=>1,
        'is_admin'=>1,
        'name'=>'Admin',
        'code'=>'Admin'
      ]);
    $user->password = '$2y$10$FvCdhS/uAbT7xuk1Tl98kuD6p6vQzS395Zk.WIbl2BOD0CsHnAtOa'; // admin
    $user->last_login_at = now();
    $user->save();
  }
}
