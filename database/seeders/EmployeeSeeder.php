<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            [
                'id'              => 1,
                'employee_number' => '2025-0001',
                'user_id'         => 1,
                'department_id'   => 1,
                'position_id'     => 1,
                'manager_id'      => null,
                'first_name'      => 'Jroi',
                'middle_name'     => null,
                'last_name'       => 'Acosta',
                'email'           => 'zhanroi18@gmail.com',
                'hire_date'       => '2025-06-16',
            ],
            [
                'id'              => 2,
                'employee_number' => '101',
                'department_id'   => 2,
                'position_id'     => 2,
                'first_name'      => 'Maricel',
                'middle_name'     => 'A',
                'last_name'       => 'Escarda',
                'email'           => 'maricel@gmail.com',
                'hire_date'       => '2025-06-17',
            ],
            [
                'id'              => 3,
                'employee_number' => '102',
                'department_id'   => 2,
                'position_id'     => 4,
                'manager_id'      => 2,
                'first_name'      => 'Jan Carlo Greg',
                'middle_name'     => 'O',
                'last_name'       => 'Regino',
                'email'           => 'jancarlo@gmail.com',
                'hire_date'       => '2025-06-17',
            ],
            [
                'id'              => 4,
                'employee_number' => '103',
                'department_id'   => 2,
                'position_id'     => 4,
                'manager_id'      => 2,
                'first_name'      => 'Mary Grace',
                'middle_name'     => 'D',
                'last_name'       => 'Victorio',
                'email'           => 'grace@gmail.com',
                'hire_date'       => '2025-06-17',
            ],
            [
                'id'              => 5,
                'employee_number' => '204',
                'department_id'   => 2,
                'position_id'     => 3,
                'first_name'      => 'Christienne Angela',
                'middle_name'     => 'A',
                'last_name'       => 'Escarda',
                'email'           => 'tiene@gmail.com',
                'hire_date'       => '2025-06-17',
            ],
            [
                'id'              => 6,
                'employee_number' => '231',
                'department_id'   => 3,
                'position_id'     => 3,
                'first_name'      => 'Percival',
                'middle_name'     => 'M',
                'last_name'       => 'Escarda',
                'email'           => 'percival@gmail.com',
                'hire_date'       => '2025-06-17',
            ],
            [
                'id'              => 7,
                'employee_number' => '232',
                'department_id'   => 3,
                'position_id'     => 5,
                'first_name'      => 'Roesmarie',
                'middle_name'     => 'P',
                'last_name'       => 'Tuddao',
                'email'           => 'rose@gmail.com',
                'hire_date'       => '2025-06-17',
            ],
            [
                'id'              => 8,
                'employee_number' => '11',
                'department_id'   => 4,
                'position_id'     => 6,
                'first_name'      => 'Rodolfo',
                'middle_name'     => 'B',
                'last_name'       => 'Magnigno',
                'suffix'          => 'JR',
                'email'           => 'rodolfo@gmail.com',
                'hire_date'       => '2025-06-17',
            ],
            [
                'id'              => 9,
                'employee_number' => '111',
                'department_id'   => 4,
                'position_id'     => 6,
                'first_name'      => 'Welliejon',
                'middle_name'     => 'A',
                'last_name'       => 'Bornea',
                'email'           => 'Welliejon@gmail.com',
                'hire_date'       => '2025-06-17',
            ],
            [
                'id'              => 11,
                'employee_number' => '126',
                'department_id'   => 4,
                'position_id'     => 6,
                'first_name'      => 'Reymark',
                'middle_name'     => 'A',
                'last_name'       => 'Galanta',
                'email'           => 'reymark@gmail.com',
                'hire_date'       => '2025-06-17',
            ],
        ];

        foreach ($rows as $row) {
            Employee::updateOrCreate(
                ['id' => $row['id']],
                array_merge([
                    // default / nullable columns
                    'division_id'          => null,
                    'prefix'               => $row['prefix']      ?? null,
                    'suffix'               => $row['suffix']      ?? null,
                    'professional_title'   => $row['professional_title'] ?? null,
                    'phone'                => null,
                    'address'              => null,
                    'street'               => null,
                    'zipcode'              => null,
                    'birth_date'           => null,
                    'termination_date'     => null,
                    'gender'               => null,
                    'marital_status'       => null,
                    'salary'               => null,
                    'employment_type'      => null,
                    'job_status'           => null,
                    'emergency_contact_name'     => null,
                    'emergency_contact_phone'    => null,
                    'emergency_contact_relation' => null,
                    'tax_id'               => null,
                    'social_security_no'   => null,
                    'philhealth_no'        => null,
                    'pagibig_no'           => null,
                    'is_active'            => true,
                    'updated_at'           => null,
                    'deleted_at'           => null,
                ], $row, [
                    'created_at' => Carbon::parse('2025-06-16 15:31:00')->addSeconds($row['id']),
                ])
            );
        }
    }
}
