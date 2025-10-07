<?php
/**
 * ExamQuestion Model - Model quản lý câu hỏi trong đề thi
 */
class ExamQuestion extends Model {
    
    protected $table = 'exam_questions';
    
    /**
     * Thêm câu hỏi vào đề thi
     */
    public function addQuestionToExam($examId, $questionId, $points = 1.0, $order = null) {
        if (!$order) {
            $order = $this->getNextQuestionOrder($examId);
        }
        
        $data = [
            'exam_id' => $examId,
            'question_id' => $questionId,
            'question_order' => $order,
            'points' => $points,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->create($data);
    }
    
    /**
     * Lấy số thứ tự câu hỏi tiếp theo
     */
    private function getNextQuestionOrder($examId) {
        $sql = "SELECT MAX(question_order) as max_order FROM {$this->table} WHERE exam_id = :exam_id";
        $result = $this->db->query($sql, ['exam_id' => $examId])->fetch();
        return ($result->max_order ?? 0) + 1;
    }
    
    /**
     * Xóa câu hỏi khỏi đề thi
     */
    public function removeQuestionFromExam($examId, $questionId) {
        $sql = "DELETE FROM {$this->table} WHERE exam_id = :exam_id AND question_id = :question_id";
        return $this->db->query($sql, [
            'exam_id' => $examId,
            'question_id' => $questionId
        ]);
    }
    
    /**
     * Cập nhật thứ tự câu hỏi
     */
    public function updateQuestionOrder($examId, $questionId, $newOrder) {
        $sql = "UPDATE {$this->table} SET question_order = :order 
                WHERE exam_id = :exam_id AND question_id = :question_id";
        return $this->db->query($sql, [
            'order' => $newOrder,
            'exam_id' => $examId,
            'question_id' => $questionId
        ]);
    }
    
    /**
     * Cập nhật điểm số câu hỏi
     */
    public function updateQuestionPoints($examId, $questionId, $points) {
        $sql = "UPDATE {$this->table} SET points = :points 
                WHERE exam_id = :exam_id AND question_id = :question_id";
        return $this->db->query($sql, [
            'points' => $points,
            'exam_id' => $examId,
            'question_id' => $questionId
        ]);
    }
    
    /**
     * Lấy danh sách câu hỏi của đề thi
     */
    public function getExamQuestions($examId) {
        $sql = "SELECT eq.*, q.question_text, q.question_type, q.difficulty
                FROM {$this->table} eq
                JOIN questions q ON eq.question_id = q.id
                WHERE eq.exam_id = :exam_id
                ORDER BY eq.question_order ASC";
        return $this->db->query($sql, ['exam_id' => $examId])->fetchAll();
    }
    
    /**
     * Đếm số câu hỏi trong đề thi
     */
    public function countExamQuestions($examId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE exam_id = :exam_id";
        $result = $this->db->query($sql, ['exam_id' => $examId])->fetch();
        return $result->count;
    }
    
    /**
     * Tính tổng điểm của đề thi
     */
    public function calculateTotalPoints($examId) {
        $sql = "SELECT SUM(points) as total FROM {$this->table} WHERE exam_id = :exam_id";
        $result = $this->db->query($sql, ['exam_id' => $examId])->fetch();
        return $result->total ?? 0;
    }
    
    /**
     * Thêm nhiều câu hỏi vào đề thi
     */
    public function addMultipleQuestions($examId, $questions) {
        try {
            $this->db->beginTransaction();
            
            foreach ($questions as $question) {
                $this->addQuestionToExam(
                    $examId,
                    $question['question_id'],
                    $question['points'] ?? 1.0,
                    $question['order'] ?? null
                );
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    /**
     * Xóa tất cả câu hỏi khỏi đề thi
     */
    public function clearExamQuestions($examId) {
        $sql = "DELETE FROM {$this->table} WHERE exam_id = :exam_id";
        return $this->db->query($sql, ['exam_id' => $examId]);
    }
}