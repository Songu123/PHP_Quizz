<?php
/**
 * Teacher Controller - Controller quản lý hoạt động giảng dạy
 */
class TeacherController extends Controller {
    
    private $userModel;
    private $subjectModel;
    private $questionModel;
    private $examModel;
    private $examAttemptModel;
    private $studentAnswerModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->subjectModel = new Subject();
        $this->questionModel = new Question();
        $this->examModel = new Exam();
        $this->examAttemptModel = new ExamAttempt();
        $this->studentAnswerModel = new StudentAnswer();
        
        // Kiểm tra đăng nhập và quyền
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
        
        $user = $this->userModel->findById($_SESSION['user_id']);
        if ($user->role !== 'teacher') {
            header('Location: /dashboard');
            exit;
        }
    }
    
    /**
     * Dashboard giáo viên
     */
    public function dashboard() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        // Lấy các môn học của giáo viên
        $subjects = $this->subjectModel->getSubjectsByTeacher($_SESSION['user_id']);
        
        // Lấy các đề thi gần đây
        $recentExams = $this->examModel->getExamsByTeacher($_SESSION['user_id']);
        $recentExams = array_slice($recentExams, 0, 5); // 5 đề thi gần nhất
        
        // Lấy thống kê tổng quan
        $stats = $this->getTeacherStats($_SESSION['user_id']);
        
        $this->view('teachers/dashboard', [
            'user' => $user,
            'subjects' => $subjects,
            'recentExams' => $recentExams,
            'stats' => $stats
        ]);
    }
    
    /**
     * Quản lý môn học
     */
    public function subjects() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $subjects = $this->subjectModel->getSubjectsByTeacher($_SESSION['user_id']);
        
        // Thêm thống kê cho mỗi môn học
        foreach ($subjects as $subject) {
            $subject->question_count = $this->subjectModel->getQuestionCount($subject->id);
            $subject->exam_count = $this->getSubjectExamCount($subject->id);
        }
        
        $this->view('teachers/subjects', [
            'user' => $user,
            'subjects' => $subjects
        ]);
    }
    
    /**
     * Quản lý câu hỏi
     */
    public function questions() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $subjects = $this->subjectModel->getSubjectsByTeacher($_SESSION['user_id']);
        
        $subjectId = $_GET['subject_id'] ?? null;
        $difficulty = $_GET['difficulty'] ?? null;
        
        $questions = [];
        if ($subjectId) {
            if ($difficulty) {
                $questions = $this->questionModel->getQuestionsByDifficulty($difficulty, $subjectId);
            } else {
                $questions = $this->questionModel->getQuestionsBySubject($subjectId);
            }
        }
        
        $this->view('teachers/questions', [
            'user' => $user,
            'subjects' => $subjects,
            'questions' => $questions,
            'selectedSubject' => $subjectId,
            'selectedDifficulty' => $difficulty
        ]);
    }
    
    /**
     * Quản lý đề thi
     */
    public function exams() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $exams = $this->examModel->getExamsByTeacher($_SESSION['user_id']);
        
        // Thêm thống kê cho mỗi đề thi
        foreach ($exams as $exam) {
            $exam->question_count = $this->examModel->countExamQuestions($exam->id);
            $exam->stats = $this->examModel->getExamStats($exam->id);
        }
        
        $this->view('teachers/exams', [
            'user' => $user,
            'exams' => $exams
        ]);
    }
    
    /**
     * Xem kết quả thi của một đề thi
     */
    public function examResults($examId) {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $exam = $this->examModel->getExamWithDetails($examId);
        
        if (!$exam || $exam->teacher_id != $_SESSION['user_id']) {
            $this->redirect('/teachers/exams', 'Bạn không có quyền xem đề thi này!', 'error');
            return;
        }
        
        $results = $this->examAttemptModel->getExamResults($examId);
        $stats = $this->examModel->getExamStats($examId);
        
        $this->view('teachers/exam_results', [
            'user' => $user,
            'exam' => $exam,
            'results' => $results,
            'stats' => $stats
        ]);
    }
    
    /**
     * Xem chi tiết bài làm của học sinh
     */
    public function studentResult($attemptId) {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $attempt = $this->examAttemptModel->getAttemptWithDetails($attemptId);
        
        if (!$attempt) {
            $this->redirect('/teachers/exams', 'Lượt thi không tồn tại!', 'error');
            return;
        }
        
        // Kiểm tra quyền xem (phải là đề thi của giáo viên này)
        $exam = $this->examModel->findById($attempt->exam_id);
        if ($exam->teacher_id != $_SESSION['user_id']) {
            $this->redirect('/teachers/exams', 'Bạn không có quyền xem kết quả này!', 'error');
            return;
        }
        
        $answers = $this->studentAnswerModel->getAttemptAnswers($attemptId);
        
        // Lấy thông tin chi tiết câu hỏi
        foreach ($answers as $answer) {
            $question = $this->questionModel->getQuestionWithOptions($answer->question_id);
            $answer->question_detail = $question;
        }
        
        $this->view('teachers/student_result', [
            'user' => $user,
            'attempt' => $attempt,
            'answers' => $answers
        ]);
    }
    
    /**
     * Chấm điểm thủ công
     */
    public function manualGrading() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/teachers/exams');
            return;
        }
        
        $answerId = $_POST['answer_id'];
        $points = floatval($_POST['points']);
        $isCorrect = isset($_POST['is_correct']) ? 1 : 0;
        
        if ($this->studentAnswerModel->manualGrading($answerId, $points, $isCorrect)) {
            // Cập nhật lại tổng điểm của lượt thi
            $answer = $this->studentAnswerModel->findById($answerId);
            $totalScore = $this->studentAnswerModel->calculateAttemptScore($answer->attempt_id);
            $percentage = $this->studentAnswerModel->calculateAttemptPercentage($answer->attempt_id);
            
            $this->examAttemptModel->update($answer->attempt_id, [
                'total_score' => $totalScore,
                'percentage' => $percentage
            ]);
            
            $this->redirect('/teachers/student-result/' . $answer->attempt_id, 'Chấm điểm thành công!', 'success');
        } else {
            $this->redirect('/teachers/exams', 'Có lỗi xảy ra!', 'error');
        }
    }
    
    /**
     * Báo cáo thống kê tổng quan
     */
    public function reports() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $subjects = $this->subjectModel->getSubjectsByTeacher($_SESSION['user_id']);
        
        $selectedSubject = $_GET['subject_id'] ?? null;
        
        // Thống kê theo môn học
        $subjectStats = [];
        if ($selectedSubject) {
            $subjectStats = $this->getSubjectDetailedStats($selectedSubject);
        }
        
        $this->view('teachers/reports', [
            'user' => $user,
            'subjects' => $subjects,
            'selectedSubject' => $selectedSubject,
            'subjectStats' => $subjectStats
        ]);
    }
    
    /**
     * Export kết quả thi ra Excel/CSV
     */
    public function exportResults($examId) {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $exam = $this->examModel->findById($examId);
        
        if (!$exam || $exam->teacher_id != $_SESSION['user_id']) {
            $this->redirect('/teachers/exams', 'Bạn không có quyền xuất kết quả đề thi này!', 'error');
            return;
        }
        
        $results = $this->examAttemptModel->getExamResults($examId);
        
        // Tạo CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="ket_qua_thi_' . $examId . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // BOM cho UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header
        fputcsv($output, [
            'STT',
            'Tên học sinh',
            'Username',
            'Thời gian bắt đầu',
            'Thời gian kết thúc',
            'Điểm số',
            'Phần trăm',
            'Trạng thái'
        ]);
        
        // Dữ liệu
        $index = 1;
        foreach ($results as $result) {
            fputcsv($output, [
                $index++,
                $result->student_name,
                $result->username,
                $result->start_time,
                $result->end_time,
                $result->total_score,
                $result->percentage . '%',
                $result->status
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Lấy thống kê tổng quan của giáo viên
     */
    private function getTeacherStats($teacherId) {
        $db = new Database();
        
        // Số môn học
        $sql = "SELECT COUNT(*) as count FROM subjects WHERE teacher_id = :teacher_id";
        $subjectCount = $db->query($sql, ['teacher_id' => $teacherId])->fetch()->count;
        
        // Số câu hỏi
        $sql = "SELECT COUNT(*) as count FROM questions WHERE created_by = :teacher_id";
        $questionCount = $db->query($sql, ['teacher_id' => $teacherId])->fetch()->count;
        
        // Số đề thi
        $sql = "SELECT COUNT(*) as count FROM exams WHERE teacher_id = :teacher_id";
        $examCount = $db->query($sql, ['teacher_id' => $teacherId])->fetch()->count;
        
        // Số học sinh đã thi
        $sql = "SELECT COUNT(DISTINCT ea.student_id) as count 
                FROM exam_attempts ea 
                JOIN exams e ON ea.exam_id = e.id 
                WHERE e.teacher_id = :teacher_id AND ea.status = 'completed'";
        $studentCount = $db->query($sql, ['teacher_id' => $teacherId])->fetch()->count;
        
        return [
            'subject_count' => $subjectCount,
            'question_count' => $questionCount,
            'exam_count' => $examCount,
            'student_count' => $studentCount
        ];
    }
    
    /**
     * Lấy số đề thi của môn học
     */
    private function getSubjectExamCount($subjectId) {
        $db = new Database();
        $sql = "SELECT COUNT(*) as count FROM exams WHERE subject_id = :subject_id";
        $result = $db->query($sql, ['subject_id' => $subjectId])->fetch();
        return $result->count;
    }
    
    /**
     * Lấy thống kê chi tiết của môn học
     */
    private function getSubjectDetailedStats($subjectId) {
        $db = new Database();
        
        // Thống kê câu hỏi theo độ khó
        $questionStats = $this->questionModel->getQuestionStats($subjectId);
        
        // Thống kê điểm trung bình theo đề thi
        $sql = "SELECT e.title, AVG(ea.percentage) as avg_percentage, COUNT(ea.id) as attempt_count
                FROM exams e
                LEFT JOIN exam_attempts ea ON e.id = ea.exam_id AND ea.status = 'completed'
                WHERE e.subject_id = :subject_id
                GROUP BY e.id, e.title
                ORDER BY e.created_at DESC";
        $examStats = $db->query($sql, ['subject_id' => $subjectId])->fetchAll();
        
        return [
            'question_stats' => $questionStats,
            'exam_stats' => $examStats
        ];
    }
}