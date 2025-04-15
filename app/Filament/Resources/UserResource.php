<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Outlet;
use App\Models\Resto;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $activeNavigationIcon = 'heroicon-s-user-plus';
    protected static ?string $navigationLabel = 'Management User';
    protected static ?string $navigationGroup = 'F&B Core';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('restos_id')
                    ->label('Resto ID')
                    ->required()
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search): array => Resto::where('nama_resto', 'like', "%{$search}%")
                        ->limit(50)
                        ->pluck('nama_resto', 'id')
                        ->toArray())
                    ->getOptionLabelUsing(fn($value): ?string => Resto::find($value)?->nama_resto)
                    ->reactive(),
                Forms\Components\Select::make('outlets_id')
                    ->label('outlets_id')
                    ->required()
                    ->options(function (callable $get) {
                        $restoID = $get('restos_id');
                        if ($restoID) {
                            return Outlet::where('restos_id', $restoID)
                                ->pluck('nama_outlet', 'id');
                        }
                        return Outlet::all()->pluck('nama_outlet', 'id');
                    })
                    ->disabled(fn(callable $get) => !$get('restos_id'))
                    ->searchable(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('call_number')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name', function ($query) {
                        if (Auth::user()->hasRole(2)) {
                            return $query->whereNotIn('id', [3, 1]);

                        } else if (Auth::user()->hasRole(1)) {
                            return $query->whereNotIn('id', [3]);
                        }
                    }),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table   
            ->query(function () {
                $query = User::query();
                if (Auth::user()->hasRole(2)) {
                    $query->whereHas('roles', function ($query) {
                        $query->where('restos_id', Auth::user()->restos_id)
                            ->where('outlets_id', Auth::user()->outlets_id)
                            ->where('roles.id', '=', 4);
                    })->with('roles');
                } else if (Auth::user()->hasRole(1)) {
                    $query->where([
                        ['restos_id', '=', Auth::user()->restos_id],
                        ['email', '!=', Auth::user()->email]
                    ]);
                } else if (Auth::user()->hasRole(3)) {
                    $query->get();
                }
                return $query;
            })
           ->poll('5s')
            ->columns([
                Tables\Columns\TextColumn::make('roles.name')->badge(), 
                Tables\Columns\TextColumn::make('resto.nama_resto')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('outlet.nama_outlet')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('call_number')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shift')
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
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
        ];
    }
}
