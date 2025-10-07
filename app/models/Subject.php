<?php
/**
 * Subject Model - Model quản lý môn học
 */
class Subject extends Model {
    
    protected $table = 'subjects';
    
    /**
     * Lấy tất cả môn học có trạng thái active
     */
    public function getActiveSubjects() {
        return $this->findWhere(['status' => 'active'], 'name ASC');
    }
    
    /**
     * Lấy môn học theo teacher_id
     */
    public function getSubjectsByTeacher($teacherId) {
        return $this->findWhere(['teacher_id' => $teacherId], 'name ASC');
    }
    
    /**
     * Lấy môn học theo code
     */
    public function findByCode($code) {
        $sql = "SELECT * FROM {$this->table} WHERE code = :code";
        return $this->db->query($sql, ['code' => $code])->fetch();
    }
    
    /**
     * Tạo môn học mới
     */
    public function createSubject($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
    
    /**
     * Cập nhật môn học
     */
    public function updateSubject($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->update($id, $data);
    }
    
    /**
     * Lấy thông tin môn học với thông tin giáo viên
     */
    public function getSubjectWithTeacher($id) {
        $sql = "SELECT s.*, u.full_name as teacher_name, u.email as teacher_email 
                FROM {$this->table} s 
                LEFT JOIN users u ON s.teacher_id = u.id 
                WHERE s.id = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }
    
    /**
     * Lấy số lượng câu hỏi theo môn học
     */
    public function getQuestionCount($subjectId) {
        $sql = "SELECT COUNT(*) as count FROM questions WHERE subject_id = :subject_id AND status = 'active'";
        $result = $this->db->query($sql, ['subject_id' => $subjectId])->fetch();
        return $result->count;
    }
}