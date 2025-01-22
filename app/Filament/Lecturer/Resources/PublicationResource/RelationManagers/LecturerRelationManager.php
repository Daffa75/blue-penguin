<?php

namespace App\Filament\Lecturer\Resources\PublicationResource\RelationManagers;

use App\Models\Lecturer;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LecturerRelationManager extends RelationManager
    {
    protected static string $relationship = 'lecturers';
    protected static ?string $title = 'Authors';
    public static function getTitle(Model $ownerRecord, string $pageClass): string
        {
        return (__("Research Team"));
        }

    public function table(Table $table): Table
        {
        return $table
            ->recordTitleAttribute(attribute: 'name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->icon(function (Lecturer $record) {
                        if (!$record->image_url)
                            return \asset('assets/images/default_avatar.jpg');
                        elseif (!empty (parse_url($record->image_url)['scheme']))
                            return $record->image_url;
                        elseif (empty (parse_url($record->image_url)['scheme']))
                            return 'https://eng.unhas.ac.id/siminformatika' . $record->image_url;
                        return \asset('assets/images/default_avatar.jpg');
                        }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->modalHeading(__('Attach Lecturer'))
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
                Tables\Actions\AttachAction::make(),
            ]);
        }
    }