<?php

namespace App\Filament\Lecturer\Resources\FinalProjectResource\RelationManagers;

use App\Models\Lecturer;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class LecturersRelationManager extends RelationManager
{
    protected static string $relationship = 'lecturers';
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return (__('Lecturers'));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('role', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->icon(function (Lecturer $record) {
                        if (!$record->image_url)
                            return \asset('assets/images/default_avatar.jpg');
                        elseif (!empty(parse_url($record->image_url)['scheme']))
                            return $record->image_url;
                        elseif (empty(parse_url($record->image_url)['scheme']))
                            return 'https://eng.unhas.ac.id/siminformatika'.$record->image_url;
                        return \asset('assets/images/default_avatar.jpg');
                    }),
                Tables\Columns\TextColumn::make('role')
                    ->translateLabel()
                    ->badge()
                    ->colors([
                        'info' => 'supervisor 1',
                        'success' => 'supervisor 2',
                        'violet' => 'evaluator'
                    ])
                    ->formatStateUsing(fn (string $state): string => (__(ucfirst($state))))
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()->form(fn (AttachAction $action): array => [
                    $action->getRecordSelect(),
                    Forms\Components\Select::make('role')
                        ->translateLabel()
                        ->native(false)
                        ->options([
                            'supervisor 1' => (__('Supervisor 1')),
                            'supervisor 2' => (__('Supervisor 2')),
                            'evaluator' => (__('Evaluator'))
                        ])
                        ->required(),
                ])
                    ->modalHeading(__("Attach Lecturer"))

            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                AttachAction::make()->form(fn (AttachAction $action): array => [
                    $action->getRecordSelect(),
                    Forms\Components\Select::make('role')
                        ->translateLabel()
                        ->native(false)
                        ->options([
                            'supervisor 1' => 'Supervisor 1',
                            'supervisor 2' => 'Supervisor 2',
                            'evaluator' => 'Evaluator'
                        ])
                        ->required(),
                ])
            ]);
    }
}
