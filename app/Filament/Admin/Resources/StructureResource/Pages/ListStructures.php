<?php

namespace App\Filament\Admin\Resources\StructureResource\Pages;

use App\Actions\FilamentImport\Action\ImportAction;
use App\Filament\Admin\Resources\StructureResource;
use App\Models\Curriculum\CurriculumStructure;
use App\Models\Curriculum\Module;
use App\Models\Curriculum\Semester;
use Carbon\Carbon;
use Coolsam\FilamentExcel\Actions\ImportField;
use Filament\Resources\Components\Tab;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStructures extends ListRecords
{
    protected static string $resource = StructureResource::class;

    public function getTabs(): array
    {
        return [
            'Sarjana' => Tab::make()->query(fn ($query) => $query->where('website', 'bachelor')),
            'Magister' => Tab::make()->query(fn ($query) => $query->where('website', 'master')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // ImportAction::make()
            //     ->fields([
            //         ImportField::make('curriculum_name'),
            //         ImportField::make('semester_name'),
            //         ImportField::make('credit_total'),

            //         ImportField::make('module_code'),
            //         ImportField::make('module_name'),
            //         ImportField::make('credit_points'),
            //     ])
            //     ->handleRecordCreation(function ($data) {
            //         $keysToAdd = [
            //             'curriculum_name',
            //             'semester_name',
            //             'credit_total',
            //         ];

            //         $modulesList = [
            //             $data['module_code'],
            //             $data['module_name'],
            //             $data['credit_points'],
            //         ];

            //         $newData = [];
            //         // Loop through the keys to add
            //         foreach ($keysToAdd as $key) {
            //             // Check if the key exists in the original array
            //             if (array_key_exists($key, $data)) {
            //                 // If it exists, copy it to the new array
            //                 $newData[$key] = $data[$key];
            //             } else {
            //                 // If it doesn't exist, add it with a default value of null
            //                 $newData[$key] = null;
            //             }
            //         }

            //         $curriculumId = CurriculumStructure::where('id', 'like', "%{$data['curriculum_name']}%")->get()->first()->id;
            //         $newData['curriculum_id'] = $curriculumId;

            //         $newSemester = function () use ($newData) {
            //             $isSemesterFound = Semester::query()
            //                 ->where('curriculum_id', '=', $newData['curriculum_id']);
            //             if ()
            //             $semester = Semester::create($newData);

            //             return $semester;
            //         };

            //         $newModule = function () use ($newData) {

            //         };

            //         $addNew = function () use ($newData, $newModule) {
            //             $newData();
            //             $newModule();
            //         };
            //         return $addNew();
            //     }),
        ];
    }
}
