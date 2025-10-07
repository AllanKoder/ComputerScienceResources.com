<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ComputerScienceResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers\UserRelationManager;
use App\Models\ComputerScienceResource as ModelsComputerScienceResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ComputerScienceResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        // Eager load tags to prevent N+1 queries
        return parent::getEloquentQuery()->with('tags');
    }

    protected static ?string $model = ModelsComputerScienceResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('slug')
                    ->maxLength(255),

                Forms\Components\TextInput::make('page_url')
                    ->maxLength(255),

                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),

                Forms\Components\CheckboxList::make('difficulties')
                    ->label('Difficulties')
                    ->options(fn () => array_combine(config('computerScienceResource.difficulties'), config('computerScienceResource.difficulties')))
                    ->columns(2)
                    ->required(),

                Forms\Components\TagsInput::make('topics_tags')
                    ->label('Topic Tags')
                    ->disabled()
                    ->helperText('These are computed from relationships'),

                Forms\Components\TagsInput::make('programming_languages_tags')
                    ->label('Programming Language Tags')
                    ->disabled()
                    ->helperText('These are computed from relationships'),

                Forms\Components\TagsInput::make('general_tags')
                    ->label('General Tags')
                    ->disabled()
                    ->helperText('These are computed from relationships'),

                // Editable date fields
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Created Date')
                    ->seconds(false),

                Forms\Components\DateTimePicker::make('updated_at')
                    ->label('Updated Date')
                    ->seconds(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->searchable()
                    ->description(fn (ModelsComputerScienceResource $resource): string => $resource->slug)->wrap(),
                TextColumn::make('user.name')
                    ->description(fn (ModelsComputerScienceResource $resource): string => $resource->user->id)->wrap(),
                TextColumn::make('name')->searchable()
                    ->description(fn (ModelsComputerScienceResource $resource): string => $resource->description)->wrap(),
                TextColumn::make('page_url')->searchable()->wrap()->copyable(),
                TextColumn::make('topics_tags')
                    ->label('topics_tags')
                    ->badge()
                    ->color('primary')
                    ->getStateUsing(fn ($record) => $record->topics_tags),
                TextColumn::make('programming_languages_tags')
                    ->label('Languages')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(fn ($record) => $record->programming_languages_tags),
                TextColumn::make('general_tags')
                    ->label('General Tags')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(fn ($record) => $record->general_tags),
            ])
            ->filters([
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Custom delete action that uses model delete() method
                DeleteAction::make()
                    ->action(function (ModelsComputerScienceResource $record) {
                        $record->forceDelete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Custom bulk delete action that uses model delete() method
                    BulkAction::make('delete')
                        ->label('Delete selected')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            // Loop through each record and call delete() individually
                            // This ensures all model events and custom logic are triggered
                            $records->each(function ($record) {
                                $record->delete();
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UserRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComputerSciences::route('/'),
            'edit' => Pages\EditComputerScience::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
