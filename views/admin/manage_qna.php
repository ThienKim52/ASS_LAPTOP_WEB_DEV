<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">

      <div class="content-header mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="bi bi-question-circle"></i> Quản lý Hỏi & Đáp</h2>
            <p class="text-muted">Thêm, sửa hoặc xoá các câu hỏi thường gặp hiển thị trên website.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#qnaModal" id="addNewBtn">
          <i class="bi bi-plus-circle me-1"></i> Thêm câu hỏi mới
        </button>
      </div>

      <div class="content-card">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="qnaTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 50px;">#</th>
                                <th style="width: 30%;">Câu hỏi</th>
                                <th>Câu trả lời</th>
                                <th class="pe-4 text-end" style="width: 150px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dữ liệu được load từ AJAX -->
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Đang tải...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
      </div>
</div>

  <!-- Modal for Add/Edit Q&A -->
  <div class="modal fade" id="qnaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="qnaForm">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalTitle">Thêm Hỏi & Đáp</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="qnaId">
            <div class="mb-3">
              <label for="qnaQuestion" class="form-label fw-bold">Câu hỏi *</label>
              <input type="text" id="qnaQuestion" class="form-control" placeholder="Nhập câu hỏi..." required>
            </div>
            <div class="mb-3">
              <label for="qnaAnswer" class="form-label fw-bold">Câu trả lời *</label>
              <textarea id="qnaAnswer" class="form-control" rows="5" placeholder="Nhập câu trả lời chi tiết..." required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
            <button type="submit" class="btn btn-primary">Lưu thông tin</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="assets/javascript/admin_manage_qna.js"></script>
<?php include 'views/layouts/admin_footer.php'; ?>
