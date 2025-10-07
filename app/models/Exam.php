<?php
/**
 * Exam Model - Model quản lý đề thi
 */
class Exam extends Model {
    
    protected $table = 'exams';
    
    /**
     * Lấy đề thi theo teacher_id
     */
    public function getExamsByTeacher($teacherId) {
        return $this->findWhere(['teacher_id' => $teacherId], 'created_at DESC');
    }
    
    /**
     * Lấy đề thi theo subject_id
     */
    public function getExamsBySubject($subjectId) {
        return $this->findWhere(['subject_id' => $subjectId], 'created_at DESC');
    }
    
    /**
     * Lấy đề thi đang active
     */
    public function getActiveExams() {
        $sql = "SELECT e.*, s.name as subject_name, u.full_name as teacher_name
                FROM {$this->table} e
                LEFT JOIN subjects s ON e.subject_id = s.id
                LEFT JOIN users u ON e.teacher_id = u.id
                WHERE e.status = 'active' 
                AND e.start_time <= NOW() 
                AND e.end_time >= NOW()
                ORDER BY e.start_time ASC";
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * Lấy đề thi với thông tin chi tiết
     */
    public function getExamWithDetails($examId) {
        $sql = "SELECT e.*, s.name as subject_name, s.code as subject_code,
                       u.full_name as teacher_name, u.email as teacher_email
                FROM {$this->table} e
                LEFT JOIN subjects s ON e.subject_id = s.id
                LEFT JOIN users u ON e.teacher_id = u.id
                WHERE e.id = :id";
        return $this->db->query($sql, ['id' => $examId])->fetch();
    }
    
    /**
     * Tạo đề thi mới
     */
    public function createExam($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
    
    /**
     * Cập nhật đề thi
     */
    public function updateExam($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->update($id, $data);
    }
    
    /**
     * Lấy câu hỏi của đề thi
     */
    public function getExamQuestions($examId) {
        $sql = "SELECT q.*, eq.points as exam_points, eq.question_order,
                       s.name as subject_name
                FROM exam_questions eq
                JOIN questions q ON eq.question_id = q.id
                LEFT JOIN subjects s ON q.subject_id = s.id
                WHERE eq.exam_id = :exam_id
                ORDER BY eq.question_order ASC";
        return $this->db->query($sql, ['exam_id' => $examId])->fetchAll();
    }
    
    /**
     * Lấy câu hỏi của đề thi với options
     */
    public function getExamQuestionsWithOptions($examId) {
        $questions = $this->getExamQuestions($examId);
        
        foreach ($questions as $question) {
            $sql = "SELECT * FROM question_options WHERE question_id = :question_id ORDER BY option_order";
            $options = $this->db->query($sql, ['question_id' => $question->id])->fetchAll();
            $question->options = $options;
        }
        
        return $questions;
    }
    
    /**
     * Kiểm tra đề thi có thể làm bài không
     */
    public function canTakeExam($examId, $studentId) {
        $exam = $this->findById($examId);
        
        if (!$exam || $exam->status !== 'active') {
            return false;
        }
        
        $now = date('Y-m-d H:i:s');
        if ($now < $exam->start_time || $now > $exam->end_time) {
            return false;
        }
        
        // Kiểm tra đã làm bài chưa
        $sql = "SELECT COUNT(*) as count FROM exam_attempts 
                WHERE exam_id = :exam_id AND student_id = :student_id AND status = 'completed'";
        $result = $this->db->query($sql, [
            'exam_id' => $examId,
            'student_id' => $studentId
        ])->fetch();
        
        return $result->count == 0;
    }
    
    /**
     * Đếm số câu hỏi trong đề thi
     */
    public function countExamQuestions($examId) {
        $sql = "SELECT COUNT(*) as count FROM exam_questions WHERE exam_id = :exam_id";
        $result = $this->db->query($sql, ['exam_id' => $examId])->fetch();
        return $result->count;
    }
    
    /**
     * Lấy thống kê đề thi
     */
    public function getExamStats($examId) {
        $sql = "SELECT 
                    COUNT(*) as total_attempts,
                    AVG(total_score) as avg_score,
                    AVG(percentage) as avg_percentage,
                    MAX(total_score) as max_score,
                    MIN(total_score) as min_score
                FROM exam_attempts 
                WHERE exam_id = :exam_id AND status = 'completed'";
        return $this->db->query($sql, ['exam_id' => $examId])->fetch();
    }
}