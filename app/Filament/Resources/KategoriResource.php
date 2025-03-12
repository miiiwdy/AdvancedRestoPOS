<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriResource\Pages;
use App\Filament\Resources\KategoriResource\RelationManagers;
use App\Models\Kategori;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\ColorPicker;
use Illuminate\Support\Facades\Auth;

class KategoriResource extends Resource
{
    protected static ?string $model = Kategori::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $activeNavigationIcon = 'heroicon-s-queue-list';
    protected static ?string $navigationLabel = 'Kategori Produk';
    protected static ?string $navigationGroup = 'Category';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('restos_id')
                    ->default(Auth::user()->restos_id),
                Forms\Components\Hidden::make('outlets_id')
                    ->default(Auth::user()->outlets_id),
                Forms\Components\TextInput::make('icon')
                    ->required()
                    ->placeholder('ambil kode icon di https://remixicon.com/')
                    ->maxLength(255),
                Forms\Components\ColorPicker::make('warna_teks_kategori')
                    ->placeholder('gunakan warna gelap')
                    ->required(),
                Forms\Components\ColorPicker::make('warna_background_kategori')
                    ->placeholder('gunakan warna yang lebih terang')
                    ->required(),
                Forms\Components\TextInput::make('nama_kategori')
                    ->placeholder('masukkan nama kategori')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
           ->poll('5s')
            ->columns([
                Tables\Columns\TextColumn::make('icon')
                    ->searchable(),
                Tables\Columns\ColorColumn::make('warna_teks_kategori')
                    ->copyable()
                    ->copyableState(fn (string $state): string => "Color: {$state}")
                    ->searchable(),
                Tables\Columns\ColorColumn::make('warna_background_kategori')
                    ->copyable()
                    ->copyableState(fn (string $state): string => "Color: {$state}")
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_kategori')
                    ->searchable(),
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
            'index' => Pages\ListKategoris::route('/'),
            'create' => Pages\CreateKategori::route('/create'),
        ];
    }
}
