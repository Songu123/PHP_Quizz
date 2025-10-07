<?php
/**
 * Student Controller - Controller quản lý học sinh và hoạt động học tập
 */
class StudentController extends Controller {
    
    private $userModel;
    private $examModel;
    private $examAttemptModel;
    private $studentAnswerModel;
    private $subjectModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->examModel = new Exam();
        $this->examAttemptModel = new ExamAttempt();
        $this->studentAnswerModel = new StudentAnswer();
        $this->subjectModel = new Subject();
        
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
    }
    
    /**
     * Dashboard học sinh
     */
    public function dashboard() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if ($user->role !== 'student') {
            $this->redirect('/dashboard', 'Trang này chỉ dành cho học sinh!', 'error');
            return;
        }
        
        // Lấy các đề thi có thể làm
        $availableExams = $this->examModel->getActiveExams();
        $availableExams = array_filter($availableExams, function($exam) {
            return $this->examModel->canTakeExam($exam->id, $_SESSION['user_id']);
        });
        
        // Lấy lịch sử thi gần đây
        $recentAttempts = $this->examAttemptModel->getStudentAttemptHistory($_SESSION['user_id'], 5);
        
        // Lấy thống kê của học sinh
        $stats = $this->getStudentStats($_SESSION['user_id']);
        
        $this->view('students/dashboard', [
            'user' => $user,
            'availableExams' => $availableExams,
            'recentAttempts' => $recentAttempts,
            'stats' => $stats
        ]);
    }
    
    /**
     * Danh sách đề thi có thể làm
     */
    public function availableExams() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if ($user->role !== 'student') {
            $this->redirect('/dashboard', 'Trang này chỉ dành cho học sinh!', 'error');
            return;
        }
        
        $exams = $this->examModel->getActiveExams();
        
        // Kiểm tra từng đề thi xem có thể làm không
        foreach ($exams as $exam) {
            $exam->can_take = $this->examModel->canTakeExam($exam->id, $_SESSION['user_id']);
            $exam->question_count = $this->examModel->countExamQuestions($exam->id);
        }
        
        $this->view('students/available_exams', [
            'user' => $user,
            'exams' => $exams
        ]);
    }
    
    /**
     * Lịch sử thi của học sinh
     */
    public function examHistory() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if ($user->role !== 'student') {
            $this->redirect('/dashboard', 'Trang này chỉ dành cho học sinh!', 'error');
            return;
        }
        
        $attempts = $this->examAttemptModel->getStudentAttemptHistory($_SESSION['user_id'], 50);
        
        // Thêm thông tin chi tiết cho mỗi lượt thi
        foreach ($attempts as $attempt) {
            $attempt->duration = $this->examAttemptModel->getAttemptDuration($attempt->id);
        }
        
        $this->view('students/exam_history', [
            'user' => $user,
            'attempts' => $attempts
        ]);
    }
    
    /**
     * Lưu câu trả lời (AJAX)
     */
    public function saveAnswer() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $attemptId = $_POST['attempt_id'] ?? null;
        $questionId = $_POST['question_id'] ?? null;
        $selectedOptionId = $_POST['selected_option_id'] ?? null;
        $answerText = $_POST['answer_text'] ?? null;
        
        if (!$attemptId || !$questionId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }
        
        // Kiểm tra quyền của học sinh
        $attempt = $this->examAttemptModel->findById($attemptId);
        if (!$attempt || $attempt->student_id != $_SESSION['user_id'] || $attempt->status !== 'in_progress') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid attempt']);
            exit;
        }
        
        // Lưu câu trả lời
        $result = $this->studentAnswerModel->saveAnswer($attemptId, $questionId, $selectedOptionId, $answerText);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool)$result]);
        exit;
    }
    
    /**
     * Lấy thời gian còn lại (AJAX)
     */
    public function getRemainingTime($attemptId) {
        $attempt = $this->examAttemptModel->findById($attemptId);
        
        if (!$attempt || $attempt->student_id != $_SESSION['user_id']) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid attempt']);
            exit;
        }
        
        $remainingMinutes = $this->examAttemptModel->getRemainingTime($attemptId);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'remaining_minutes' => $remainingMinutes,
            'remaining_seconds' => $remainingMinutes * 60
        ]);
        exit;
    }
    
    /**
     * Nộp bài thi
     */
    public function submitExam() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/students/dashboard');
            return;
        }
        
        $attemptId = $_POST['attempt_id'] ?? null;
        
        if (!$attemptId) {
            $this->redirect('/students/dashboard', 'Lượt thi không hợp lệ!', 'error');
            return;
        }
        
        $attempt = $this->examAttemptModel->findById($attemptId);
        
        if (!$attempt || $attempt->student_id != $_SESSION['user_id'] || $attempt->status !== 'in_progress') {
            $this->redirect('/students/dashboard', 'Lượt thi không hợp lệ!', 'error');
            return;
        }
        
        // Tính điểm
        $totalScore = $this->studentAnswerModel->calculateAttemptScore($attemptId);
        $percentage = $this->studentAnswerModel->calculateAttemptPercentage($attemptId);
        
        // Hoàn thành lượt thi
        if ($this->examAttemptModel->completeExamAttempt($attemptId, $totalScore, $percentage)) {
            $this->redirect('/students/result/' . $attemptId, 'Nộp bài thành công!', 'success');
        } else {
            $this->redirect('/students/dashboard', 'Có lỗi xảy ra khi nộp bài!', 'error');
        }
    }
    
    /**
     * Xem kết quả thi
     */
    public function viewResult($attemptId) {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $attempt = $this->examAttemptModel->getAttemptWithDetails($attemptId);
        
        if (!$attempt || $attempt->student_id != $_SESSION['user_id']) {
            $this->redirect('/students/dashboard', 'Kết quả thi không tồn tại!', 'error');
            return;
        }
        
        $report = $this->studentAnswerModel->getAttemptReport($attemptId);
        
        $this->view('students/result', [
            'user' => $user,
            'attempt' => $attempt,
            'report' => $report
        ]);
    }
    
    /**
     * Xem chi tiết bài làm
     */
    public function viewDetailedResult($attemptId) {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $attempt = $this->examAttemptModel->getAttemptWithDetails($attemptId);
        
        if (!$attempt || $attempt->student_id != $_SESSION['user_id']) {
            $this->redirect('/students/dashboard', 'Kết quả thi không tồn tại!', 'error');
            return;
        }
        
        $answers = $this->studentAnswerModel->getAttemptAnswers($attemptId);
        
        // Lấy thông tin câu hỏi và options cho từng câu trả lời
        $questionModel = new Question();
        foreach ($answers as $answer) {
            $question = $questionModel->getQuestionWithOptions($answer->question_id);
            $answer->question_detail = $question;
        }
        
        $this->view('students/detailed_result', [
            'user' => $user,
            'attempt' => $attempt,
            'answers' => $answers
        ]);
    }
    
    /**
     * Lấy thống kê của học sinh
     */
    private function getStudentStats($studentId) {
        // Tạo instance database để truy vấn
        $db = new Database();
        
        // Tổng số lượt thi
        $sql = "SELECT COUNT(*) as total_attempts FROM exam_attempts WHERE student_id = :student_id";
        $totalAttempts = $db->query($sql, ['student_id' => $studentId])->fetch()->total_attempts;
        
        // Số lượt thi hoàn thành
        $sql = "SELECT COUNT(*) as completed_attempts FROM exam_attempts 
                WHERE student_id = :student_id AND status = 'completed'";
        $completedAttempts = $db->query($sql, ['student_id' => $studentId])->fetch()->completed_attempts;
        
        // Điểm trung bình
        $sql = "SELECT AVG(percentage) as avg_percentage FROM exam_attempts 
                WHERE student_id = :student_id AND status = 'completed'";
        $avgPercentage = $db->query($sql, ['student_id' => $studentId])->fetch()->avg_percentage ?? 0;
        
        // Điểm cao nhất
        $sql = "SELECT MAX(percentage) as max_percentage FROM exam_attempts 
                WHERE student_id = :student_id AND status = 'completed'";
        $maxPercentage = $db->query($sql, ['student_id' => $studentId])->fetch()->max_percentage ?? 0;
        
        // Số môn học đã thi
        $sql = "SELECT COUNT(DISTINCT e.subject_id) as subjects_count 
                FROM exam_attempts ea 
                JOIN exams e ON ea.exam_id = e.id 
                WHERE ea.student_id = :student_id AND ea.status = 'completed'";
        $subjectsCount = $db->query($sql, ['student_id' => $studentId])->fetch()->subjects_count;
        
        return [
            'total_attempts' => $totalAttempts,
            'completed_attempts' => $completedAttempts,
            'avg_percentage' => round($avgPercentage, 2),
            'max_percentage' => round($maxPercentage, 2),
            'subjects_count' => $subjectsCount
        ];
    }
}