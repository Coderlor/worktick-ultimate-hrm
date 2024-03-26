<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // Insert some stuff
	DB::table('permissions')->insert(
		array([
			'id'    => 1,
			'name'  => 'employee_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 2,
			'name'  => 'employee_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 3,
			'name'  => 'employee_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 4,
			'name'  => 'employee_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 5,
			'name'  => 'user_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 6,
			'name'  => 'user_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 7,
			'name'  => 'user_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 8,
			'name'  => 'user_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 9,
			'name'  => 'company_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 10,
			'name'  => 'company_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 11,
			'name'  => 'company_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 12,
			'name'  => 'company_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 13,
			'name'  => 'department_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 14,
			'name'  => 'department_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 15,
			'name'  => 'department_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 16,
			'name'  => 'department_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 17,
			'name'  => 'designation_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 18,
			'name'  => 'designation_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 19,
			'name'  => 'designation_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 20,
			'name'  => 'designation_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 21,
			'name'  => 'policy_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 22,
			'name'  => 'policy_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 23,
			'name'  => 'policy_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 24,
			'name'  => 'policy_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 25,
			'name'  => 'announcement_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 26,
			'name'  => 'announcement_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 27,
			'name'  => 'announcement_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 28,
			'name'  => 'announcement_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 29,
			'name'  => 'office_shift_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 30,
			'name'  => 'office_shift_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 31,
			'name'  => 'office_shift_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 32,
			'name'  => 'office_shift_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 33,
			'name'  => 'event_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 34,
			'name'  => 'event_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 35,
			'name'  => 'event_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 36,
			'name'  => 'event_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 37,
			'name'  => 'holiday_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 38,
			'name'  => 'holiday_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 39,
			'name'  => 'holiday_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 40,
			'name'  => 'holiday_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 41,
			'name'  => 'award_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 42,
			'name'  => 'award_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 43,
			'name'  => 'award_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 44,
			'name'  => 'award_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 45,
			'name'  => 'award_type',
			'guard_name'  => 'web',
		],
		[
			'id'    => 46,
			'name'  => 'complaint_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 47,
			'name'  => 'complaint_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 48,
			'name'  => 'complaint_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 49,
			'name'  => 'complaint_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 50,
			'name'  => 'travel_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 51,
			'name'  => 'travel_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 52,
			'name'  => 'travel_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 53,
			'name'  => 'travel_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 54,
			'name'  => 'arrangement_type',
			'guard_name'  => 'web',
		],
		[
			'id'    => 55,
			'name'  => 'attendance_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 56,
			'name'  => 'attendance_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 57,
			'name'  => 'attendance_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 58,
			'name'  => 'attendance_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 59,
			'name'  => 'account_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 60,
			'name'  => 'account_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 61,
			'name'  => 'account_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 62,
			'name'  => 'account_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 63,
			'name'  => 'deposit_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 64,
			'name'  => 'deposit_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 65,
			'name'  => 'deposit_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 66,
			'name'  => 'deposit_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 67,
			'name'  => 'expense_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 68,
			'name'  => 'expense_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 69,
			'name'  => 'expense_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 70,
			'name'  => 'expense_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 71,
			'name'  => 'client_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 72,
			'name'  => 'client_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 73,
			'name'  => 'client_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 74,
			'name'  => 'client_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 75,
			'name'  => 'deposit_category',
			'guard_name'  => 'web',
		],
		[
			'id'    => 76,
			'name'  => 'payment_method',
			'guard_name'  => 'web',
		],
		[
			'id'    => 77,
			'name'  => 'expense_category',
			'guard_name'  => 'web',
		],
		[
			'id'    => 78,
			'name'  => 'project_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 79,
			'name'  => 'project_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 80,
			'name'  => 'project_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 81,
			'name'  => 'project_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 82,
			'name'  => 'task_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 83,
			'name'  => 'task_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 84,
			'name'  => 'task_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 85,
			'name'  => 'task_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 86,
			'name'  => 'leave_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 87,
			'name'  => 'leave_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 88,
			'name'  => 'leave_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 89,
			'name'  => 'leave_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 90,
			'name'  => 'training_view',
			'guard_name'  => 'web',
		],
		[
			'id'    => 91,
			'name'  => 'training_add',
			'guard_name'  => 'web',
		],
		[
			'id'    => 92,
			'name'  => 'training_edit',
			'guard_name'  => 'web',
		],
		[
			'id'    => 93,
			'name'  => 'training_delete',
			'guard_name'  => 'web',
		],
		[
			'id'    => 94,
			'name'  => 'trainer',
			'guard_name'  => 'web',
		],
		[
			'id'    => 95,
			'name'  => 'training_skills',
			'guard_name'  => 'web',
		],
		[
			'id'    => 96,
			'name'  => 'settings',
			'guard_name'  => 'web',
		],
		[
			'id'    => 97,
			'name'  => 'currency',
			'guard_name'  => 'web',
		],
		[
			'id'    => 98,
			'name'  => 'backup',
			'guard_name'  => 'web',
		],
		[
			'id'    => 99,
			'name'  => 'group_permission',
			'guard_name'  => 'web',
		],
		[
			'id'    => 100,
			'name'  => 'attendance_report',
			'guard_name'  => 'web',
		],
		[
			'id'    => 101,
			'name'  => 'employee_report',
			'guard_name'  => 'web',
		],
		[
			'id'    => 102,
			'name'  => 'project_report',
			'guard_name'  => 'web',
		],
		[
			'id'    => 103,
			'name'  => 'task_report',
			'guard_name'  => 'web',
		],
		[
			'id'    => 104,
			'name'  => 'expense_report',
			'guard_name'  => 'web',
		],
		[
			'id'    => 105,
			'name'  => 'deposit_report',
			'guard_name'  => 'web',
		],
		[
			'id'    => 106,
			'name'  => 'employee_details',
			'guard_name'  => 'web',
		],
		[
			'id'    => 107,
			'name'  => 'leave_type',
			'guard_name'  => 'web',
		],
		[
			'id'    => 108,
			'name'  => 'project_details',
			'guard_name'  => 'web',
		],
		[
			'id'    => 109,
			'name'  => 'task_details',
			'guard_name'  => 'web',
		],
		[
			'id'    => 110,
			'name'  => 'module_settings',
			'guard_name'  => 'web',
		],
		[
			'id'    => 111,
			'name'  => 'kanban_task',
			'guard_name'  => 'web',
		])
	);
    }
}
