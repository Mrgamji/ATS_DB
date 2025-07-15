<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function addDefault()
    {
      
        $employee = \App\Models\Employee::create([
           
            'first_name' => 'Haladu',
            'last_name' => 'Salisu',
            'photo' => null,
            'email' => 'employee23@example.com',
            'phone' => '08038624730',
            'address' => 'Kano',
            'emergency_contact_name' => null,
            'emergency_contact_phone' => null,
            'designation' => 'Staff',
            'department' => 'General',
            'manager_id' => null,
            'employment_type' => 'full-time',
            'date_of_joining' => now(),
            'employee_code' => 'EMP' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'age' => null,
            'date_of_birth' => null,
            'role' => 'employee',
        ]);
        return redirect('/')->with('success', 'Default employee added!');
    }
}
