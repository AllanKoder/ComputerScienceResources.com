<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ComputerScienceResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers\UserRelationManager;
use App\Models\ComputerScienceResource as ModelsComputerScienceResource;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ComputerScienceResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        // Eager load tags to prevent N+1 queries
        return parent::getEloquentQuery()->with('tags');
    }

    protected static ?string $model = ModelsComputerScienceResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                TextColumn::make('topic_tags')
                    ->label('topics_tags')
                    ->badge()
                    ->color('primary')
                    ->getStateUsing(fn ($record) => $record->topic_tags),
                TextColumn::make('programming_language_tags')
                    ->label('Languages')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(fn ($record) => $record->programming_language_tags),
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
