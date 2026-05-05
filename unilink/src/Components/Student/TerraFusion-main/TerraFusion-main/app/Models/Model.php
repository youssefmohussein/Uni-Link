<?php

namespace App\Models;

use PDO;
use PDOStatement;
use RuntimeException;
use App\Libs\Database;

abstract class Model
{
    protected static string $table = '';
    protected array $attributes = [];
    protected array $fillable = [];
    protected string $primaryKey = 'id';
    protected bool $exists = false;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable) || $key === $this->primaryKey) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    public static function find(int $id): ?self
    {
        $instance = new static();
        $table = $instance->getTable();
        $primaryKey = $instance->getPrimaryKey();

        $stmt = $instance->prepare("SELECT * FROM `{$table}` WHERE `{$primaryKey}` = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        $instance->fill($result);
        $instance->exists = true;
        return $instance;
    }

    public function save(): bool
    {
        if ($this->exists) {
            return $this->update();
        }
        return $this->insert();
    }

    protected function insert(): bool
    {
        $table = $this->getTable();
        $attributes = $this->getAttributes();
        $columns = implode('`, `', array_keys($attributes));
        $placeholders = ':' . implode(', :', array_keys($attributes));

        $sql = "INSERT INTO `{$table}` (`{$columns}`) VALUES ({$placeholders})";
        $stmt = $this->prepare($sql);

        $result = $stmt->execute($attributes);

        if ($result) {
            $this->{$this->primaryKey} = $this->getConnection()->lastInsertId();
            $this->exists = true;
            return true;
        }

        return false;
    }

    protected function update(): bool
    {
        $table = $this->getTable();
        $primaryKey = $this->getPrimaryKey();
        $attributes = $this->getAttributes();
        $setClause = implode(' = ?, ', array_keys($attributes)) . ' = ?';

        $sql = "UPDATE `{$table}` SET {$setClause} WHERE `{$primaryKey}` = ?";
        $stmt = $this->prepare($sql);

        $values = array_values($attributes);
        $values[] = $this->{$primaryKey};

        return $stmt->execute($values);
    }

    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }

        $table = $this->getTable();
        $primaryKey = $this->getPrimaryKey();
        $sql = "DELETE FROM `{$table}` WHERE `{$primaryKey}` = ?";
        $stmt = $this->prepare($sql);

        $result = $stmt->execute([$this->{$primaryKey}]);

        if ($result) {
            $this->exists = false;
            return true;
        }

        return false;
    }

    public static function all(): array
    {
        $instance = new static();
        $table = $instance->getTable();
        $stmt = $instance->prepare("SELECT * FROM `{$table}`");
        $stmt->execute();

        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $model = new static();
            $model->fill($row);
            $model->exists = true;
            $results[] = $model;
        }

        return $results;
    }

    public function getTable(): string
    {
        if (empty(static::$table)) {
            $className = (new \ReflectionClass($this))->getShortName();
            return strtolower($className) . 's';
        }
        return static::$table;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function getAttributes(): array
    {
        $attributes = [];
        foreach ($this->fillable as $field) {
            if (isset($this->attributes[$field])) {
                $attributes[$field] = $this->attributes[$field];
            }
        }
        return $attributes;
    }

    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        if (in_array($name, $this->fillable) || $name === $this->primaryKey) {
            $this->attributes[$name] = $value;
        }
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    protected function prepare(string $sql): PDOStatement
    {
        return $this->getConnection()->prepare($sql);
    }

    protected function getConnection(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    public static function where(string $column, $value, string $operator = '='): array
    {
        $instance = new static();
        $table = $instance->getTable();
        
        $sql = "SELECT * FROM `{$table}` WHERE `{$column}` {$operator} :value";
        $stmt = $instance->prepare($sql);
        $stmt->execute([':value' => $value]);
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $model = new static();
            $model->fill($row);
            $model->exists = true;
            $results[] = $model;
        }
        
        return $results;
    }
}
