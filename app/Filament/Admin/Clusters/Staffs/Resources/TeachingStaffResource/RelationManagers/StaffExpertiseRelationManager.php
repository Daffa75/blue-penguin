<?php

namespace App\Filament\Admin\Clusters\Staffs\Resources\TeachingStaffResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StaffExpertiseRelationManager extends RelationManager
{
    protected static string $relationship = 'staffExpertise';
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return (__('Staff Expertise'));
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('expertise_idn')
                ->label(false),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                ->preloadRecordSelect()
                ->form(fn (AttachAction $action): array => [
                    $action->getRecordSelect()
                        ->options(function () {
                            // Get all existing staff expertise records
                            return \App\Models\StaffExpertise::pluck('expertise_idn', 'id')->toArray();
                        })
                        ->searchable()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('expertise_idn')
                                ->label('Expertise in Indonesian')
                                ->translateLabel()
                                ->required()
                                ->maxLength(255)
                                ->unique('staff_expertises', 'expertise_idn', ignoreRecord: true),
                            Forms\Components\TextInput::make('expertise_en')
                                ->label('Expertise in English')
                                ->translateLabel()
                                ->required()
                                ->maxLength(255)
                                ->unique('staff_expertises', 'expertise_en', ignoreRecord: true),
                        ])
                        ->createOptionUsing(function (array $data) {
                            // Save the new expertise record and return its ID
                            return \App\Models\StaffExpertise::create($data)->id;
                        }),
                ])
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
