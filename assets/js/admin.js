/**
 * SIPUPUK Admin JS
 */

// Toggle Sidebar
function toggleSidebar() {
    document.body.classList.toggle('sidebar-toggled');
}

// Close modals on overlay click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
    }
});

// Close modals on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
    }
});

// Auto-hide alerts after 5s
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
});

// Universal SweetAlert2 confirmation for deletions
document.addEventListener('DOMContentLoaded', function() {
    // Intercept any click on delete links or elements containing confirmation triggers
    document.body.addEventListener('click', function(e) {
        const target = e.target.closest('a');
        const href = target ? target.getAttribute('href') : null;
        
        // If it's a link containing delete action or class indicating deletion
        if (target && href && (href.includes('action=delete') || href.includes('action=delete_masuk') || href.includes('action=delete_keluar') || target.classList.contains('btn-danger'))) {
            // Prevent the inline 'onclick="return confirm()"' from firing if present
            if (target.getAttribute('onclick')) {
                target.removeAttribute('onclick');
            }
            
            e.preventDefault();
            const deleteUrl = target.href;
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: '#1e293b',
                color: '#fff',
                backdrop: `rgba(15,23,42,0.5)`
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = deleteUrl;
                }
            });
        }
    });
});
