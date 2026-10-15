import './bootstrap';
import Chart from 'chart.js/auto';
import $ from 'jquery';
window.jQuery = $;
window.$ = $;
import 'admin-lte/plugins/jquery/jquery.min.js';
import 'admin-lte/plugins/datatables/jquery.dataTables.min.js';
import 'admin-lte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js';
import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';
import 'admin-lte/dist/css/adminlte.min.css';

document.querySelectorAll('.js-rating-star-chart').forEach((canvas) => {
    const counts = JSON.parse(canvas.dataset.counts || '[]');
    const labels = JSON.parse(canvas.dataset.labels || '[]');

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data: counts,
                backgroundColor: ['#f4b740', '#f6c96a', '#f8d894', '#cfd8e3', '#adb5bd'],
                borderWidth: 0,
                borderRadius: 4,
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { drawBorder: false },
                },
                y: {
                    grid: { display: false, drawBorder: false },
                },
            },
        },
    });
});

const reviewPageRoot = document.getElementById('review-page');

if (reviewPageRoot) {
    const unreadCountElement = document.getElementById('review-unread-count');
    const unreadBadge = document.getElementById('review-unread-badge');
    const markReadUrlTemplate = reviewPageRoot.dataset.markReadUrlTemplate || '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const pendingReviewIds = new Set();
    const readReviewIds = new Set();

    let unreadCount = Number(reviewPageRoot.dataset.unreadCount || 0);

    const updateUnreadBadge = (nextCount) => {
        unreadCount = Math.max(0, Number(nextCount) || 0);

        if (unreadCountElement) {
            unreadCountElement.textContent = String(unreadCount);
        }

        if (unreadBadge) {
            unreadBadge.classList.toggle('d-none', unreadCount === 0);
        }
    };

    updateUnreadBadge(unreadCount);

    const markReviewAsRead = async (reviewCard) => {
        const reviewId = reviewCard.dataset.reviewId;
        if (!reviewId || pendingReviewIds.has(reviewId) || readReviewIds.has(reviewId)) {
            return;
        }

        pendingReviewIds.add(reviewId);

        try {
            const endpoint = markReadUrlTemplate.replace('__REVIEW__', reviewId);
            const response = await window.axios.post(
                endpoint,
                {},
                {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken || '',
                    },
                }
            );

            readReviewIds.add(reviewId);
            reviewCard.dataset.readAt = new Date().toISOString();
            reviewCard.classList.remove('border-warning-subtle');
            updateUnreadBadge(response?.data?.unread_count ?? unreadCount - 1);
        } catch (error) {
            // Ignore network blips; observer will re-attempt once visible again.
        } finally {
            pendingReviewIds.delete(reviewId);
        }
    };

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting || entry.intersectionRatio < 0.5) {
                    return;
                }

                const reviewCard = entry.target;
                const reviewId = reviewCard.dataset.reviewId;
                const alreadyRead = Boolean(reviewCard.dataset.readAt);

                if (!reviewId || alreadyRead || readReviewIds.has(reviewId)) {
                    observer.unobserve(reviewCard);
                    return;
                }

                markReviewAsRead(reviewCard).then(() => {
                    observer.unobserve(reviewCard);
                });
            });
        },
        { threshold: [0.5] }
    );

    document.querySelectorAll('.js-review-card').forEach((reviewCard) => {
        if (reviewCard.dataset.readAt) {
            return;
        }

        observer.observe(reviewCard);
    });

    const reviewChannel = reviewPageRoot.dataset.reviewChannel;
    if (reviewChannel && window.Echo) {
        window.Echo.private(reviewChannel).listen('.review.created', () => {
            updateUnreadBadge(unreadCount + 1);
        });
    }
}
