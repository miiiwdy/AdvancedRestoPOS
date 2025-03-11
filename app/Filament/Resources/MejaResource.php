<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MejaResource\Pages;
use App\Filament\Resources\MejaResource\RelationManagers;
use App\Models\KategoriMeja;
use App\Models\Meja;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MejaResource extends Resource
{
    protected static ?string $model = Meja::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $activeNavigationIcon = 'heroicon-s-rectangle-group';
    protected static ?string $navigationLabel = 'Data Meja';
    protected static ?string $navigationGroup = 'Master Data';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kategori_mejas_id')
                    ->label('Kategori Meja')
                    ->required()
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search): array => KategoriMeja::where('nama_kategori_meja', 'like', "%{$search}%")
                        ->limit(50)
                        ->pluck('nama_kategori_meja', 'id')
                        ->toArray())
                    ->getOptionLabelUsing(fn($value): ?string => KategoriMeja::find($value)?->nama_kategori_meja)
                    ->reactive(),
                Forms\Components\TextInput::make('nomor_meja')
                    ->maxLength(255),
                Forms\Components\TextInput::make('guest')
                    ->maxLength(255),
                Forms\Components\TextInput::make('time_used'),
                Forms\Components\Toggle::make('is_available')
                ->default(true),
                Forms\Components\Toggle::make('is_served'),
                Forms\Components\Toggle::make('is_reserved'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kategoriMeja.nama_kategori_meja')
                    ->label('Kategori Meja')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nomor_meja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guest')
                    ->searchable(),
                Tables\Columns\TextColumn::make('time_used'),
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_served')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_reserved')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMejas::route('/'),
            'create' => Pages\CreateMeja::route('/create'),
        ];
    }
}
