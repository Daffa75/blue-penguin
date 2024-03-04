<?php

namespace App\Filament\Admin\Resources\CurriculumStructure\StructureResource\RelationManagers;

use App\Filament\Admin\Resources\CurriculumStructure\SemesterResource;
use App\Models\Curriculum\Semester;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    -> url(fn (Semester $record): string => SemesterResource::getUrl('edit', [$record])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
