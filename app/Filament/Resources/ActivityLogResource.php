<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Log Activity';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('user_name')
                            ->label('User Name')
                            ->disabled(),
                        Forms\Components\TextInput::make('user_email')
                            ->label('Email')
                            ->disabled(),
                        Forms\Components\TextInput::make('user_type')
                            ->label('User Type')
                            ->disabled(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Activity Information')
                    ->schema([
                        Forms\Components\TextInput::make('action')
                            ->disabled(),
                        Forms\Components\TextInput::make('model')
                            ->disabled(),
                        Forms\Components\TextInput::make('model_id')
                            ->label('Model ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('model_name')
                            ->label('Record Name')
                            ->disabled(),
                        Forms\Components\Textarea::make('description')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Request Information')
                    ->schema([
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address')
                            ->disabled(),
                        Forms\Components\TextInput::make('method')
                            ->disabled(),
                        Forms\Components\TextInput::make('url')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Change Details')
                    ->schema([
                        Forms\Components\Textarea::make('old_values')
                            ->label('Old Values (JSON)')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('new_values')
                            ->label('New Values (JSON)')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Timestamp')
                    ->schema([
                        Forms\Components\TextInput::make('created_at')
                            ->label('Created At')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user_name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('action')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'login' => 'success',
                        'logout' => 'info',
                        'view' => 'gray',
                        'create' => 'success',
                        'update' => 'warning',
                        'delete' => 'danger',
                        'password_change' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('model')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable(),

                TextColumn::make('user_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'user' => 'primary',
                        'mahasiswa' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Filter::make('action')
                    ->form([
                        Forms\Components\Select::make('action')
                            ->options([
                                'login' => 'Login',
                                'logout' => 'Logout',
                                'view' => 'View',
                                'create' => 'Create',
                                'update' => 'Update',
                                'delete' => 'Delete',
                                'password_change' => 'Password Change',
                            ])
                            ->searchable(),
                    ])
                    ->query(fn(Builder $query, array $data): Builder =>
                        isset($data['action'])
                            ? $query->where('action', $data['action'])
                            : $query
                    ),

                Filter::make('user_type')
                    ->form([
                        Forms\Components\Select::make('user_type')
                            ->options([
                                'user' => 'Admin',
                                'mahasiswa' => 'Student',
                            ])
                            ->searchable(),
                    ])
                    ->query(fn(Builder $query, array $data): Builder =>
                        isset($data['user_type'])
                            ? $query->where('user_type', $data['user_type'])
                            : $query
                    ),

                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn(Builder $q, $date) => $q->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn(Builder $q, $date) => $q->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
}
