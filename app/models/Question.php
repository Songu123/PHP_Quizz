<?php
/**
 * Question Model - Model quản lý câu hỏi
 */
class Question extends Model {
    
    protected $table = 'questions';
    
    /**
     * Lấy câu hỏi theo subject_id
     */
    public function getQuestionsBySubject($subjectId, $status = 'active') {
        return $this->findWhere([
            'subject_id' => $subjectId, 
            'status' => $status
        ], 'created_at DESC');
    }
    
    /**
     * Lấy câu hỏi theo độ khó
     */
    public function getQuestionsByDifficulty($difficulty, $subjectId = null) {
        $conditions = ['difficulty' => $difficulty, 'status' => 'active'];
        if ($subjectId) {
            $conditions['subject_id'] = $subjectId;
        }
        return $this->findWhere($conditions);
    }
    
    /**
     * Lấy câu hỏi với các lựa chọn
     */
    public function getQuestionWithOptions($questionId) {
        $sql = "SELECT q.*, s.name as subject_name, u.full_name as creator_name
                FROM {$this->table} q
                LEFT JOIN subjects s ON q.subject_id = s.id
                LEFT JOIN users u ON q.created_by = u.id
                WHERE q.id = :id";
        $question = $this->db->query($sql, ['id' => $questionId])->fetch();
        
        if ($question) {
            // Lấy các options
            $sql = "SELECT * FROM question_options WHERE question_id = :question_id ORDER BY option_order";
            $options = $this->db->query($sql, ['question_id' => $questionId])->fetchAll();
            $question->options = $options;
        }
        
        return $question;
    }
    
    /**
     * Tạo câu hỏi mới với options
     */
    public function createQuestionWithOptions($questionData, $options) {
        try {
            $this->db->beginTransaction();
            
            $questionData['created_at'] = date('Y-m-d H:i:s');
            $questionData['updated_at'] = date('Y-m-d H:i:s');
            
            // Tạo câu hỏi
            $questionId = $this->create($questionData);
            
            // Tạo options
            if ($questionId && !empty($options)) {
                $optionModel = new QuestionOption();
                foreach ($options as $order => $option) {
                    $optionData = [
                        'question_id' => $questionId,
                        'option_text' => $option['text'],
                        'is_correct' => $option['is_correct'] ?? false,
                        'option_order' => $order + 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $optionModel->create($optionData);
                }
            }
            
            $this->db->commit();
            return $questionId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    /**
     * Lấy câu hỏi ngẫu nhiên theo môn học và độ khó
     */
    public function getRandomQuestions($subjectId, $difficulty = null, $limit = 10) {
        $sql = "SELECT * FROM {$this->table} WHERE subject_id = :subject_id AND status = 'active'";
        $params = ['subject_id' => $subjectId];
        
        if ($difficulty) {
            $sql .= " AND difficulty = :difficulty";
            $params['difficulty'] = $difficulty;
        }
        
        $sql .= " ORDER BY RAND() LIMIT :limit";
        $params['limit'] = $limit;
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    /**
     * Lấy thống kê câu hỏi theo môn học
     */
    public function getQuestionStats($subjectId) {
        $sql = "SELECT 
                    difficulty,
                    COUNT(*) as count,
                    AVG(points) as avg_points
                FROM {$this->table} 
                WHERE subject_id = :subject_id AND status = 'active'
                GROUP BY difficulty";
        return $this->db->query($sql, ['subject_id' => $subjectId])->fetchAll();
    }
}
