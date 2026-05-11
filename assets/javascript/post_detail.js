// assets/javascript/post_detail.js

document.addEventListener('DOMContentLoaded', function() {
    const commentsList = document.getElementById('commentsList');
    const commentForm = document.getElementById('commentForm');
    const commentCount = document.getElementById('commentCount');
    const commentCount2 = document.getElementById('commentCount2');
    const reportModal = new bootstrap.Modal(document.getElementById('reportModal'));
    const reportForm = document.getElementById('reportForm');
    const reportCommentId = document.getElementById('reportCommentId');

    function fetchComments(page = 1) {
        fetch(`ajax/get_article_comments.php?article_id=${window.ARTICLE_ID}&page=${page}`)
            .then(res => res.json())
            .then(data => {
                renderComments(data.items);
                commentCount.textContent = data.total;
                commentCount2.textContent = data.total;
            })
            .catch(err => console.error('Error fetching comments:', err));
    }

    function renderComments(comments) {
        if (!comments || comments.length === 0) {
            commentsList.innerHTML = '<p class="text-muted text-center py-4">Chưa có bình luận nào. Hãy là người đầu tiên!</p>';
            return;
        }

        commentsList.innerHTML = comments.map(comment => `
            <div class="comment-item">
                <div class="d-flex gap-3">
                    <div class="comment-avatar">
                        ${comment.commenter_avatar ? `<img src="${comment.commenter_avatar}" class="rounded-circle w-100 h-100">` : comment.commenter_name.charAt(0)}
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <div class="comment-author">${comment.commenter_name}</div>
                            <div class="comment-date">${new Date(comment.created_at).toLocaleDateString('vi-VN')}</div>
                        </div>
                        <div class="comment-text">${comment.content}</div>
                        <div class="comment-actions">
                            <a onclick="replyTo('${comment.id}', '${comment.commenter_name}')"><i class="bi bi-reply me-1"></i>Trả lời</a>
                            <a onclick="openReportModal('${comment.id}')"><i class="bi bi-flag me-1"></i>Báo cáo</a>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('ajax/submit_article_comment.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    commentForm.reset();
                    fetchComments();
                } else {
                    alert(data.message || 'Không thể gửi bình luận.');
                }
            });
        });
    }

    window.openReportModal = function(commentId) {
        if (!window.CURRENT_USER_ID || window.CURRENT_USER_ID === 'null') {
            alert('Vui lòng đăng nhập để báo cáo bình luận.');
            return;
        }
        reportCommentId.value = commentId;
        reportModal.show();
    };

    reportForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('ajax/report_article_comment.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Cảm ơn bạn đã báo cáo. Chúng tôi sẽ xem xét nội dung này.');
                reportModal.hide();
                reportForm.reset();
            } else {
                alert(data.message || 'Không thể gửi báo cáo.');
            }
        });
    });

    // Initial fetch
    fetchComments();
});

function replyTo(id, name) {
    const textarea = document.querySelector('textarea[name="content"]');
    if (textarea) {
        textarea.value = `@${name}: `;
        textarea.focus();
    } else {
        alert('Vui lòng đăng nhập để trả lời.');
    }
}
