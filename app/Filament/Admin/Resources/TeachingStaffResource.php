<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Clusters\Staffs;
use App\Filament\Admin\Resources\TeachingStaffResource\Api\Transformers\TeachingStaffTransformer;
use App\Filament\Admin\Resources\TeachingStaffResource\Pages;
use App\Filament\Admin\Resources\TeachingStaffResource\RelationManagers;
use App\Models\Lecturer;
use App\Models\StaffRole;
use App\Models\TeachingStaff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use app\Filament\Admin\Clusters\Staffs\Resources\TeachingStaffResource\RelationManagers as StaffRelationManagers;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;

class TeachingStaffResource extends Resource
{
    protected static ?string $model = TeachingStaff::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Staffs::class;
    protected static ?int $navigationSort = 1;

    public static function getPluralLabel(): ?string
    {
        return __('Teaching Staff');
    }

    public static function getApiTransformer()
    {
        return TeachingStaffTransformer::class;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('lecturer_id')
                            ->label('Lecturer')
                            ->disabledOn(operations: 'edit')
                            ->translateLabel()
                            ->relationship('lecturer', 'name') // This ensures the name is shown
                            ->options(function () {
                                // Get all lecturer IDs that have already been assigned
                                $assignedLecturers = \App\Models\TeachingStaff::pluck('lecturer_id')->toArray();
                            
                                // Return lecturers that are not assigned, display lecturer name
                                return \App\Models\Lecturer::whereNotIn('id', $assignedLecturers)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\Select::make('concentration')
                            ->required()
                            ->translateLabel() 
                            ->options([
                                'Artificial Intelligence' => 'Artificial Intelligence',
                                'Cloud Computing' => 'Cloud Computing',
                                'Internet of Things' => 'Internet of Things'
                            ]),
                        Forms\Components\Select::make('role_id')
                            ->label('Position')
                            ->translateLabel()
                            ->relationship(name: 'role', titleAttribute: 'role_idn')
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('role_idn')
                                ->label('Position in Indonesian')
                                ->translateLabel()
                                ->required()
                                ->maxLength(length: 255),
                                Forms\Components\TextInput::make('role_en')
                                    ->label('Position in English')
                                    ->translateLabel()
                                    ->required()
                                    ->maxLength(255)
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->translateLabel()
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                    ]),

                    Forms\Components\Section::make('Tautan')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('handbook_link')
                        ->label('Handbook')
                        ->translateLabel()
                        ->maxLength(255),
                        Forms\Components\TextInput::make('scholar_link')
                        ->label('Google Scholar')
                        ->translateLabel()
                        ->maxLength(255),
                        Forms\Components\TextInput::make('scopus_link')
                        ->label('Scopus')
                        ->translateLabel()
                        ->maxLength(255),
                    ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('lecturer.image_url')
                    ->label('')
                    ->circular(),
                Tables\Columns\TextColumn::make('lecturer.name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('concentration')
                    ->translateLabel()
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role.role_idn')
                    ->label('Position')
                    ->translateLabel()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StaffRelationManagers\StaffExpertiseRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTeachingStaff::route('/'),
            'create' => Pages\CreateTeachingStaff::route('/create'),
            'view' => Pages\ViewTeachingStaff::route('/{record}'),
            'edit' => Pages\EditTeachingStaff::route('/{record}/edit'),
        ];
    }
    // protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    // public static function getRecordSubNavigation(Page $page): array
    // {
    //     return $page->generateNavigationItems([
    //         Pages\ViewTeachingStaff::class,
    //         Pages\EditTeachingStaff::class,
    //     ]);
    // }
}
