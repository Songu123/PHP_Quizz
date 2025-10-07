<?php
/**
 * Database (Singleton) - Kết nối và thao tác DB bằng PDO
 * Singleton pattern (mẫu đơn thể) đảm bảo chỉ có 1 instance của Database trong app.
 */
class Database {
    // Instance duy nhất
    private static $instance = null;

    // Thông tin kết nối (bạn có thể load từ file config/ENV)
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $charset = 'utf8';

    // PDO objects
    private $dbh;
    private $stmt;
    private $error;

    // Constructor private để không thể new trực tiếp
    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        $options = [
            PDO::ATTR_PERSISTENT => true,                 // kết nối persistent (cân nhắc khi dùng)
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // throw exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            // Lưu lỗi để debug; trong production nên log thay vì echo
            $this->error = $e->getMessage();
            throw new RuntimeException("Database connection error: " . $this->error);
        }
    }

    // Không cho clone hoặc unserialize (bảo toàn singleton)
    private function __clone() {}
    private function __wakeup() {}

    /**
     * Lấy instance duy nhất của Database
     * @return Database
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Trả về PDO connection nếu cần thao tác trực tiếp
     * @return PDO
     */
    public function getConnection(): PDO {
        return $this->dbh;
    }

    /**
     * Chuẩn bị câu query (prepare)
     * Hỗ trợ truyền params trực tiếp để execute luôn hoặc bind rời
     * @param string $sql
     * @param array $params (tùy chọn) - nếu truyền sẽ execute luôn
     * @return $this
     */
    public function query(string $sql, array $params = []) {
        $this->stmt = $this->dbh->prepare($sql);

        if (!empty($params)) {
            // Nếu truyền params dưới dạng indexed or assoc, execute luôn
            $this->stmt->execute($params);
        }

        return $this;
    }

    /**
     * Bind giá trị (khi bạn muốn bind từng param riêng)
     * @param mixed $param (ví dụ ':id' hoặc 1)
     * @param mixed $value
     * @param int|null $type PDO::PARAM_*
     */
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            if (is_int($value)) {
                $type = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            } elseif (is_null($value)) {
                $type = PDO::PARAM_NULL;
            } else {
                $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Thực thi statement (khi đã dùng bind)
     * @return bool
     */
    public function execute(): bool {
        return $this->stmt->execute();
    }

    /**
     * Lấy tất cả kết quả (mặc định là array of objects)
     * @return array
     */
    public function fetchAll(): array {
        // Nếu statement chưa được execute (ví dụ gọi query() mà ko truyền params), execute ở đây
        if ($this->stmt && $this->stmt->errorCode() === '00000') {
            // đã execute
            return $this->stmt->fetchAll();
        }

        $this->execute();
        return $this->stmt->fetchAll();
    }

    /**
     * Lấy 1 record
     * @return object|false
     */
    public function fetch() {
        if ($this->stmt && $this->stmt->errorCode() === '00000') {
            return $this->stmt->fetch();
        }

        $this->execute();
        return $this->stmt->fetch();
    }

    /**
     * rowCount (số dòng bị ảnh hưởng)
     * @return int
     */
    public function rowCount(): int {
        return $this->stmt ? $this->stmt->rowCount() : 0;
    }

    /**
     * lastInsertId
     * @return string
     */
    public function lastInsertId(): string {
        return $this->dbh->lastInsertId();
    }

    /**
     * Transaction helpers
     */
    public function beginTransaction(): bool {
        return $this->dbh->beginTransaction();
    }

    public function commit(): bool {
        return $this->dbh->commit();
    }

    public function rollBack(): bool {
        return $this->dbh->rollBack();
    }

    /**
     * Lấy lỗi gần nhất (nên dùng logging thay vì echo)
     * @return string|null
     */
    public function getError(): ?string {
        return $this->error;
    }
}
