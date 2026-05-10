<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">

      <div class="content-header mb-4">
        <h2><i class="bi bi-envelope"></i> Quản lý liên hệ</h2>
        <p>Xem, đánh dấu trạng thái (đã đọc / chưa đọc / đã phản hồi) và xoá các liên hệ từ khách hàng.</p>
      </div>

      <div class="content-card">
        <!-- Filter Bar -->
        <div class="row mb-3 g-2 align-items-end">
          <div class="col-md-4">
            <label class="form-label small text-muted">Tìm kiếm</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input type="text" class="form-control" id="contactSearch" placeholder="Tên, email hoặc chủ đề...">
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label small text-muted">Lọc trạng thái</label>
            <select class="form-select" id="contactStatusFilter">
              <option value="">Tất cả</option>
              <option value="unread">Chưa đọc</option>
              <option value="read">Đã đọc</option>
              <option value="replied">Đã phản hồi</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small text-muted">Hiển thị</label>
            <select class="form-select" id="contactPerPage">
              <option value="10" selected>10</option>
              <option value="25">25</option>
              <option value="50">50</option>
            </select>
          </div>
          <div class="col-md-3 text-end">
            <span class="badge bg-secondary" id="contactTotal">0 liên hệ</span>
          </div>
        </div>

      <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap" id="contactTable">
          <thead>
            <tr>
              <th width="5%">#</th>
              <th width="15%">Họ tên</th>
              <th width="20%">Email</th>
              <th width="15%">Chủ đề</th>
              <th width="15%">Ngày gửi</th>
              <th width="12%">Trạng thái</th>
              <th width="18%">Thao tác</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small" id="contactPaginationInfo">Hiển thị 0 / 0</div>
        <nav>
          <ul class="pagination pagination-sm mb-0" id="contactPagination"></ul>
        </nav>
      </div>

      </div>
</div>

  <!-- View Contact Modal -->
  <div class="modal fade" id="viewContactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-envelope-open me-2"></i>Chi tiết liên hệ</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2"><strong>Người gửi:</strong> <span id="contactViewName"></span></div>
          <div class="mb-2"><strong>Email:</strong> <span id="contactViewEmail"></span></div>
          <div class="mb-2"><strong>Ngày gửi:</strong> <span id="contactViewDate"></span></div>
          <hr>
          <strong>Chủ đề:</strong>
          <p id="contactSubject" class="fw-bold"></p>
          <strong>Nội dung:</strong>
          <p id="contactMessage" class="bg-light p-3 rounded" style="white-space: pre-wrap;"></p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Confirm Modal -->
  <div class="modal fade" id="deleteContactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <div class="modal-body text-center pt-4">
          <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
          <h5 class="mt-2">Xoá liên hệ này?</h5>
          <p class="text-muted small">Hành động này không thể hoàn tác.</p>
        </div>
        <div class="modal-footer justify-content-center border-0 pt-0">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
          <button class="btn btn-danger" id="confirmDeleteBtn">Xoá</button>
        </div>
      </div>
    </div>
  </div>
  
  <script src="assets/javascript/admin_manage_contacts.js"></script>
<?php include 'views/layouts/admin_footer.php'; ?>
