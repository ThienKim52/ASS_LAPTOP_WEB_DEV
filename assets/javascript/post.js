// assets/javascript/post.js

document.addEventListener('DOMContentLoaded', function() {
    const postsContainer = document.getElementById('postsContainer');
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    const searchInfo = document.getElementById('searchInfo');
    const resultCount = document.getElementById('resultCount');
    const clearSearch = document.getElementById('clearSearch');
    const paginationNav = document.getElementById('paginationNav');
    const paginationList = document.getElementById('paginationList');
    const noResults = document.getElementById('noResults');

    let currentPage = 1;
    let currentSearch = '';

    // Extract search from URL if present
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search')) {
        currentSearch = urlParams.get('search');
        searchInput.value = currentSearch;
    }

    function fetchPosts(page = 1, search = '') {
        // Show skeletons
        const skeletons = document.querySelectorAll('.article-skeleton');
        skeletons.forEach(s => s.classList.remove('d-none'));
        postsContainer.innerHTML = '';
        skeletons.forEach(s => postsContainer.appendChild(s));
        
        noResults.classList.add('d-none');
        paginationNav.classList.add('d-none');

        const params = new URLSearchParams({
            page: page,
            search: search,
            limit: 6
        });

        fetch(`${window.POST_API_URL}?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                postsContainer.innerHTML = '';
                
                if (data.items && data.items.length > 0) {
                    data.items.forEach(post => {
                        const card = createPostCard(post);
                        postsContainer.appendChild(card);
                    });

                    renderPagination(data.page, data.total_pages);
                    paginationNav.classList.remove('d-none');

                    if (search) {
                        searchInfo.classList.remove('d-none');
                        resultCount.textContent = `Tìm thấy ${data.total} kết quả cho "${search}"`;
                    } else {
                        searchInfo.classList.add('d-none');
                    }
                } else {
                    noResults.classList.remove('d-none');
                    searchInfo.classList.add('d-none');
                }
            })
            .catch(err => {
                console.error('Error fetching posts:', err);
                postsContainer.innerHTML = '<div class="alert alert-danger">Không thể tải bài viết. Vui lòng thử lại sau.</div>';
            });
    }

    function createPostCard(post) {
        const div = document.createElement('div');
        div.className = 'col-lg-4 col-md-6';
        
        const date = new Date(post.published_at).toLocaleDateString('vi-VN');
        const excerpt = post.content.replace(/<[^>]*>/g, '').substring(0, 120) + '...';
        const img = post.thumbnail_url || 'assets/img/placeholder.png';

        div.innerHTML = `
            <div class="article-card">
                <div class="article-img-wrapper">
                    <img src="${img}" class="article-img" alt="${post.title}" onerror="this.onerror=null; this.src='assets/img/placeholder.png'">
                </div>
                <div class="article-body">
                    <div class="article-meta">
                        <span><i class="bi bi-calendar3 me-1"></i> ${date}</span>
                    </div>
                    <h3 class="article-title">
                        <a href="?page=post_detail&id=${post.id}">${post.title}</a>
                    </h3>
                    <p class="article-excerpt">${excerpt}</p>
                    <a href="?page=post_detail&id=${post.id}" class="btn-read-more">
                        Đọc tiếp <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        `;
        return div;
    }

    function renderPagination(current, total) {
        paginationList.innerHTML = '';
        if (total <= 1) return;

        // Previous
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${current === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" data-page="${current - 1}">Trước</a>`;
        paginationList.appendChild(prevLi);

        for (let i = 1; i <= total; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === current ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            paginationList.appendChild(li);
        }

        // Next
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${current === total ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" data-page="${current + 1}">Sau</a>`;
        paginationList.appendChild(nextLi);

        // Add event listeners
        paginationList.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                if (page > 0 && page <= total && page !== current) {
                    currentPage = page;
                    fetchPosts(currentPage, currentSearch);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        });
    }

    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        currentSearch = searchInput.value.trim();
        currentPage = 1;
        fetchPosts(currentPage, currentSearch);
    });

    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        currentSearch = '';
        currentPage = 1;
        fetchPosts(currentPage, currentSearch);
    });

    // Initial fetch
    fetchPosts(currentPage, currentSearch);
});
