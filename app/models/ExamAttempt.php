<?php
/**
 * ExamAttempt Model - Model quản lý lượt thi
 */
class ExamAttempt extends Model {
    
    protected $table = 'exam_attempts';
    
    /**
     * Bắt đầu lượt thi mới
     */
    public function startExamAttempt($examId, $studentId) {
        // Kiểm tra đã có lượt thi nào chưa hoàn thành không
        $existingAttempt = $this->getIncompleteAttempt($examId, $studentId);
        if ($existingAttempt) {
            return $existingAttempt;
        }
        
        // Tạo lượt thi mới
        $attemptNumber = $this->getNextAttemptNumber($examId, $studentId);
        
        $data = [
            'exam_id' => $examId,
            'student_id' => $studentId,
            'attempt_number' => $attemptNumber,
            'start_time' => date('Y-m-d H:i:s'),
            'status' => 'in_progress',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $attemptId = $this->create($data);
        return $this->findById($attemptId);
    }
    
    /**
     * Lấy số lượt thi tiếp theo
     */
    private function getNextAttemptNumber($examId, $studentId) {
        $sql = "SELECT MAX(attempt_number) as max_attempt 
                FROM {$this->table} 
                WHERE exam_id = :exam_id AND student_id = :student_id";
        $result = $this->db->query($sql, [
            'exam_id' => $examId,
            'student_id' => $studentId
        ])->fetch();
        
        return ($result->max_attempt ?? 0) + 1;
    }
    
    /**
     * Lấy lượt thi chưa hoàn thành
     */
    public function getIncompleteAttempt($examId, $studentId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE exam_id = :exam_id AND student_id = :student_id 
                AND status = 'in_progress'";
        return $this->db->query($sql, [
            'exam_id' => $examId,
            'student_id' => $studentId
        ])->fetch();
    }
    
    /**
     * Hoàn thành lượt thi
     */
    public function completeExamAttempt($attemptId, $totalScore, $percentage) {
        $data = [
            'end_time' => date('Y-m-d H:i:s'),
            'total_score' => $totalScore,
            'percentage' => $percentage,
            'status' => 'completed',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($attemptId, $data);
    }
    
    /**
     * Đánh dấu lượt thi timeout
     */
    public function timeoutExamAttempt($attemptId) {
        $data = [
            'end_time' => date('Y-m-d H:i:s'),
            'status' => 'timeout',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($attemptId, $data);
    }
    
    /**
     * Lấy lượt thi với thông tin chi tiết
     */
    public function getAttemptWithDetails($attemptId) {
        $sql = "SELECT ea.*, e.title as exam_title, e.duration_minutes,
                       s.name as subject_name, u.full_name as student_name
                FROM {$this->table} ea
                LEFT JOIN exams e ON ea.exam_id = e.id
                LEFT JOIN subjects s ON e.subject_id = s.id
                LEFT JOIN users u ON ea.student_id = u.id
                WHERE ea.id = :id";
        return $this->db->query($sql, ['id' => $attemptId])->fetch();
    }
    
    /**
     * Lấy lịch sử thi của học sinh
     */
    public function getStudentAttemptHistory($studentId, $limit = 10) {
        $sql = "SELECT ea.*, e.title as exam_title, s.name as subject_name
                FROM {$this->table} ea
                LEFT JOIN exams e ON ea.exam_id = e.id
                LEFT JOIN subjects s ON e.subject_id = s.id
                WHERE ea.student_id = :student_id
                ORDER BY ea.created_at DESC
                LIMIT :limit";
        return $this->db->query($sql, [
            'student_id' => $studentId,
            'limit' => $limit
        ])->fetchAll();
    }
    
    /**
     * Lấy kết quả thi của đề thi
     */
    public function getExamResults($examId) {
        $sql = "SELECT ea.*, u.full_name as student_name, u.username
                FROM {$this->table} ea
                LEFT JOIN users u ON ea.student_id = u.id
                WHERE ea.exam_id = :exam_id AND ea.status = 'completed'
                ORDER BY ea.percentage DESC, ea.total_score DESC";
        return $this->db->query($sql, ['exam_id' => $examId])->fetchAll();
    }
    
    /**
     * Tính thời gian làm bài
     */
    public function getAttemptDuration($attemptId) {
        $attempt = $this->findById($attemptId);
        if ($attempt && $attempt->start_time && $attempt->end_time) {
            $start = new DateTime($attempt->start_time);
            $end = new DateTime($attempt->end_time);
            $diff = $start->diff($end);
            return $diff->format('%H:%I:%S');
        }
        return null;
    }
    
    /**
     * Kiểm tra thời gian còn lại
     */
    public function getRemainingTime($attemptId) {
        $attempt = $this->getAttemptWithDetails($attemptId);
        if (!$attempt || $attempt->status !== 'in_progress') {
            return 0;
        }
        
        $startTime = new DateTime($attempt->start_time);
        $now = new DateTime();
        $elapsed = $startTime->diff($now)->format('%i'); // minutes
        
        $remainingMinutes = $attempt->duration_minutes - $elapsed;
        return max(0, $remainingMinutes);
    }
}