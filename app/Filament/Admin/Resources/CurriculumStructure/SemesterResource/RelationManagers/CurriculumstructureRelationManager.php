<?php

namespace App\Filament\Admin\Resources\CurriculumStructure\SemesterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CurriculumstructureRelationManager extends RelationManager
{
    protected static string $relationship = 'curriculumStructure';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->label('Curriculum ID')
                    ->disabled(),
                    Forms\Components\TextInput::make('curriculum_name')
                    ->label('Curriculum Name')
                    ->required()
                    ->maxLength(64),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Curriculum ID'),
                Tables\Columns\TextColumn::make('curriculum_name')->label('Curriculum Name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
