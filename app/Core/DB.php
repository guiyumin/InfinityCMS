<?php

namespace App\Core;

/**
 * Database - PDO wrapper with query builder
 * 数据库类 - PDO 包装器与查询构建器
 */
class DB {
    /**
     * PDO instance
     * @var \PDO
     */
    protected $pdo;

    /**
     * Query builder state
     */
    protected $table;
    protected $select = ['*'];
    protected $where = [];
    protected $bindings = [];
    protected $orderBy = [];
    protected $limit;
    protected $offset;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = []) {
        if (!empty($config)) {
            $this->connect($config);
        }
    }

    /**
     * Connect to database
     * 连接数据库
     *
     * @param array $config
     * @return void
     */
    public function connect(array $config) {
        $driver = $config['driver'] ?? 'mysql';

        try {
            if ($driver === 'mysql') {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    $config['host'] ?? 'localhost',
                    $config['port'] ?? 3306,
                    $config['database'],
                    $config['charset'] ?? 'utf8mb4'
                );
                $this->pdo = new \PDO(
                    $dsn,
                    $config['username'],
                    $config['password']
                );
            } else {
                throw new \Exception("Unsupported database driver: {$driver}");
            }

            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Get PDO instance
     * 获取 PDO 实例
     *
     * @return \PDO
     */
    public function getPdo() {
        return $this->pdo;
    }

    /**
     * Set table for query
     * 设置查询表
     *
     * @param string $table
     * @return $this
     */
    public function table($table) {
        $this->resetQuery();
        $this->table = $table;
        return $this;
    }

    /**
     * Set SELECT columns
     * 设置 SELECT 列
     *
     * @param array|string $columns
     * @return $this
     */
    public function select($columns = ['*']) {
        $this->select = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * Add WHERE clause
     * 添加 WHERE 条件
     *
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     * @return $this
     */
    public function where($column, $operator = null, $value = null) {
        // If only 2 arguments, assume operator is '='
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->where[] = [
            'type' => 'AND',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];

        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add OR WHERE clause
     * 添加 OR WHERE 条件
     *
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->where[] = [
            'type' => 'OR',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];

        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add ORDER BY clause
     * 添加 ORDER BY
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'ASC') {
        $this->orderBy[] = [$column, strtoupper($direction)];
        return $this;
    }

    /**
     * Set LIMIT
     * 设置 LIMIT
     *
     * @param int $limit
     * @return $this
     */
    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set OFFSET
     * 设置 OFFSET
     *
     * @param int $offset
     * @return $this
     */
    public function offset($offset) {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Execute SELECT query and get results
     * 执行 SELECT 查询并获取结果
     *
     * @return array
     */
    public function get() {
        $sql = $this->buildSelectQuery();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll();
    }

    /**
     * Get first result
     * 获取第一条结果
     *
     * @return array|null
     */
    public function first() {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    /**
     * Find by ID
     * 根据 ID 查找
     *
     * @param mixed $id
     * @return array|null
     */
    public function find($id) {
        return $this->where('id', $id)->first();
    }

    /**
     * Insert record
     * 插入记录
     *
     * @param array $data
     * @return int Last insert ID
     */
    public function insert(array $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));

        return $this->pdo->lastInsertId();
    }

    /**
     * Update records
     * 更新记录
     *
     * @param array $data
     * @return int Affected rows
     */
    public function update(array $data) {
        $sets = [];
        $bindings = [];

        foreach ($data as $column => $value) {
            $sets[] = "{$column} = ?";
            $bindings[] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);

        // Add WHERE clause
        if (!empty($this->where)) {
            $sql .= ' WHERE ' . $this->buildWhereClause();
            $bindings = array_merge($bindings, $this->bindings);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);

        return $stmt->rowCount();
    }

    /**
     * Delete records
     * 删除记录
     *
     * @return int Affected rows
     */
    public function delete() {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= ' WHERE ' . $this->buildWhereClause();
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->rowCount();
    }

    /**
     * Get count of records
     * 获取记录数
     *
     * @return int
     */
    public function count() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= ' WHERE ' . $this->buildWhereClause();
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);

        return (int) $stmt->fetch()['count'];
    }

    /**
     * Execute raw query
     * 执行原始查询
     *
     * @param string $sql
     * @param array $bindings
     * @return array
     */
    public function query($sql, array $bindings = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    /**
     * Execute raw statement (INSERT, UPDATE, DELETE)
     * 执行原始语句
     *
     * @param string $sql
     * @param array $bindings
     * @return bool
     */
    public function execute($sql, array $bindings = []) {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($bindings);
    }

    /**
     * Build SELECT query
     * 构建 SELECT 查询
     *
     * @return string
     */
    protected function buildSelectQuery() {
        $columns = implode(', ', $this->select);
        $sql = "SELECT {$columns} FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= ' WHERE ' . $this->buildWhereClause();
        }

        if (!empty($this->orderBy)) {
            $orders = [];
            foreach ($this->orderBy as $order) {
                $orders[] = "{$order[0]} {$order[1]}";
            }
            $sql .= ' ORDER BY ' . implode(', ', $orders);
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }

    /**
     * Build WHERE clause
     * 构建 WHERE 子句
     *
     * @return string
     */
    protected function buildWhereClause() {
        $clauses = [];

        foreach ($this->where as $index => $condition) {
            $clause = "{$condition['column']} {$condition['operator']} ?";

            if ($index > 0) {
                $clause = "{$condition['type']} {$clause}";
            }

            $clauses[] = $clause;
        }

        return implode(' ', $clauses);
    }

    /**
     * Reset query builder state
     * 重置查询构建器状态
     *
     * @return void
     */
    protected function resetQuery() {
        $this->select = ['*'];
        $this->where = [];
        $this->bindings = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
    }

    /**
     * Begin transaction
     * 开始事务
     *
     * @return bool
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit transaction
     * 提交事务
     *
     * @return bool
     */
    public function commit() {
        return $this->pdo->commit();
    }

    /**
     * Rollback transaction
     * 回滚事务
     *
     * @return bool
     */
    public function rollback() {
        return $this->pdo->rollBack();
    }
}
