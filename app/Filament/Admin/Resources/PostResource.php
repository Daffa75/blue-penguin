<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PostResource\Pages;
use App\Filament\Admin\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Infolists\Components;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\SpatieTagsColumn;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    public static function getNavigationGroup(): ?string
    {
        return (__('Website'));
    }

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->translateLabel()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                                Forms\Components\TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Post::class, 'slug', ignoreRecord: true),

                                Forms\Components\MarkdownEditor::make('article')
                                    ->fileAttachmentsDirectory('post/attachments')
                                    ->required()
                                    ->columnSpan('full'),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make(__('Image'))
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('image')
                                    ->image()
                                    ->imageEditor()
                                    ->imageResizeMode('contain')
                                    ->imageCropAspectRatio('16:9')
                                    ->collection('post/images')
                                    ->hiddenLabel()
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Option')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                    ])
                                    ->required()
                                    ->live(),

                                Forms\Components\DateTimePicker::make('published_at')
                                    ->seconds(false)
                                    ->timezone('Asia/Makassar')
                                    ->hidden(fn (Get $get) => $get('status') !== 'published'),

                                Forms\Components\Select::make('language')
                                    ->label(__("Language"))
                                    ->options([
                                        'id' => 'Bahasa Indonesia',
                                        'en' => 'English',
                                    ])
                                    ->searchable()
                                    ->required(),

                                SpatieTagsInput::make('tags'),
                            ])
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->collection('post/images')
                    ->label(__('Image')),

                Tables\Columns\TextColumn::make('title')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('status')
                    ->getStateUsing(fn (Post $record): string => $record->published_at?->isPast() ? 'Published' : ($record->published_at ? 'Pending' : 'Draft'))
                    ->sortable()
                    ->colors([
                        'success' => 'Published',
                        'warning' => 'Pending',
                        'info' => 'Draft',
                    ]),

                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('Published Date'))
                    ->sortable()
                    ->datetime()
                    ->timezone('Asia/Makassar'),

                    Tables\Columns\TextColumn::make('tags.name')
                    ->badge()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('published_at')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')
                            ->placeholder(fn ($state): string => now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('published_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['published_from'] ?? null) {
                            $indicators['published_from'] = 'Published from ' . Carbon::parse($data['published_from'])->toFormattedDateString();
                        }
                        if ($data['published_until'] ?? null) {
                            $indicators['published_until'] = 'Published until ' . Carbon::parse($data['published_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make()
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Group::make([
                    Components\Section::make()
                        ->schema([
                            Components\TextEntry::make('title')
                                ->translateLabel(),

                            Components\Split::make([
                                Components\Grid::make(['lg' => 3, 'md' => 2])
                                    ->schema([
                                        Components\Group::make([
                                            Components\TextEntry::make('updated_by.name')
                                                ->label(__('Last updated by')),
                                        ]),

                                        Components\Group::make([
                                            Components\TextEntry::make('created_by.name')
                                                ->label(__('Created by')),
                                        ]),

                                        Components\SpatieTagsEntry::make('tags'),
                                    ]),
                                Components\ImageEntry::make('image')
                                    ->hiddenLabel()
                                    ->grow(false),
                            ])->from('lg'),
                        ]),
                    Components\Section::make('Content')
                        ->schema([
                            Components\TextEntry::make('article')
                                ->prose()
                                ->markdown()
                                ->hiddenLabel(),
                        ])
                        ->collapsible(),
                ])
                    ->columnSpan(['lg' => 2]),

                Components\Group::make([
                    Components\Section::make()
                        ->schema([
                            SpatieMediaLibraryImageEntry::make('image')
                                ->hiddenLabel()
                                ->collection('post/images'),

                            Components\TextEntry::make('status')
                                ->badge()
                                ->getStateUsing(fn (Post $record): string => $record->published_at?->isPast() ? 'Published' : ($record->published_at ? 'Pending' : 'Draft'))
                                ->color(fn (string $state): string => match ($state) {
                                    'Published' => 'success',
                                    'Pending' => 'warning',
                                    'Draft' => 'info',
                                }),

                            Components\TextEntry::make('published_at')
                                ->label(__('Published Date'))
                                ->date('l, d M Y')
                                ->hidden(fn ($record) => !$record->status === 'published')
                        ]),
                ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(['lg' => 3]);
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
