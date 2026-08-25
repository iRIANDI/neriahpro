<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\KeyValue;
use App\Models\CmsGlobalSetting;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Global Settings';
    protected static ?string $title = 'Global Settings';
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = CmsGlobalSetting::all()->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('General Setup')
                            ->icon('heroicon-m-adjustments-horizontal')
                            ->schema([
                                Select::make('app_timezone')
                                    ->label('Master Timezone (UTC Offset)')
                                    ->options([
                                        'UTC' => 'UTC (Standard)',
                                        'Asia/Jakarta' => 'WIB - Waktu Indonesia Barat (GMT+7)',
                                        'Asia/Makassar' => 'WITA - Waktu Indonesia Tengah (GMT+8)',
                                        'Asia/Jayapura' => 'WIT - Waktu Indonesia Timur (GMT+9)',
                                    ])
                                    ->searchable()
                                    ->required()
                                    ->default('Asia/Jakarta'),
                                    
                                KeyValue::make('site_identity')
                                    ->label('Site Identity')
                                    ->keyLabel('Key (e.g. site_name, tagline)')
                                    ->valueLabel('Value'),
                                    
                                KeyValue::make('contact_info')
                                    ->label('Contact Information')
                                    ->keyLabel('Type (e.g. email, phone, address)')
                                    ->valueLabel('Value'),
                                    
                                KeyValue::make('social_links')
                                    ->label('Social Media Links')
                                    ->keyLabel('Platform (e.g. facebook, instagram)')
                                    ->valueLabel('URL'),
                            ]),

                        Tabs\Tab::make('Navigation & Footer')
                            ->icon('heroicon-m-bars-3-bottom-left')
                            ->schema([
                                Builder::make('navigation_format')
                                    ->label('Main Navigation')
                                    ->blocks([
                                        Builder\Block::make('simple_nav')
                                            ->label('Simple Navigation')
                                            ->icon('heroicon-m-bars-3')
                                            ->schema([
                                                Repeater::make('links')->schema([
                                                    TextInput::make('label')->required(),
                                                    TextInput::make('url')->required(),
                                                ])->columns(2)
                                            ]),
                                        Builder\Block::make('mega_menu_nav')
                                            ->label('Mega Menu Navigation')
                                            ->icon('heroicon-m-queue-list')
                                            ->schema([
                                                Repeater::make('menus')->schema([
                                                    TextInput::make('title')->required(),
                                                    Repeater::make('links')->schema([
                                                        TextInput::make('label')->required(),
                                                        TextInput::make('url')->required(),
                                                    ])->columns(2)
                                                ])
                                            ]),
                                    ])
                                    ->maxItems(1),
                                    
                                Builder::make('footer_format')
                                    ->label('Footer Configuration')
                                    ->blocks([
                                        Builder\Block::make('simple_footer')
                                            ->label('Simple Footer')
                                            ->icon('heroicon-m-document-minus')
                                            ->schema([
                                                TextInput::make('copyright_text')->required(),
                                            ]),
                                        Builder\Block::make('multi_column_footer')
                                            ->label('Multi-Column Footer')
                                            ->icon('heroicon-m-view-columns')
                                            ->schema([
                                                TextInput::make('copyright_text')->required(),
                                                Repeater::make('columns')->schema([
                                                    TextInput::make('title')->required(),
                                                    Repeater::make('links')->schema([
                                                        TextInput::make('label')->required(),
                                                        TextInput::make('url')->required(),
                                                    ])->columns(2)
                                                ])
                                            ]),
                                    ])
                                    ->maxItems(1),
                            ]),
                            
                        Tabs\Tab::make('SEO Schema Markup')
                            ->icon('heroicon-m-magnifying-glass')
                            ->schema([
                                Section::make('Organization / Local Business Data')
                                    ->description('These details will be injected as JSON-LD Schema to help Google understand your business entity.')
                                    ->schema([
                                        Select::make('seo_schema.organization.type')
                                            ->label('Business Entity Type')
                                            ->options([
                                                'Organization' => 'General Organization',
                                                'LocalBusiness' => 'Local Business',
                                                'Corporation' => 'Corporation',
                                            ])
                                            ->default('Organization')
                                            ->required(),
                                        TextInput::make('seo_schema.organization.name')
                                            ->label('Company / Organization Name')
                                            ->required(),
                                        TextInput::make('seo_schema.organization.logo')
                                            ->label('Logo URL')
                                            ->url()
                                            ->helperText('Absolute URL to your logo image.'),
                                        TextInput::make('seo_schema.organization.telephone')
                                            ->label('Official Telephone')
                                            ->tel(),
                                        TextInput::make('seo_schema.organization.email')
                                            ->label('Official Email')
                                            ->email(),
                                    ])->columns(2),
                                    
                                Section::make('Physical Address')
                                    ->schema([
                                        TextInput::make('seo_schema.address.streetAddress')
                                            ->label('Street Address')
                                            ->columnSpanFull(),
                                        TextInput::make('seo_schema.address.addressLocality')
                                            ->label('City / Locality'),
                                        TextInput::make('seo_schema.address.addressRegion')
                                            ->label('State / Province / Region'),
                                        TextInput::make('seo_schema.address.postalCode')
                                            ->label('Postal Code'),
                                        TextInput::make('seo_schema.address.addressCountry')
                                            ->label('Country (e.g. ID, US)')
                                            ->default('ID'),
                                    ])->columns(2),
                                    
                                Section::make('Social Profiles (sameAs)')
                                    ->description('Link your official social media profiles to build knowledge graph presence.')
                                    ->schema([
                                        Repeater::make('seo_schema.sameAs')
                                            ->label('Social Media URLs')
                                            ->schema([
                                                TextInput::make('url')->label('Profile URL')->url()->required(),
                                            ])
                                            ->defaultItems(1)
                                    ]),
                            ]),
                    ])
                    ->columnSpan('full')
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        
        foreach ($data as $key => $value) {
            CmsGlobalSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }
}
