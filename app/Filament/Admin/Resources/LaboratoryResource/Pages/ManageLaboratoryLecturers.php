<?php

namespace App\Filament\Admin\Resources\LaboratoryResource\Pages;

use App\Filament\Admin\Resources\LaboratoryResource;
use App\Models\Lecturer;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageLaboratoryLecturers extends ManageRelatedRecords
{
    protected static string $resource = LaboratoryResource::class;

    protected static string $relationship = 'lecturers';

    protected static ?string $navigationIcon = 'phosphor-chalkboard-teacher';

    public static function getNavigationLabel(): string
    {
        return __('Lecturers');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->translateLabel()
                    ->sortable()
                    ->icon(fn(Lecturer $record) => $record->image_url ?: asset('assets/images/default_avatar.jpg')),
                Tables\Columns\TextColumn::make('role')
                    ->translateLabel()
                    ->badge()
                    ->colors([
                        'info' => 'head',
                        'success' => 'staff',
                    ])
                    ->formatStateUsing(fn(string $state): string => (__(ucfirst($state))))
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()->form(fn(AttachAction $action): array => [
                    $action->getRecordSelect(),
                    Forms\Components\Select::make('role')
                        ->translateLabel()
                        ->native(false)
                        ->options([
                            'head' => (__('Head')),
                            'staff' => (__('Staff')),
                        ])
                        ->required(),
                ])
                    ->modalHeading(__("Attach Lecturer"))
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
            ])
            ->defaultSort('role', 'asc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                ]),
            ]);
    }
}
