<?php

namespace App\Filament\Admin\Resources\LaboratoryResource\Pages;

use App\Filament\Admin\Resources\LaboratoryResource;
use App\Models\Student;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageLaboratoryStudents extends ManageRelatedRecords
{
    protected static string $resource = LaboratoryResource::class;

    protected static string $relationship = 'students';

    protected static ?string $navigationIcon = 'phosphor-student-fill';

    public static function getNavigationLabel(): string
    {
        return 'Students';
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
                    ->icon(fn(Student $record) => $record->image_url ?: asset('assets/images/default_avatar.jpg')),
                Tables\Columns\TextColumn::make('role')
                    ->sortable()
                    ->translateLabel()
                    ->badge()
                    ->colors([
                        'info' => 'bachelor',
                        'success' => 'magister',
                    ])
                    ->formatStateUsing(fn(string $state): string => (__(ucfirst($state))))
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                ->multiple()
                ->options([
                    'bachelor' => (__('Bachelor')),
                    'magister' => (__('Magister')),
                ]),
            ])
            ->headerActions([
                AttachAction::make()->form(fn(AttachAction $action): array => [
                    $action->getRecordSelect(),
                    Forms\Components\Select::make('role')
                        ->translateLabel()
                        ->native(false)
                        ->options([
                            'bachelor' => (__('Bachelor')),
                            'magister' => (__('Magister')),
                        ])
                        ->required(),
                ])
                    ->modalHeading(__("Attach Student"))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('role', 'asc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
