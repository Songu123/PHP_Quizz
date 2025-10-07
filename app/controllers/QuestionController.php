<?php
/**
 * Question Controller - Controller quản lý câu hỏi
 */
class QuestionController extends Controller {
    
    private $questionModel;
    private $subjectModel;
    private $userModel;
    private $questionOptionModel;
    
    public function __construct() {
        $this->questionModel = new Question();
        $this->subjectModel = new Subject();
        $this->userModel = new User();
        $this->questionOptionModel = new QuestionOption();
        
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
    }
    
    /**
     * Hiển thị danh sách câu hỏi
     */
    public function index() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $subjectId = $_GET['subject_id'] ?? null;
        $difficulty = $_GET['difficulty'] ?? null;
        
        // Lấy danh sách môn học theo role
        if ($user->role === 'teacher') {
            $subjects = $this->subjectModel->getSubjectsByTeacher($_SESSION['user_id']);
        } else {
            $subjects = $this->subjectModel->getActiveSubjects();
        }
        
        // Lấy câu hỏi theo filter
        $questions = [];
        if ($subjectId) {
            if ($difficulty) {
                $questions = $this->questionModel->getQuestionsByDifficulty($difficulty, $subjectId);
            } else {
                $questions = $this->questionModel->getQuestionsBySubject($subjectId);
            }
        }
        
        $this->view('questions/index', [
            'questions' => $questions,
            'subjects' => $subjects,
            'selectedSubject' => $subjectId,
            'selectedDifficulty' => $difficulty,
            'user' => $user
        ]);
    }
    
    /**
     * Hiển thị chi tiết câu hỏi
     */
    public function show($id) {
        $question = $this->questionModel->getQuestionWithOptions($id);
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$question) {
            $this->redirect('/questions', 'Câu hỏi không tồn tại!', 'error');
            return;
        }
        
        // Kiểm tra quyền xem (teacher chỉ xem câu hỏi của môn học mình dạy)
        if ($user->role === 'teacher') {
            $subject = $this->subjectModel->findById($question->subject_id);
            if ($subject->teacher_id != $_SESSION['user_id']) {
                $this->redirect('/questions', 'Bạn không có quyền xem câu hỏi này!', 'error');
                return;
            }
        }
        
        $this->view('questions/show', [
            'question' => $question,
            'user' => $user
        ]);
    }
    
    /**
     * Hiển thị form tạo câu hỏi mới
     */
    public function create() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        // Chỉ admin và teacher mới được tạo câu hỏi
        if (!in_array($user->role, ['admin', 'teacher'])) {
            $this->redirect('/questions', 'Bạn không có quyền tạo câu hỏi!', 'error');
            return;
        }
        
        // Lấy danh sách môn học
        if ($user->role === 'teacher') {
            $subjects = $this->subjectModel->getSubjectsByTeacher($_SESSION['user_id']);
        } else {
            $subjects = $this->subjectModel->getActiveSubjects();
        }
        
        $this->view('questions/create', [
            'subjects' => $subjects,
            'user' => $user
        ]);
    }
    
    /**
     * Xử lý tạo câu hỏi mới
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/questions/create');
            return;
        }
        
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        $questionData = [
            'subject_id' => $_POST['subject_id'],
            'question_text' => trim($_POST['question_text']),
            'question_type' => $_POST['question_type'],
            'difficulty' => $_POST['difficulty'],
            'points' => floatval($_POST['points']),
            'explanation' => trim($_POST['explanation']),
            'created_by' => $_SESSION['user_id'],
            'status' => 'active'
        ];
        
        // Validate
        $errors = $this->validateQuestionData($questionData);
        if (!empty($errors)) {
            $this->view('questions/create', [
                'errors' => $errors,
                'data' => $questionData,
                'user' => $user
            ]);
            return;
        }
        
        // Xử lý options
        $options = [];
        if (isset($_POST['options'])) {
            foreach ($_POST['options'] as $index => $optionText) {
                if (!empty(trim($optionText))) {
                    $options[] = [
                        'text' => trim($optionText),
                        'is_correct' => isset($_POST['correct_options']) && in_array($index, $_POST['correct_options'])
                    ];
                }
            }
        }
        
        // Validate options
        if (empty($options)) {
            $this->view('questions/create', [
                'errors' => ['options' => 'Phải có ít nhất một lựa chọn!'],
                'data' => $questionData,
                'user' => $user
            ]);
            return;
        }
        
        $correctCount = count(array_filter($options, function($opt) { return $opt['is_correct']; }));
        if ($correctCount === 0) {
            $this->view('questions/create', [
                'errors' => ['correct_options' => 'Phải có ít nhất một đáp án đúng!'],
                'data' => $questionData,
                'user' => $user
            ]);
            return;
        }
        
        if ($this->questionModel->createQuestionWithOptions($questionData, $options)) {
            $this->redirect('/questions', 'Tạo câu hỏi thành công!', 'success');
        } else {
            $this->redirect('/questions/create', 'Có lỗi xảy ra!', 'error');
        }
    }
    
    /**
     * Hiển thị form chỉnh sửa câu hỏi
     */
    public function edit($id) {
        $question = $this->questionModel->getQuestionWithOptions($id);
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$question) {
            $this->redirect('/questions', 'Câu hỏi không tồn tại!', 'error');
            return;
        }
        
        // Kiểm tra quyền sửa
        if ($user->role === 'teacher') {
            $subject = $this->subjectModel->findById($question->subject_id);
            if ($subject->teacher_id != $_SESSION['user_id']) {
                $this->redirect('/questions', 'Bạn không có quyền sửa câu hỏi này!', 'error');
                return;
            }
        }
        
        // Lấy danh sách môn học
        if ($user->role === 'teacher') {
            $subjects = $this->subjectModel->getSubjectsByTeacher($_SESSION['user_id']);
        } else {
            $subjects = $this->subjectModel->getActiveSubjects();
        }
        
        $this->view('questions/edit', [
            'question' => $question,
            'subjects' => $subjects,
            'user' => $user
        ]);
    }
    
    /**
     * Xử lý cập nhật câu hỏi
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/questions/' . $id . '/edit');
            return;
        }
        
        $question = $this->questionModel->findById($id);
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$question) {
            $this->redirect('/questions', 'Câu hỏi không tồn tại!', 'error');
            return;
        }
        
        $questionData = [
            'subject_id' => $_POST['subject_id'],
            'question_text' => trim($_POST['question_text']),
            'question_type' => $_POST['question_type'],
            'difficulty' => $_POST['difficulty'],
            'points' => floatval($_POST['points']),
            'explanation' => trim($_POST['explanation']),
            'status' => $_POST['status'] ?? 'active'
        ];
        
        // Validate
        $errors = $this->validateQuestionData($questionData);
        if (!empty($errors)) {
            $question = $this->questionModel->getQuestionWithOptions($id);
            $this->view('questions/edit', [
                'errors' => $errors,
                'question' => (object)array_merge((array)$question, $questionData),
                'user' => $user
            ]);
            return;
        }
        
        // Xử lý options
        $options = [];
        if (isset($_POST['options'])) {
            foreach ($_POST['options'] as $index => $optionText) {
                if (!empty(trim($optionText))) {
                    $options[] = [
                        'text' => trim($optionText),
                        'is_correct' => isset($_POST['correct_options']) && in_array($index, $_POST['correct_options'])
                    ];
                }
            }
        }
        
        // Cập nhật câu hỏi và options
        if ($this->questionModel->update($id, $questionData)) {
            if (!empty($options)) {
                $this->questionOptionModel->updateQuestionOptions($id, $options);
            }
            $this->redirect('/questions', 'Cập nhật câu hỏi thành công!', 'success');
        } else {
            $this->redirect('/questions/' . $id . '/edit', 'Có lỗi xảy ra!', 'error');
        }
    }
    
    /**
     * Xóa câu hỏi
     */
    public function destroy($id) {
        $question = $this->questionModel->findById($id);
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$question) {
            $this->redirect('/questions', 'Câu hỏi không tồn tại!', 'error');
            return;
        }
        
        // Kiểm tra quyền xóa
        if ($user->role === 'teacher') {
            $subject = $this->subjectModel->findById($question->subject_id);
            if ($subject->teacher_id != $_SESSION['user_id']) {
                $this->redirect('/questions', 'Bạn không có quyền xóa câu hỏi này!', 'error');
                return;
            }
        }
        
        if ($this->questionModel->delete($id)) {
            $this->redirect('/questions', 'Xóa câu hỏi thành công!', 'success');
        } else {
            $this->redirect('/questions', 'Có lỗi xảy ra!', 'error');
        }
    }
    
    /**
     * API lấy câu hỏi ngẫu nhiên
     */
    public function getRandomQuestions() {
        $subjectId = $_GET['subject_id'] ?? null;
        $difficulty = $_GET['difficulty'] ?? null;
        $limit = $_GET['limit'] ?? 10;
        
        if (!$subjectId) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Subject ID is required']);
            exit;
        }
        
        $questions = $this->questionModel->getRandomQuestions($subjectId, $difficulty, $limit);
        
        header('Content-Type: application/json');
        echo json_encode($questions);
        exit;
    }
    
    /**
     * Validate dữ liệu câu hỏi
     */
    private function validateQuestionData($data) {
        $errors = [];
        
        if (empty($data['subject_id'])) {
            $errors['subject_id'] = 'Vui lòng chọn môn học!';
        }
        
        if (empty($data['question_text'])) {
            $errors['question_text'] = 'Nội dung câu hỏi không được để trống!';
        }
        
        if (!in_array($data['question_type'], ['multiple_choice', 'true_false'])) {
            $errors['question_type'] = 'Loại câu hỏi không hợp lệ!';
        }
        
        if (!in_array($data['difficulty'], ['easy', 'medium', 'hard'])) {
            $errors['difficulty'] = 'Độ khó không hợp lệ!';
        }
        
        if ($data['points'] <= 0) {
            $errors['points'] = 'Điểm phải lớn hơn 0!';
        }
        
        return $errors;
    }
}