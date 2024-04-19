<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StudentResource\Pages;
use App\Filament\Admin\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            '' => $record->nim,
        ];
    }
    public static function getPluralLabel(): ?string
    {
        return (__('Student'));
    }

    protected static ?string $navigationIcon = 'phosphor-student-fill';
    public static function getNavigationGroup(): ?string
    {
        return (__('Management'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nim')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number'),
                Forms\Components\Section::make()
                ->heading(__('Related User'))
                ->translateLabel()
                ->collapsed()
                ->collapsible()
                ->description(__('If student have account, Link the account here'))
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label(__('User'))
                        ->searchable()
                        ->relationship('user', 'name')
                        ->options(
                            User::all()->pluck('name', 'id')
                        ),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->searchPlaceholder(__('Search Name and NIM'))
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->translateLabel()
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                        ->weight(FontWeight::Bold)
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('nim')
                        ->copyable()
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Medium)
                        ->weight(FontWeight::Medium)
                        ->icon('phosphor-identification-card-fill')
                        ->sortable()
                        ->searchable(),
                ])->space(2),

                Tables\Columns\TextColumn::make('email')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Medium)
                    ->icon('heroicon-m-envelope')
                    ->searchable(),
                Tables\Columns\Layout\Grid::make(),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->paginated([6, 12, 24, 48, 96, 'all'])
            ->defaultPaginationPageOption(12)
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
        ];
    }
}

