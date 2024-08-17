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
    case StartAt = 'starts_at';
    case EndAt = 'ends_at';
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
    
    /** Coalseced for consistency */
    case File = 'file';
    case Title = 'title';
    case Notes = 'notes';
    case Priority = 'priority';
    case Lat = 'lat';
    case Long = 'long';

    /** Undefined */
    case Undefined = 'undefined';

    public function coalesced(): ?string
    {
        return match ($this) {
            self::File => 'file',
            self::Title => 'title',
            default => null,
        };
    }

    public function blueprint(mixed $value): string
    {
        return match ($this) {
            self::Name, self::Title, => '$table->string(\'name\');',
            self::Email => '$table->string(\'email\');',
            self::Slug => '$table->string(\'slug\')->unique();',
            self::Password => '$table->string(\'password\');',
            self::Username => '$table->string(\'username\');',
            self::FirstName => '$table->string(\'first_name\');',
            self::LastName => '$table->string(\'last_name\');',
            self::Description => sprintf('$table->text(\'description\', %s)->nullable();', $this->length()),
            self::Status => '$table->unsignedTinyInteger(\'status\')->default(0);',
            self::Color => '$table->string(\'color\')->default(\'#000000\');',
            self::Code => '$table->string(\'code\');',
            self::Path, self::File => '$table->string(\'path\');',
            self::ForeignId => sprintf('$table->foreignId(\'%s\')->constrained()->onDelete(\'cascade\');', $value),
            self::StartAt => '$table->dateTime(\'starts_at\');',
            self::EndAt => '$table->dateTime(\'ends_at\')->nullable();',
            self::IsBoolean => sprintf('$table->boolean(\'%s\')->default(false);', $value),
            self::Details, self::Notes => '$table->text(\'details\')->nullable();',
            self::Data => '$table->json(\'data\')->nullable();',
            self::Phone => '$table->string(\'phone\');',
            self::Price => '$table->unsignedInteger(\'price\')',
            self::Quantity => '$table->integer(\'quantity\')',
            self::Address => '$table->string(\'address\');',
            self::City => '$table->string(\'city\');',
            self::State => '$table->string(\'state\');',
            self::Zip => '$table->string(\'zip\');',
            self::Country => '$table->string(\'country\');',
            self::Duration => '$table->unsignedInteger(\'duration\');',
            self::Minutes => '$table->unsignedInteger(\'minutes\');',
            self::Uuid => '$table->uuid(\'uuid\');',
            self::Order, self::Priority => '$table->unsignedSmallInteger(\'order\');',
            self::Token => '$table->string(\'token\')',
            self::Coordinate => '$table->point(\'coordinate\')',
            self::Latitude, self::Lat => '$table->decimal(\'latitude\', 10, 8)',
            self::Longitude, self::Long => '$table->decimal(\'longitude\', 11, 8)',
            self::Version => '$table->unsignedSmallInteger(\'version\')',
            self::Image => '$table->string(\'image\')',
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
            self::Color,
            self::Code,
            self::Path, self::File,
            self::Phone, 
            self::Address, 
            self::City, 
            self::State, 
            self::Zip, 
            self::Country,
            self::Image => 255,

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
}