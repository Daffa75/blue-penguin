<?php

namespace App\Filament\Admin\Resources\StructureResource\RelationManager;

use App\Models\Curriculum\Module;
use App\Models\Curriculum\Semester;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SemesterRelationManager extends RelationManager
{
    protected static string $relationship = 'semester';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('semester_name')
                    ->label(__('Semester Name'))
                    ->required()
                    ->maxLength(64),
                Forms\Components\TextInput::make('credit_total')
                    ->label(__('Credit Total'))
                    ->required()
                    ->numeric()
                    ->maxValue(40),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('semester_name')
            ->columns([
                Tables\Columns\TextColumn::make('semester_name')
                    ->label(__('Semester Name')),
                Tables\Columns\TextColumn::make('credit_total')
                    ->label(__('Credit Total')),
                Tables\Columns\TextColumn::make('Total Mata Kuliah')
                    ->label(__('Total Mata Kuliah'))
                    ->state(function (?Semester $record) {
                        $modulesCount = Module::where('semester_id', '=', $record->id)->count();
                        return $modulesCount;
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
