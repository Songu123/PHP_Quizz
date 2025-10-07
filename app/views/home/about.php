<?php require_once APP . '/views/partials/header.php'; ?>

<div class="row">
    <div class="col-md-8">
        <h1><?php echo $title; ?></h1>
        <p class="lead"><?php echo $message; ?></p>
        
        <h3>Về Framework MVC này</h3>
        <p>Framework này được xây dựng với các tính năng:</p>
        <ul>
            <li><strong>Routing đơn giản:</strong> URL được parse tự động thành Controller/Method/Params</li>
            <li><strong>Database PDO:</strong> Sử dụng PDO cho kết nối database an toàn</li>
            <li><strong>Model base class:</strong> Các method CRUD cơ bản cho Model</li>
            <li><strong>Controller base class:</strong> Load Model, View và các utility methods</li>
            <li><strong>View system:</strong> Hệ thống view với layout và partial</li>
            <li><strong>Session management:</strong> Quản lý session và flash messages</li>
        </ul>
        
        <h3>Cấu trúc thư mục</h3>
        <ul>
            <li><code>app/controllers/</code> - Các Controller</li>
            <li><code>app/models/</code> - Các Model</li>
            <li><code>app/views/</code> - Các View template</li>
            <li><code>app/core/</code> - Core classes (App, Controller, Model, Database)</li>
            <li><code>app/config/</code> - File cấu hình</li>
            <li><code>public/</code> - Thư mục public (CSS, JS, Images)</li>
        </ul>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Thông tin hệ thống</h5>
            </div>
            <div class="card-body">
                <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
                <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
                <p><strong>App Version:</strong> <?php echo APPVERSION; ?></p>
            </div>
        </div>
    </div>
</div>

<?php require_once APP . '/views/partials/footer.php'; ?>