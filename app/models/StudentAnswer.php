<?php
/**
 * StudentAnswer Model - Model quản lý câu trả lời của học sinh
 */
class StudentAnswer extends Model {
    
    protected $table = 'student_answers';
    
    /**
     * Lưu câu trả lời của học sinh
     */
    public function saveAnswer($attemptId, $questionId, $selectedOptionId = null, $answerText = null) {
        // Kiểm tra đã trả lời câu này chưa
        $existingAnswer = $this->getStudentAnswer($attemptId, $questionId);
        
        $data = [
            'attempt_id' => $attemptId,
            'question_id' => $questionId,
            'selected_option_id' => $selectedOptionId,
            'answer_text' => $answerText,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Kiểm tra đáp án có đúng không và tính điểm
        $this->evaluateAnswer($data);
        
        if ($existingAnswer) {
            // Cập nhật câu trả lời
            unset($data['created_at']);
            $data['updated_at'] = date('Y-m-d H:i:s');
            return $this->update($existingAnswer->id, $data);
        } else {
            // Tạo câu trả lời mới
            return $this->create($data);
        }
    }
    
    /**
     * Đánh giá và tính điểm cho câu trả lời
     */
    private function evaluateAnswer(&$data) {
        if ($data['selected_option_id']) {
            // Kiểm tra option có đúng không
            $optionModel = new QuestionOption();
            $isCorrect = $optionModel->isCorrectOption($data['selected_option_id']);
            $data['is_correct'] = $isCorrect;
            
            if ($isCorrect) {
                // Lấy điểm của câu hỏi từ exam_questions
                $sql = "SELECT points FROM exam_questions eq
                        JOIN exam_attempts ea ON eq.exam_id = ea.exam_id
                        WHERE ea.id = :attempt_id AND eq.question_id = :question_id";
                $result = $this->db->query($sql, [
                    'attempt_id' => $data['attempt_id'],
                    'question_id' => $data['question_id']
                ])->fetch();
                
                $data['points_earned'] = $result ? $result->points : 0;
            } else {
                $data['points_earned'] = 0;
            }
        } else {
            // Câu hỏi tự luận - cần chấm thủ công
            $data['is_correct'] = false;
            $data['points_earned'] = 0;
        }
    }
    
    /**
     * Lấy câu trả lời của học sinh
     */
    public function getStudentAnswer($attemptId, $questionId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE attempt_id = :attempt_id AND question_id = :question_id";
        return $this->db->query($sql, [
            'attempt_id' => $attemptId,
            'question_id' => $questionId
        ])->fetch();
    }
    
    /**
     * Lấy tất cả câu trả lời của một lượt thi
     */
    public function getAttemptAnswers($attemptId) {
        $sql = "SELECT sa.*, q.question_text, q.question_type,
                       qo.option_text as selected_option_text,
                       eq.points as max_points
                FROM {$this->table} sa
                LEFT JOIN questions q ON sa.question_id = q.id
                LEFT JOIN question_options qo ON sa.selected_option_id = qo.id
                LEFT JOIN exam_questions eq ON q.id = eq.question_id
                LEFT JOIN exam_attempts ea ON sa.attempt_id = ea.id AND eq.exam_id = ea.exam_id
                WHERE sa.attempt_id = :attempt_id
                ORDER BY eq.question_order";
        return $this->db->query($sql, ['attempt_id' => $attemptId])->fetchAll();
    }
    
    /**
     * Tính tổng điểm của lượt thi
     */
    public function calculateAttemptScore($attemptId) {
        $sql = "SELECT SUM(points_earned) as total_score FROM {$this->table} 
                WHERE attempt_id = :attempt_id";
        $result = $this->db->query($sql, ['attempt_id' => $attemptId])->fetch();
        return $result->total_score ?? 0;
    }
    
    /**
     * Tính phần trăm điểm
     */
    public function calculateAttemptPercentage($attemptId) {
        $totalEarned = $this->calculateAttemptScore($attemptId);
        
        // Lấy tổng điểm tối đa
        $sql = "SELECT SUM(eq.points) as max_score
                FROM exam_questions eq
                JOIN exam_attempts ea ON eq.exam_id = ea.exam_id
                WHERE ea.id = :attempt_id";
        $result = $this->db->query($sql, ['attempt_id' => $attemptId])->fetch();
        $maxScore = $result->max_score ?? 0;
        
        if ($maxScore > 0) {
            return round(($totalEarned / $maxScore) * 100, 2);
        }
        
        return 0;
    }
    
    /**
     * Đếm số câu đúng
     */
    public function countCorrectAnswers($attemptId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE attempt_id = :attempt_id AND is_correct = 1";
        $result = $this->db->query($sql, ['attempt_id' => $attemptId])->fetch();
        return $result->count;
    }
    
    /**
     * Lấy báo cáo chi tiết lượt thi
     */
    public function getAttemptReport($attemptId) {
        $answers = $this->getAttemptAnswers($attemptId);
        $totalScore = $this->calculateAttemptScore($attemptId);
        $percentage = $this->calculateAttemptPercentage($attemptId);
        $correctCount = $this->countCorrectAnswers($attemptId);
        
        return [
            'answers' => $answers,
            'total_score' => $totalScore,
            'percentage' => $percentage,
            'correct_count' => $correctCount,
            'total_questions' => count($answers)
        ];
    }
    
    /**
     * Chấm điểm thủ công cho câu tự luận
     */
    public function manualGrading($answerId, $points, $isCorrect) {
        $data = [
            'points_earned' => $points,
            'is_correct' => $isCorrect,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($answerId, $data);
    }
}