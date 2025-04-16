<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OutletResource\Pages;
use App\Filament\Resources\OutletResource\RelationManagers;
use App\Models\Outlet;
use App\Models\Resto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class OutletResource extends Resource
{
    protected static ?string $model = Outlet::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $activeNavigationIcon = 'heroicon-s-building-library';
    protected static ?string $navigationLabel = 'Management Outlet';
    protected static ?string $navigationGroup = 'F&B Core';
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Auth::user()->hasRole(3)
                ? Forms\Components\Select::make('restos_id')
                    ->label('Nama Resto')
                    ->required()
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search): array =>
                        Resto::where('nama_resto', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('nama_resto', 'id')
                            ->toArray()
                    )
                    ->getOptionLabelUsing(fn($value): ?string =>
                        Resto::find($value)?->nama_resto
                    )
                    ->reactive()
                : Forms\Components\Hidden::make('restos_id')
                    ->default(Auth::user()->restos_id),
        
            Forms\Components\TextInput::make('nama_outlet')
                ->placeholder('Nama Outlet')
                ->required()
                ->maxLength(255),
        
            Forms\Components\TextInput::make('deskripsi')
                ->placeholder('Deskripsi Outlet')
                ->required()
                ->maxLength(255),
        
            Forms\Components\TextInput::make('alamat')
                ->placeholder('Alamat Outlet')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function() {
                $query = Outlet::query();
                if (Auth::user()->hasRole(1)) {
                    $query->where([
                        ['restos_id', '=', Auth::user()->restos_id],
                    ]);
                }
                else if (Auth::user()->hasRole(2)) {
                    $query->where([
                        ['restos_id', '=', Auth::user()->restos_id],
                        ['id', '=', Auth::user()->outlets_id],
                    ]);
                }
                else if (Auth::user()->hasRole(3)) {
                    $query->get();
                }
                return $query;
            })
           ->poll('5s')
            ->columns([
                Tables\Columns\TextColumn::make('resto.nama_resto')
                    ->label('Nama Resto')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_outlet')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function canCreate(): bool {
        if (Auth::user()->hasRole(2)) {
            return false;
        };
        return true;
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOutlets::route('/'),
            'create' => Pages\CreateOutlet::route('/create'),
        ];
    }
}
