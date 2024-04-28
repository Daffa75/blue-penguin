<?php

namespace App\Filament\Admin\Resources\DepartmentEventResource\RelationManagers;

use App\Models\Lecturer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LecturersRelationManager extends RelationManager
{
    protected static string $relationship = 'lecturers';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->translateLabel()
                    ->icon(fn (Lecturer $record) => $record->image_url ?: asset('assets/images/default_avatar.jpg')),
                Tables\Columns\TextColumn::make('role')
                    ->translateLabel()
                    ->sortable()
                    ->badge()
                    ->colors([
                        'info' => 'Head',
                        'success' => 'Staff',
                    ])
                    ->formatStateUsing(fn (string $state): string => (__(ucfirst($state))))
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()->preloadRecordSelect()->form(fn (AttachAction $action): array => [
                    $action->getRecordSelect(),
                    Forms\Components\Select::make('role')
                        ->translateLabel()
                        ->native(false)
                        ->options([
                            'Speaker' => (__('Speaker')),
                            'Examiner' => (__('Penguji')),
                            'Moderator' => (__('Moderator')),
                            'Participant' => (__('Participant')),
                        ]),
                ])
                    ->modalHeading(__("Attach Lecturer"))
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
