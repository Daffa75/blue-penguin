<?php

namespace App\Filament\Lecturer\Resources\PublicationResource\RelationManagers;

use Filament\Facades\Filament;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __("Students");
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('nim')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                ->modalHeading(__('Attach Student'))
                ->disabled(fn () => Filament::getCurrentPanel()->getId() === 'student'),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                ->disabled(fn () => Filament::getCurrentPanel()->getId() === 'student'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\AttachAction::make(),
            ]);
    }
}
