<?php

namespace App\Filament\Admin\Resources\DepartmentEventResource\RelationManagers;

use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->icon(fn(Student $record) => $record->image_url ?: asset('assets/images/default_avatar.jpg')),
                Tables\Columns\TextColumn::make('role')
                    ->translateLabel()
                    ->badge()
                    ->colors([
                        'info' => 'Bachelor',
                        'success' => 'Master',
                    ])
                    ->formatStateUsing(fn(string $state): string => (__(ucfirst($state))))
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()->form(fn (AttachAction $action): array => [
                    $action->getRecordSelect(),
                    Forms\Components\Select::make('role')
                        ->default('bachelor')
                        ->translateLabel()
                        ->native(false)
                        ->options([
                            'bachelor' => (__('Bachelor')),
                            'master' => (__('Master')),
                        ])
                        ->required(),
                ])
                    ->modalHeading(__("Attach Student"))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
