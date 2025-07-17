<?php

namespace App\Livewire;

use App\Models\User;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class UsersTable extends DataTableComponent
{
    protected $model = User::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('id', 'desc')
            ->setPerPage(10)
            ->setLoadingPlaceholderEnabled(true)
            ->setLoadingPlaceholderContent(view('components.table-loader'))
            ->setEmptyMessage('No users found')
            ->setTableWrapperAttributes([
                'default' => false,
                'class' => 'overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow',
            ])
            ->setThAttributes(function($column) {
                return [
                    'class' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider',
                ];
            })
            ->setTdAttributes(function($column, $row, $columns) {
                return [
                    'class' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200',
                ];
            });
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->searchable(),
                
            Column::make('Username', 'username')
                ->sortable()
                ->searchable(),
                
            Column::make('Email', 'email')
                ->searchable(),
                
            Column::make('Created At', 'created_at')
                ->sortable()
                ->format(fn($value) => $value->format('Y-m-d H:i'))
        ];
    }
}