<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Route;
Route::get('/', function () {
   try {
      $employees = Employee::get();
      return view('employee', compact('employees'));
  } catch (\Exception $e) {
      dd('PostgreSQL Error: ' . $e->getMessage());
  }
});
Route::get('/employees/create', function () {
    return view('employees.create');
})->name('employees.create');

Route::get('/employees/add-default', [\App\Http\Controllers\EmployeeController::class, 'addDefault'])->name('employees.add-default');

Route::get('/employees/{employee}', function ($id) {
    $employee = \App\Models\Employee::findOrFail($id);
    return view('employees.show', compact('employee'));
})->name('employees.show');
 
Route::get('/employees/{employee}/edit', function ($id) {
    $employee = \App\Models\Employee::findOrFail($id);
    return view('employees.edit', compact('employee'));
})->name('employees.edit');
Route::delete('/employees/{employee}', function ($id) {
    $employee = \App\Models\Employee::findOrFail($id);
    $employee->delete();
    return redirect('/')->with('success', 'Employee deleted successfully!');
})->name('employees.destroy');


