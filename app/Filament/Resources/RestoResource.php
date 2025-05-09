<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RestoResource\Pages;
use App\Filament\Resources\RestoResource\RelationManagers;
use App\Models\Resto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class RestoResource extends Resource
{
    protected static ?string $model = Resto::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $activeNavigationIcon = 'heroicon-s-building-storefront';
    protected static ?string $navigationLabel = 'Management Resto';
    protected static ?string $navigationGroup = 'F&B Core';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_resto')
                    ->required()
                    ->placeholder('Nama Resto')
                    ->maxLength(255),
                Forms\Components\TextInput::make('deskripsi')
                    ->placeholder('Deskripsi Resto')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('alamat')
                    ->placeholder('Alamat Resto')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = Resto::query();
                if (Auth::user()->hasRole(1)) {
                    $query->where([
                        ['id', '=', Auth::user()->restos_id],
                    ]);
                } else if (Auth::user()->hasRole(3)) {
                    $query->get();
                }
                return $query;
            })
            ->poll('5s')
            ->columns([
                Tables\Columns\TextColumn::make('nama_resto')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')
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

    public static function canCreate(): bool
    {
        if (Auth::user()->hasRole(3)) {
            return true;
        }
        return false;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRestos::route('/'),
            'create' => Pages\CreateResto::route('/create'),
        ];
    }
}
