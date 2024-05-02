<?php 

namespace App\Imports;

use App\Models\Curriculum\Semester as CurriculumSemester;
use App\Models\Curriculum\Module;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SemesterImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $semester = CurriculumSemester::firstOrCreate([
                'semester_name' => $row['semester_name']
            ]);

            $module = Module::firstOrCreate([
                'module_code' => $row['module_code'],
                'module_name' => $row['module_name'],
                'credit_points' => $row['credit_points']
            ]);

            $semester->modules()->attach($module->id);

            $semester->update([
                'semester_credit_total' => $semester->modules()->sum('credit_points')
            ]);
        }
    }
}
