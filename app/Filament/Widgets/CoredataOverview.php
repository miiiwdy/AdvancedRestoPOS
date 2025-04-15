<?php
namespace App\Filament\Widgets;

use App\Models\LaporanPajak;
use App\Models\Product;
use App\Models\LaporanOrder;
use App\Models\LaporanOrderProduct;
use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\IconPosition;

class CoredataOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    public function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        if(Auth::user()->hasRole(1)) {
            $t_SalesAmount = LaporanOrder::where('restos_id', Auth::user()->restos_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->sum('total_harga_jual');
            
            $t_Transaction = LaporanOrder::where('restos_id', Auth::user()->restos_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count();

            $t_productSales = LaporanOrderProduct::where('restos_id', Auth::user()->restos_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count();

            $t_guests = LaporanOrder::where('restos_id', Auth::user()->restos_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->sum('guest');

            $t_netProfits = LaporanOrder::where('restos_id', Auth::user()->restos_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->sum('keuntungan');

            $t_productAmount = Product::where('restos_id', Auth::user()->restos_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count();

            $t_activePayment = Payment::where('restos_id', Auth::user()->restos_id)->where('is_active', true)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count();

            $t_calcpajak = LaporanPajak::where('restos_id', Auth::user()->restos_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->sum('total_pajak');

            $t_dataUser = User::where('restos_id', Auth::user()->restos_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count();

        }
        if(Auth::user()->hasRole(2)) {
            $t_SalesAmount = LaporanOrder::where('restos_id', Auth::user()->restos_id)
                ->where('outlets_id', Auth::user()->outlets_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->sum('total_harga_jual');

            $t_Transaction = LaporanOrder::where('restos_id', Auth::user()->restos_id)
            ->where('outlets_id', Auth::user()->outlets_id)
            ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();

            $t_productSales = LaporanOrderProduct::where('restos_id', Auth::user()->restos_id)
                ->where('outlets_id', Auth::user()->outlets_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count();

            $t_guests = LaporanOrder::where('restos_id', Auth::user()->restos_id)
                ->where('outlets_id', Auth::user()->outlets_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->sum('guest');

            $t_netProfits = LaporanOrder::where('restos_id', Auth::user()->restos_id)
                ->where('outlets_id', Auth::user()->outlets_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->sum('keuntungan');

            $t_productAmount = Product::where('restos_id', Auth::user()->restos_id)
                ->where('outlets_id', Auth::user()->outlets_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count();

            $t_activePayment = Payment::where('restos_id', Auth::user()->restos_id)
                ->where('outlets_id', Auth::user()->outlets_id)->where('is_active', true)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count();

            $t_calcpajak = LaporanPajak::where('restos_id', Auth::user()->restos_id)
                ->where('outlets_id', Auth::user()->outlets_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->sum('total_pajak');

            $t_dataUser = User::where('restos_id', Auth::user()->restos_id)
                ->where('outlets_id', Auth::user()->outlets_id)
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count();
        }
        if(Auth::user()->hasRole(3)) {
            $t_SalesAmount = LaporanOrder::when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->sum('total_harga_jual');
                
            $t_Transaction = LaporanOrder::when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count();

            $t_productSales = LaporanOrderProduct::when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count();

            $t_guests = LaporanOrder::when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->sum('guest');

            $t_netProfits = LaporanOrder::when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->sum('keuntungan');

            $t_productAmount = Product::when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count();

            $t_activePayment = Payment::when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count();

            $t_calcpajak = LaporanPajak::when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->sum('total_pajak');

            $t_dataUser = User::when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count();
        }

        return [
            Stat::make('Total Transaction', number_format($t_Transaction, 0, ',', '.'))
                ->description('Total Transaction')
                ->descriptionIcon('heroicon-s-currency-dollar', IconPosition::Before)
                ->color($t_Transaction < 5 ? 'danger' : ($t_Transaction <= 10 ? 'warning' : 'success'))
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total Sales Amount (Revenue)', 'Rp ' . number_format($t_SalesAmount, 0, ',', '.'))
                ->description('Total Sales Amount')
                ->descriptionIcon('heroicon-s-document-currency-dollar', IconPosition::Before)
                ->color($t_SalesAmount < 5 ? 'danger' : ($t_SalesAmount <= 10 ? 'warning' : 'success'))
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Net Profits', 'Rp ' . number_format($t_netProfits, 0, ',', '.'))
                ->description('Net Profits')
                ->descriptionIcon('heroicon-s-document-currency-dollar', IconPosition::Before)
                ->color($t_netProfits < 5 ? 'danger' : ($t_netProfits <= 10 ? 'warning' : 'success'))
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total Product Sales', number_format($t_productSales, 0, ',', '.'))
                ->description('Total Product Sales')
                ->descriptionIcon('heroicon-s-shopping-cart', IconPosition::Before)
                ->color($t_productSales < 5 ? 'danger' : ($t_productSales <= 10 ? 'warning' : 'success'))
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total Guests', number_format($t_guests, 0, ',', '.'))
                ->description('Total Guests')
                ->descriptionIcon('heroicon-s-user-group', IconPosition::Before)
                ->color($t_guests < 5 ? 'danger' : ($t_guests <= 10 ? 'warning' : 'success'))
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total Product', number_format($t_productAmount, 0, ',', '.'))
                ->description('Total Product')
                ->descriptionIcon('heroicon-s-archive-box', IconPosition::Before)
                ->color($t_productAmount < 5 ? 'danger' : ($t_productAmount <= 10 ? 'warning' : 'success'))
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total Active Payment', number_format($t_activePayment, 0, ',', '.'))
                ->description('Total Active Payment')
                ->descriptionIcon('heroicon-s-document-currency-dollar', IconPosition::Before)
                ->color($t_activePayment < 5 ? 'danger' : ($t_activePayment <= 10 ? 'warning' : 'success'))
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total Pajak Terkumpul','Rp ' .  number_format($t_calcpajak, 0, ',', '.'))
                ->description('Total Pajak Terkumpul')
                ->descriptionIcon('heroicon-s-document-currency-dollar', IconPosition::Before)
                ->color($t_calcpajak < 5 ? 'danger' : ($t_calcpajak <= 10 ? 'warning' : 'success'))
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Data User', number_format($t_dataUser, 0, ',', '.'))
                ->description('Data User')
                ->descriptionIcon('heroicon-s-user-group', IconPosition::Before)
                ->color($t_dataUser < 5 ? 'danger' : ($t_dataUser <= 10 ? 'warning' : 'success'))
                ->chart([7, 2, 10, 3, 15, 4, 17]),
                
        ];
    }
}