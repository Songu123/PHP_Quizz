<?php
/**
 * Subject Controller - Controller quản lý môn học
 */
class SubjectController extends Controller {
    
    private $subjectModel;
    private $userModel;
    
    public function __construct() {
        $this->subjectModel = new Subject();
        $this->userModel = new User();
        
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
    }
    
    /**
     * Hiển thị danh sách môn học
     */
    public function index() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if ($user->role === 'teacher') {
            // Giáo viên chỉ xem môn học của mình
            $subjects = $this->subjectModel->getSubjectsByTeacher($_SESSION['user_id']);
        } else {
            // Admin xem tất cả môn học
            $subjects = $this->subjectModel->findAll();
        }
        
        // Lấy số lượng câu hỏi cho mỗi môn học
        foreach ($subjects as $subject) {
            $subject->question_count = $this->subjectModel->getQuestionCount($subject->id);
        }
        
        $this->view('subjects/index', [
            'subjects' => $subjects,
            'user' => $user
        ]);
    }
    
    /**
     * Hiển thị chi tiết môn học
     */
    public function show($id) {
        $subject = $this->subjectModel->getSubjectWithTeacher($id);
        
        if (!$subject) {
            $this->redirect('/subjects', 'Môn học không tồn tại!', 'error');
            return;
        }
        
        // Kiểm tra quyền xem
        $user = $this->userModel->findById($_SESSION['user_id']);
        if ($user->role === 'teacher' && $subject->teacher_id != $_SESSION['user_id']) {
            $this->redirect('/subjects', 'Bạn không có quyền xem môn học này!', 'error');
            return;
        }
        
        $subject->question_count = $this->subjectModel->getQuestionCount($subject->id);
        
        $this->view('subjects/show', [
            'subject' => $subject,
            'user' => $user
        ]);
    }
    
    /**
     * Hiển thị form tạo môn học mới
     */
    public function create() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        // Chỉ admin và teacher mới được tạo môn học
        if (!in_array($user->role, ['admin', 'teacher'])) {
            $this->redirect('/subjects', 'Bạn không có quyền tạo môn học!', 'error');
            return;
        }
        
        // Lấy danh sách giáo viên (nếu là admin)
        $teachers = [];
        if ($user->role === 'admin') {
            $teachers = $this->userModel->findWhere(['role' => 'teacher'], 'full_name ASC');
        }
        
        $this->view('subjects/create', [
            'user' => $user,
            'teachers' => $teachers
        ]);
    }
    
    /**
     * Xử lý tạo môn học mới
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/subjects/create');
            return;
        }
        
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        $data = [
            'name' => trim($_POST['name']),
            'code' => strtoupper(trim($_POST['code'])),
            'description' => trim($_POST['description']),
            'teacher_id' => $user->role === 'admin' ? $_POST['teacher_id'] : $_SESSION['user_id'],
            'status' => $_POST['status'] ?? 'active'
        ];
        
        // Validate
        $errors = $this->validateSubjectData($data);
        if (!empty($errors)) {
            $this->view('subjects/create', [
                'errors' => $errors,
                'data' => $data,
                'user' => $user
            ]);
            return;
        }
        
        // Kiểm tra code đã tồn tại chưa
        if ($this->subjectModel->findByCode($data['code'])) {
            $this->view('subjects/create', [
                'errors' => ['code' => 'Mã môn học đã tồn tại!'],
                'data' => $data,
                'user' => $user
            ]);
            return;
        }
        
        if ($this->subjectModel->createSubject($data)) {
            $this->redirect('/subjects', 'Tạo môn học thành công!', 'success');
        } else {
            $this->redirect('/subjects/create', 'Có lỗi xảy ra!', 'error');
        }
    }
    
    /**
     * Hiển thị form chỉnh sửa môn học
     */
    public function edit($id) {
        $subject = $this->subjectModel->findById($id);
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$subject) {
            $this->redirect('/subjects', 'Môn học không tồn tại!', 'error');
            return;
        }
        
        // Kiểm tra quyền sửa
        if ($user->role === 'teacher' && $subject->teacher_id != $_SESSION['user_id']) {
            $this->redirect('/subjects', 'Bạn không có quyền sửa môn học này!', 'error');
            return;
        }
        
        // Lấy danh sách giáo viên (nếu là admin)
        $teachers = [];
        if ($user->role === 'admin') {
            $teachers = $this->userModel->findWhere(['role' => 'teacher'], 'full_name ASC');
        }
        
        $this->view('subjects/edit', [
            'subject' => $subject,
            'user' => $user,
            'teachers' => $teachers
        ]);
    }
    
    /**
     * Xử lý cập nhật môn học
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/subjects/' . $id . '/edit');
            return;
        }
        
        $subject = $this->subjectModel->findById($id);
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$subject) {
            $this->redirect('/subjects', 'Môn học không tồn tại!', 'error');
            return;
        }
        
        $data = [
            'name' => trim($_POST['name']),
            'code' => strtoupper(trim($_POST['code'])),
            'description' => trim($_POST['description']),
            'status' => $_POST['status'] ?? 'active'
        ];
        
        // Admin có thể thay đổi giáo viên
        if ($user->role === 'admin') {
            $data['teacher_id'] = $_POST['teacher_id'];
        }
        
        // Validate
        $errors = $this->validateSubjectData($data);
        if (!empty($errors)) {
            $this->view('subjects/edit', [
                'errors' => $errors,
                'subject' => (object)array_merge((array)$subject, $data),
                'user' => $user
            ]);
            return;
        }
        
        // Kiểm tra code đã tồn tại chưa (trừ chính nó)
        $existingSubject = $this->subjectModel->findByCode($data['code']);
        if ($existingSubject && $existingSubject->id != $id) {
            $this->view('subjects/edit', [
                'errors' => ['code' => 'Mã môn học đã tồn tại!'],
                'subject' => (object)array_merge((array)$subject, $data),
                'user' => $user
            ]);
            return;
        }
        
        if ($this->subjectModel->updateSubject($id, $data)) {
            $this->redirect('/subjects', 'Cập nhật môn học thành công!', 'success');
        } else {
            $this->redirect('/subjects/' . $id . '/edit', 'Có lỗi xảy ra!', 'error');
        }
    }
    
    /**
     * Xóa môn học
     */
    public function destroy($id) {
        $subject = $this->subjectModel->findById($id);
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$subject) {
            $this->redirect('/subjects', 'Môn học không tồn tại!', 'error');
            return;
        }
        
        // Chỉ admin mới được xóa môn học
        if ($user->role !== 'admin') {
            $this->redirect('/subjects', 'Bạn không có quyền xóa môn học!', 'error');
            return;
        }
        
        if ($this->subjectModel->delete($id)) {
            $this->redirect('/subjects', 'Xóa môn học thành công!', 'success');
        } else {
            $this->redirect('/subjects', 'Có lỗi xảy ra!', 'error');
        }
    }
    
    /**
     * Validate dữ liệu môn học
     */
    private function validateSubjectData($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'Tên môn học không được để trống!';
        }
        
        if (empty($data['code'])) {
            $errors['code'] = 'Mã môn học không được để trống!';
        } elseif (!preg_match('/^[A-Z0-9]{3,20}$/', $data['code'])) {
            $errors['code'] = 'Mã môn học chỉ được chứa chữ hoa và số, từ 3-20 ký tự!';
        }
        
        if (empty($data['teacher_id'])) {
            $errors['teacher_id'] = 'Vui lòng chọn giáo viên!';
        }
        
        return $errors;
    }
}