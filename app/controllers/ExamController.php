<?php
/**
 * Exam Controller - Controller quản lý đề thi
 */
class ExamController extends Controller {
    
    private $examModel;
    private $subjectModel;
    private $questionModel;
    private $examQuestionModel;
    private $examAttemptModel;
    private $userModel;
    
    public function __construct() {
        $this->examModel = new Exam();
        $this->subjectModel = new Subject();
        $this->questionModel = new Question();
        $this->examQuestionModel = new ExamQuestion();
        $this->examAttemptModel = new ExamAttempt();
        $this->userModel = new User();
        
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
    }
    
    /**
     * Hiển thị danh sách đề thi
     */
    public function index() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if ($user->role === 'teacher') {
            $exams = $this->examModel->getExamsByTeacher($_SESSION['user_id']);
        } elseif ($user->role === 'student') {
            $exams = $this->examModel->getActiveExams();
        } else {
            $exams = $this->examModel->findAll();
        }
        
        // Thêm thông tin thống kê cho mỗi đề thi
        foreach ($exams as $exam) {
            $exam->question_count = $this->examQuestionModel->countExamQuestions($exam->id);
            if ($user->role !== 'student') {
                $exam->stats = $this->examModel->getExamStats($exam->id);
            }
        }
        
        $this->view('exams/index', [
            'exams' => $exams,
            'user' => $user
        ]);
    }
    
    /**
     * Hiển thị chi tiết đề thi
     */
    public function show($id) {
        $exam = $this->examModel->getExamWithDetails($id);
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$exam) {
            $this->redirect('/exams', 'Đề thi không tồn tại!', 'error');
            return;
        }
        
        // Kiểm tra quyền xem
        if ($user->role === 'teacher' && $exam->teacher_id != $_SESSION['user_id']) {
            $this->redirect('/exams', 'Bạn không có quyền xem đề thi này!', 'error');
            return;
        }
        
        $exam->questions = $this->examModel->getExamQuestions($exam->id);
        $exam->question_count = count($exam->questions);
        $exam->total_points = $this->examQuestionModel->calculateTotalPoints($exam->id);
        
        // Nếu là học sinh, kiểm tra đã làm bài chưa
        if ($user->role === 'student') {
            $exam->can_take = $this->examModel->canTakeExam($exam->id, $_SESSION['user_id']);
            $exam->attempt_history = $this->examAttemptModel->getStudentAttemptHistory($_SESSION['user_id'], 5);
        } else {
            $exam->stats = $this->examModel->getExamStats($exam->id);
            $exam->results = $this->examAttemptModel->getExamResults($exam->id);
        }
        
        $this->view('exams/show', [
            'exam' => $exam,
            'user' => $user
        ]);
    }
    
    /**
     * Hiển thị form tạo đề thi mới
     */
    public function create() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        // Chỉ admin và teacher mới được tạo đề thi
        if (!in_array($user->role, ['admin', 'teacher'])) {
            $this->redirect('/exams', 'Bạn không có quyền tạo đề thi!', 'error');
            return;
        }
        
        // Lấy danh sách môn học
        if ($user->role === 'teacher') {
            $subjects = $this->subjectModel->getSubjectsByTeacher($_SESSION['user_id']);
        } else {
            $subjects = $this->subjectModel->getActiveSubjects();
        }
        
        $this->view('exams/create', [
            'subjects' => $subjects,
            'user' => $user
        ]);
    }
    
    /**
     * Xử lý tạo đề thi mới
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/exams/create');
            return;
        }
        
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        $data = [
            'title' => trim($_POST['title']),
            'subject_id' => $_POST['subject_id'],
            'teacher_id' => $_SESSION['user_id'],
            'description' => trim($_POST['description']),
            'duration_minutes' => intval($_POST['duration_minutes']),
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'],
            'status' => $_POST['status'] ?? 'draft'
        ];
        
        // Validate
        $errors = $this->validateExamData($data);
        if (!empty($errors)) {
            $subjects = $user->role === 'teacher' 
                ? $this->subjectModel->getSubjectsByTeacher($_SESSION['user_id'])
                : $this->subjectModel->getActiveSubjects();
                
            $this->view('exams/create', [
                'errors' => $errors,
                'data' => $data,
                'subjects' => $subjects,
                'user' => $user
            ]);
            return;
        }
        
        $examId = $this->examModel->createExam($data);
        if ($examId) {
            $this->redirect('/exams/' . $examId . '/questions', 'Tạo đề thi thành công! Hãy thêm câu hỏi.', 'success');
        } else {
            $this->redirect('/exams/create', 'Có lỗi xảy ra!', 'error');
        }
    }
    
    /**
     * Hiển thị form quản lý câu hỏi trong đề thi
     */
    public function manageQuestions($id) {
        $exam = $this->examModel->getExamWithDetails($id);
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$exam) {
            $this->redirect('/exams', 'Đề thi không tồn tại!', 'error');
            return;
        }
        
        // Kiểm tra quyền
        if ($user->role === 'teacher' && $exam->teacher_id != $_SESSION['user_id']) {
            $this->redirect('/exams', 'Bạn không có quyền chỉnh sửa đề thi này!', 'error');
            return;
        }
        
        $examQuestions = $this->examQuestionModel->getExamQuestions($exam->id);
        $availableQuestions = $this->questionModel->getQuestionsBySubject($exam->subject_id);
        
        // Loại bỏ các câu hỏi đã có trong đề thi
        $usedQuestionIds = array_column($examQuestions, 'question_id');
        $availableQuestions = array_filter($availableQuestions, function($q) use ($usedQuestionIds) {
            return !in_array($q->id, $usedQuestionIds);
        });
        
        $this->view('exams/manage_questions', [
            'exam' => $exam,
            'examQuestions' => $examQuestions,
            'availableQuestions' => $availableQuestions,
            'user' => $user
        ]);
    }
    
    /**
     * Thêm câu hỏi vào đề thi
     */
    public function addQuestion($examId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/exams/' . $examId . '/questions');
            return;
        }
        
        $questionId = $_POST['question_id'];
        $points = floatval($_POST['points']) ?: 1.0;
        
        if ($this->examQuestionModel->addQuestionToExam($examId, $questionId, $points)) {
            // Cập nhật tổng câu hỏi và điểm của đề thi
            $this->updateExamTotals($examId);
            $this->redirect('/exams/' . $examId . '/questions', 'Thêm câu hỏi thành công!', 'success');
        } else {
            $this->redirect('/exams/' . $examId . '/questions', 'Có lỗi xảy ra!', 'error');
        }
    }
    
    /**
     * Xóa câu hỏi khỏi đề thi
     */
    public function removeQuestion($examId, $questionId) {
        if ($this->examQuestionModel->removeQuestionFromExam($examId, $questionId)) {
            $this->updateExamTotals($examId);
            $this->redirect('/exams/' . $examId . '/questions', 'Xóa câu hỏi thành công!', 'success');
        } else {
            $this->redirect('/exams/' . $examId . '/questions', 'Có lỗi xảy ra!', 'error');
        }
    }
    
    /**
     * Bắt đầu làm bài thi
     */
    public function startExam($id) {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if ($user->role !== 'student') {
            $this->redirect('/exams', 'Chỉ học sinh mới có thể làm bài thi!', 'error');
            return;
        }
        
        if (!$this->examModel->canTakeExam($id, $_SESSION['user_id'])) {
            $this->redirect('/exams', 'Bạn không thể làm bài thi này!', 'error');
            return;
        }
        
        $attempt = $this->examAttemptModel->startExamAttempt($id, $_SESSION['user_id']);
        if ($attempt) {
            $this->redirect('/exams/' . $id . '/take/' . $attempt->id);
        } else {
            $this->redirect('/exams', 'Có lỗi xảy ra!', 'error');
        }
    }
    
    /**
     * Hiển thị trang làm bài thi
     */
    public function takeExam($examId, $attemptId) {
        $attempt = $this->examAttemptModel->getAttemptWithDetails($attemptId);
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$attempt || $attempt->student_id != $_SESSION['user_id']) {
            $this->redirect('/exams', 'Lượt thi không hợp lệ!', 'error');
            return;
        }
        
        if ($attempt->status !== 'in_progress') {
            $this->redirect('/exams/' . $examId . '/result/' . $attemptId);
            return;
        }
        
        $questions = $this->examModel->getExamQuestionsWithOptions($examId);
        $remainingTime = $this->examAttemptModel->getRemainingTime($attemptId);
        
        // Nếu hết giờ, tự động nộp bài
        if ($remainingTime <= 0) {
            $this->submitExam($examId, $attemptId);
            return;
        }
        
        $this->view('exams/take', [
            'exam' => $this->examModel->findById($examId),
            'attempt' => $attempt,
            'questions' => $questions,
            'remainingTime' => $remainingTime,
            'user' => $user
        ]);
    }
    
    /**
     * Nộp bài thi
     */
    public function submitExam($examId, $attemptId) {
        $attempt = $this->examAttemptModel->findById($attemptId);
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$attempt || $attempt->student_id != $_SESSION['user_id']) {
            $this->redirect('/exams', 'Lượt thi không hợp lệ!', 'error');
            return;
        }
        
        // Tính điểm
        $studentAnswerModel = new StudentAnswer();
        $totalScore = $studentAnswerModel->calculateAttemptScore($attemptId);
        $percentage = $studentAnswerModel->calculateAttemptPercentage($attemptId);
        
        // Hoàn thành lượt thi
        $this->examAttemptModel->completeExamAttempt($attemptId, $totalScore, $percentage);
        
        $this->redirect('/exams/' . $examId . '/result/' . $attemptId);
    }
    
    /**
     * Hiển thị kết quả thi
     */
    public function showResult($examId, $attemptId) {
        $attempt = $this->examAttemptModel->getAttemptWithDetails($attemptId);
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$attempt) {
            $this->redirect('/exams', 'Kết quả thi không tồn tại!', 'error');
            return;
        }
        
        // Kiểm tra quyền xem kết quả
        if ($user->role === 'student' && $attempt->student_id != $_SESSION['user_id']) {
            $this->redirect('/exams', 'Bạn không có quyền xem kết quả này!', 'error');
            return;
        }
        
        $studentAnswerModel = new StudentAnswer();
        $report = $studentAnswerModel->getAttemptReport($attemptId);
        
        $this->view('exams/result', [
            'exam' => $this->examModel->findById($examId),
            'attempt' => $attempt,
            'report' => $report,
            'user' => $user
        ]);
    }
    
    /**
     * Cập nhật tổng số câu hỏi và điểm của đề thi
     */
    private function updateExamTotals($examId) {
        $questionCount = $this->examQuestionModel->countExamQuestions($examId);
        $totalPoints = $this->examQuestionModel->calculateTotalPoints($examId);
        
        $this->examModel->update($examId, [
            'total_questions' => $questionCount,
            'total_points' => $totalPoints
        ]);
    }
    
    /**
     * Validate dữ liệu đề thi
     */
    private function validateExamData($data) {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors['title'] = 'Tiêu đề đề thi không được để trống!';
        }
        
        if (empty($data['subject_id'])) {
            $errors['subject_id'] = 'Vui lòng chọn môn học!';
        }
        
        if ($data['duration_minutes'] <= 0) {
            $errors['duration_minutes'] = 'Thời gian thi phải lớn hơn 0!';
        }
        
        if (empty($data['start_time'])) {
            $errors['start_time'] = 'Vui lòng chọn thời gian bắt đầu!';
        }
        
        if (empty($data['end_time'])) {
            $errors['end_time'] = 'Vui lòng chọn thời gian kết thúc!';
        }
        
        if (!empty($data['start_time']) && !empty($data['end_time'])) {
            if (strtotime($data['start_time']) >= strtotime($data['end_time'])) {
                $errors['end_time'] = 'Thời gian kết thúc phải sau thời gian bắt đầu!';
            }
        }
        
        return $errors;
    }
}