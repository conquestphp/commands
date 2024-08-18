<?php

namespace Conquest\Command\Concerns;

use Conquest\Command\Enums\SchemaColumn;
use Illuminate\Support\Collection;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;

trait HasSchemaColumns
{
    /**
     * Whether the user has confirmed undefined columns during prompting.
     * 
     * @var bool
     */
    protected $confirmedDuringPrompting = false;

    /**
     * Get the schema for a given column.
     *
     * @param string $column The column name to get the schema for.
     * @return null|array{0: SchemaColumn, 1: string} An array containing the SchemaColumn enum and the original column name.
     */
    protected function getSchemaColumn(string $column): ?array
    {
        $schema = SchemaColumn::tryWithPatterns($column);

        if ($this->option('suppress')) {
            // Do nothing
        } elseif ($coalesced = $schema->coalesced()) {
            $this->components->warn(sprintf('Column [%s] will be coalesced to [%s].', $column, $coalesced));
        } elseif ($schema->isUndefined() && ! $this->confirmedDuringPrompting) {
            if (! confirm(sprintf('Column [%s] is not a predefined column. Do you want to include it anyway?', $column))) {
                return null;
            }
        }

        return [$schema, $column];
    }

    protected function getSchemaColumns(): Collection
    {
        return str($this->option('columns'))->explode(',')
            ->map(fn (string $column) => trim($column))
            ->map(fn (string $column) => $this->getSchemaColumn($column))
            ->filter(fn (?array $column) => $column !== null)
            ->sortByDesc(fn (array $column) => $column[0]->precedence());
    }

    protected function promptForSchemaColumns(): string
    {
        $columns = collect();
        collect(multiselect('Select which columns you would like to include?.', 
            collect(SchemaColumn::cases())
                ->filter(fn (SchemaColumn $column) => !$column->coalesced())
                ->mapWithKeys(fn (SchemaColumn $column) => [$column->value => $column->name])
                ->toArray()
        ))->each(fn ($option) => $columns->push($option));
        
        if ($columns->contains(SchemaColumn::ForeignId->value)) {
            $columns = $columns->reject(fn ($column) => $column === SchemaColumn::ForeignId->value);

            while (true) {
                $value = text('What is the foreign key column?', 'user_id');
                $columns->push($value);

                if (empty($value) || !confirm('Do you want to add another foreign key column?')) {
                    break;
                }
            }                    
        }

        if ($columns->contains(SchemaColumn::Undefined->value)) {
            $columns = $columns->reject(fn ($column) => $column === SchemaColumn::Undefined->value);
            while (true) {
                $value = text('What is the column name?', 'custom');
                $columns->push($value);

                if (empty($value) || !confirm('Do you want to add another column?')) {
                    break;
                }
            }                    
        }

        return $columns->unique()->implode(',');
    }



}
