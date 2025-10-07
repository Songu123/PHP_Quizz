<?php
/**
 * QuestionOption Model - Model quản lý lựa chọn câu hỏi
 */
class QuestionOption extends Model {
    
    protected $table = 'question_options';
    
    /**
     * Lấy options theo question_id
     */
    public function getOptionsByQuestion($questionId) {
        return $this->findWhere(['question_id' => $questionId], 'option_order ASC');
    }
    
    /**
     * Lấy đáp án đúng của câu hỏi
     */
    public function getCorrectOption($questionId) {
        $sql = "SELECT * FROM {$this->table} WHERE question_id = :question_id AND is_correct = 1";
        return $this->db->query($sql, ['question_id' => $questionId])->fetch();
    }
    
    /**
     * Lấy tất cả đáp án đúng (trường hợp nhiều đáp án đúng)
     */
    public function getCorrectOptions($questionId) {
        return $this->findWhere([
            'question_id' => $questionId,
            'is_correct' => 1
        ], 'option_order ASC');
    }
    
    /**
     * Tạo option mới
     */
    public function createOption($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
    
    /**
     * Cập nhật tất cả options của một câu hỏi
     */
    public function updateQuestionOptions($questionId, $options) {
        try {
            $this->db->beginTransaction();
            
            // Xóa options cũ
            $this->deleteByQuestion($questionId);
            
            // Thêm options mới
            foreach ($options as $order => $option) {
                $optionData = [
                    'question_id' => $questionId,
                    'option_text' => $option['text'],
                    'is_correct' => $option['is_correct'] ?? false,
                    'option_order' => $order + 1,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->create($optionData);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    /**
     * Xóa tất cả options của một câu hỏi
     */
    public function deleteByQuestion($questionId) {
        $sql = "DELETE FROM {$this->table} WHERE question_id = :question_id";
        return $this->db->query($sql, ['question_id' => $questionId]);
    }
    
    /**
     * Kiểm tra đáp án có đúng không
     */
    public function isCorrectOption($optionId) {
        $sql = "SELECT is_correct FROM {$this->table} WHERE id = :id";
        $result = $this->db->query($sql, ['id' => $optionId])->fetch();
        return $result ? (bool)$result->is_correct : false;
    }
}