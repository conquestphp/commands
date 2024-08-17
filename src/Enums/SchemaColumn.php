<?php

namespace Conquest\Command\Enums;

enum SchemaColumn: string
{
    case Name = 'name';
    case Email = 'email';
    case Slug = 'slug';
    case Password = 'password';
    case Username = 'username';
    case FirstName = 'first_name';
    case LastName = 'last_name';
    case Description = 'description';
    case Status = 'status';
    case Color = 'color';
    case Code = 'code';
    case Path = 'path';
    case ForeignId = '*_id';
    case StartsAt = 'starts_at';
    case EndsAt = 'ends_at';
    case IsBoolean = 'is_*';
    case Details = 'details';
    case Data = 'data';
    case Phone = 'phone';
    case Price = 'price';
    case Quantity = 'quantity';
    case Address = 'address';
    case City = 'city';
    case State = 'state';
    case Zip = 'zip';
    case Country = 'country';
    case Duration = 'duration';
    case Minutes = 'minutes';
    case Uuid = 'uuid';
    case Order = 'order';
    case Token = 'token';
    case Coordinate = 'coordinate';
    case Latitude = 'latitude';
    case Longitude = 'longitude';
    case Version = 'version';
    case Image = 'image';
    case Url = 'url';
    case CreatedBy = 'created_by';
    case UpdatedBy = 'updated_by';
    case IpAddress = 'ip_address';
    case Icon = 'icon';   
    
    /** Coalseced for consistency */
    case File = 'file';
    case Title = 'title';
    case Notes = 'notes';
    case Priority = 'priority';
    case Lat = 'lat';
    case Long = 'long';
    case Colour = 'colour';


    /** Undefined */
    case Undefined = 'undefined';

    public function coalesced(): ?string
    {
        return match ($this) {
            self::File => 'file',
            self::Title => 'title',
            self::Notes => 'details',
            self::Priority => 'order',
            self::Lat => 'latitude',
            self::Long => 'longitude',
            self::Colour => 'color',
            default => null,
        };
    }

    public function blueprint(mixed $value): string
    {
        return match ($this) {
            self::Name, self::Title, => '$table->string(\'name\');',
            // self::Email => '$table->string(\'email\');',
            self::Slug => '$table->string(\'slug\')->unique();',
            // self::Password => '$table->string(\'password\');',
            // self::Username => '$table->string(\'username\');',
            // self::FirstName => '$table->string(\'first_name\');',
            // self::LastName => '$table->string(\'last_name\');',
            self::Description => sprintf('$table->text(\'description\', %s)->nullable();', $this->length()()),
            self::Status => '$table->unsignedTinyInteger(\'status\')->default(0);',
            self::Color, self::Colour => '$table->string(\'color\')->default(\'#000000\');',
            // self::Code => '$table->string(\'code\');',
            self::Path, self::File => '$table->string(\'path\');',
            self::ForeignId => sprintf('$table->foreignId(\'%s\')->constrained()->onDelete(\'cascade\');', $value),
            self::StartsAt => '$table->dateTime(\'starts_at\');',
            self::EndsAt => '$table->dateTime(\'ends_at\')->nullable();',
            self::IsBoolean => sprintf('$table->boolean(\'%s\')->default(false);', $value),
            self::Details, self::Notes => '$table->text(\'details\')->nullable();',
            self::Data => '$table->json(\'data\')->nullable();',
            self::Phone => '$table->string(\'phone\');',
            self::Price => '$table->unsignedInteger(\'price\')',
            self::Quantity => '$table->integer(\'quantity\')',
            // self::Address => '$table->string(\'address\');',
            // self::City => '$table->string(\'city\');',
            // self::State => '$table->string(\'state\');',
            // self::Zip => '$table->string(\'zip\');',
            self::Country => '$table->string(\'country\');',
            self::Duration => '$table->unsignedInteger(\'duration\');',
            self::Minutes => '$table->unsignedInteger(\'minutes\');',
            self::Uuid => '$table->uuid(\'uuid\');',
            self::Order, self::Priority => '$table->unsignedSmallInteger(\'order\');',
            // self::Token => '$table->string(\'token\')',
            self::Coordinate => '$table->point(\'coordinate\')',
            self::Latitude, self::Lat => '$table->decimal(\'latitude\', 10, 8)',
            self::Longitude, self::Long => '$table->decimal(\'longitude\', 11, 8)',
            self::Version => '$table->unsignedSmallInteger(\'version\')',
            self::CreatedBy => '$table->foreignId(\'created_by\')->nullable()->constrained(\'users\')->onDelete(\'cascade\');',
            self::UpdatedBy => '$table->foreignId(\'updated_by\')->nullable()->constrained(\'users\')->onDelete(\'cascade\');',
            // self::Image => '$table->string(\'image\')',
            // self::Url => '$table->string(\'url\')',
            // self::IpAddress => '$table->string(\'ip_address\')',
            // self::Icon => '$table->string(\'icon\')',
            default => sprintf('$table->string(\'%s\');', $value),
        };
    }

    public function length(): int
    {
        return match ($this) {
            self::Status,
            self::Token,
            self::Name, self::Title,
            self::Email,
            self::Slug,
            self::Password,
            self::Username,
            self::FirstName,
            self::LastName,
            self::Color, self::Colour,
            self::Code,
            self::Path, self::File,
            self::Phone, 
            self::Address, 
            self::City, 
            self::State, 
            self::Zip, 
            self::Country,
            self::Image,
            self::Icon, self::IpAddress => 255,

            self::Description => 512,

            self::Order, self::Priority,
            self::Version,
            self::Details,
            self::Notes => 65535,

            self::Data => 16777215,

            self::Quantity => 2147483647,

            self::Price,
            self::Duration,
            self::Minutes => 4294967295, 
            default => 0,
        };
    }

    public function rules(mixed $value, ?string $table = null): array
    {
        return match ($this) {
            self::Email => ['required', 'string', 'email', sprintf('max:%s', $this->length())],
            self::Slug => ['required', 'string', 'lowercase', sprintf('unique:%s,slug', $table), sprintf('max:%s', $this->length())],
            self::Password => ['required', 'string', 'min:8', sprintf('max:%s', $this->length())],
            self::Description => ['nullable', 'string', sprintf('max:%s', $this->length())],
            self::Status => ['required', 'integer', 'min:0', sprintf('max:%s', $this->length())],
            self::Color, self::Colour => ['nullable', 'hex_color', sprintf('max:%s', $this->length())],
            self::ForeignId => ['required', 'integer', 'exists:users,id'],
            self::StartsAt => ['required', 'date', 'after_or_equal:now'],
            self::EndsAt => ['nullable', 'date', 'after_or_equal:now'],
            self::IsBoolean => ['required', 'boolean'],
            self::Details, self::Notes => ['nullable', 'string', sprintf('max:%s', $this->length())],
            self::Data => ['required', 'json', sprintf('max:%s', $this->length())],
            self::Price => ['required', 'integer', 'min:0', sprintf('max:%s', $this->length())],
            self::Quantity => ['required', 'integer', 'min:0', sprintf('max:%s', $this->length())],
            self::Duration => ['required', 'integer', 'min:0', sprintf('max:%s', $this->length())],
            self::Minutes => ['required', 'integer', 'min:0', sprintf('max:%s', $this->length())],
            self::Uuid => ['required', 'uuid'],
            self::Order, self::Priority => ['required', 'integer', 'min:0', sprintf('max:%s', $this->length())],
            self::Token => ['required', 'alpha_num', sprintf('max:%s', $this->length())],
            self::Coordinate => ['required', 'point'],
            self::Latitude => ['required', 'decimal', 'min:-90', 'max:90'],
            self::Longitude => ['required', 'decimal', 'min:-180', 'max:180'],
            self::Version => ['required', 'integer', 'min:0', sprintf('max:%s', $this->length())],
            self::IpAddress => ['required', 'ip'],
            self::Undefined => ['required'],
            self::Url => ['required', 'url', sprintf('max:%s', $this->length())],
            default => ['required', 'string', sprintf('max:%s', $this->length())],
        };
    }

    public function cast(): ?string
    {
        return match ($this) {
            self::StartsAt, self::EndsAt => '\'datetime\'',
            self::Data => '\'json\'',
            self::Price => 'Money::class',
            default => null,
        };
    }

    public function hidden(): bool
    {
        return match ($this) {
            self::ForeignId, 
            self::CreatedBy, 
            self::UpdatedBy,
            self::Password,
            self::Token => true,
            default => false,
        };
    }

    public function factory(mixed $value): string
    {
        return match ($this) {
            self::Name, self::Title => 'fake()->name()',
            self::Email => 'fake()->unique()->safeEmail()',
            self::Slug => 'fake()->unique()->slug()',
            self::Password => 'fake()->password(8)',
            self::Username => 'fake()->unique()->userName()',
            self::FirstName => 'fake()->firstName()',
            self::LastName => 'fake()->lastName()',
            self::Description => 'fake()->text(512)',
            self::Status => 'fake()->numberBetween(0, 6)',
            self::Color, self::Colour => 'fake()->hexColor()',
            self::Code => 'fake()->unique()->regexify(\'[A-Z0-9]{5}\')',
            self::Path, self::File => 'fake()->filePath()',
            // self::ForeignId => 'null',
            self::StartsAt => 'fake()->dateTimeBetween(\'-1 year\', \'now\')',
            self::EndsAt => 'fake()->dateTimeBetween(\'now\', \'+1 year\')',
            self::IsBoolean => 'fake()->boolean()',
            self::Details, self::Notes => 'fake()->text(65535)',
            self::Data => 'fake()->json()',
            self::Phone => 'fake()->phoneNumber()',
            self::Price => 'fake()->numberBetween(0, 10000)',
            self::Quantity => 'fake()->numberBetween(0, 1000000)',
            self::Address => 'fake()->address()',
            self::City => 'fake()->city()',
            self::State => 'fake()->state()',
            self::Zip => 'fake()->postcode()',
            self::Country => 'fake()->country()',
            self::Duration => 'fake()->numberBetween(0, 300)',
            self::Minutes => 'fake()->numberBetween(0, 300)',
            self::Uuid => 'fake()->uuid()',
            self::Order, self::Priority => 'fake()->numberBetween(0, 100)',
            self::Token => 'fake()->regexify(\'[A-Z0-9]{32}\')',
            self::Coordinate => 'fake()->point()',
            self::Latitude, self::Lat => 'fake()->latitude()',
            self::Longitude, self::Long => 'fake()->longitude()',
            self::Version => 'fake()->numberBetween(0, 100)',
            self::Image => 'fake()->imageUrl()',
            self::Url => 'fake()->url()',
            self::IpAddress => 'fake()->ipv4()',
            self::Icon => 'fake()->slug()',
            // self::UpdatedBy => 'null',
            // self::CreatedBy => 'null',
            default => 'null',
        };
    }

    public function hasRelationship(): bool
    {
        return match ($this) {
            self::ForeignId,
            self::CreatedBy,
            self::UpdatedBy => true,
            default => false,
        };
    }

    // Assume that everything except user based relations is a HasMany
    // public static function relationship(string $value, boolean $other = false): ?string
    // {
    //     if (in_array($value, [SchemaColumn::CreatedBy->value, SchemaColumn::UpdatedBy->value])) {
    //         return 'belong';
    //     }

    //     // Retrieve the model name from the value
    //     $model = str($value)->replace('*_id', '')->ucfirst()->value();


    // }
    
}