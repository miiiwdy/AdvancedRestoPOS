<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManagementShiftResource\Pages;
use App\Filament\Resources\ManagementShiftResource\RelationManagers;
use App\Models\DataShift;
use App\Models\ManagementShift;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;

class ManagementShiftResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Management Shift';
    protected static ?string $navigationGroup = 'F&B Core';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('shift')
                ->options(DataShift::where([
                    ['restos_id', '=', Auth::user()->restos_id],
                    ['outlets_id', '=', Auth::user()->outlets_id],
                ])->pluck('nama_shift', 'no_shift'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function() {
                $query = User::query();
                if (Auth::user()->hasRole(1)) {
                    $query->where('restos_id', Auth::user()->restos_id)
                        ->where('email', '!=', Auth::user()->email)
                        ->whereHas('roles', function ($q) {
                        $q->where('id', '!=', 3);
                    });
                }
                else if (Auth::user()->hasRole(2)) {
                    $query->whereHas('roles', function ($query) {
                        $query->where('restos_id', Auth::user()->restos_id)
                            ->where('outlets_id', Auth::user()->outlets_id)
                            ->where('email', '!=', Auth::user()->email)
                            ->where('roles.id', '=', 4);
                    })->with('roles');
                }
                else if (Auth::user()->hasRole(3)) {
                    $query->get();
                }
                return $query;
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('call_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                // Tables\Columns\SelectColumn::make('shift')
                //     ->options(DataShift::where([
                //         ['restos_id', '=', Auth::user()->restos_id],
                //         ['outlets_id', '=', Auth::user()->outlets_id],
                //     ])->pluck('nama_shift', 'no_shift'))
                //     ->selectablePlaceholder(false),
                Tables\Columns\TextColumn::make('shift')
                    ->searchable(),
                Tables\Columns\TextColumn::make('resto.nama_resto')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('outlet.nama_outlet')
                    ->badge()
                    ->color('success')
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
    public static function canCreate(): bool {
        return false;
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListManagementShifts::route('/'),
            'create' => Pages\CreateManagementShift::route('/create'),
        ];
    }
}
