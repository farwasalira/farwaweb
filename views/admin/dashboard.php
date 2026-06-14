<!-- Dashboard View -->
<div class="welcome-banner animate__animated animate__fadeInDown">
    <div class="welcome-content">
        <h2 class="welcome-title">Halo, Admin! <span class="wave">👋</span></h2>
        <p class="welcome-subtitle">Selamat Datang di Sistem Informasi Stok dan Distribusi Pupuk Desa Lumaring.</p>
    </div>
    <div class="welcome-decoration">
        <i class="bx bxs-leaf"></i>
    </div>
</div>

<style>
.welcome-banner {
    position: relative;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 16px;
    padding: 30px 40px;
    margin-bottom: 28px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    overflow: hidden;
    box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
}

.welcome-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -5%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.welcome-content {
    position: relative;
    z-index: 2;
}

.welcome-title {
    font-size: 1.8rem;
    font-weight: 800;
    margin-bottom: 8px;
    letter-spacing: -0.5px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.welcome-subtitle {
    font-size: 1.05rem;
    margin: 0;
    opacity: 0.9;
    font-weight: 500;
    max-width: 600px;
    line-height: 1.5;
}

.welcome-decoration {
    position: relative;
    z-index: 1;
    font-size: 6rem;
    color: rgba(255, 255, 255, 0.15);
    transform: rotate(-15deg);
    transition: transform 0.3s ease;
}

.welcome-banner:hover .welcome-decoration {
    transform: rotate(0deg) scale(1.1);
}

.wave {
    display: inline-block;
    animation: wave-animation 2.5s infinite;
    transform-origin: 70% 70%;
}

@keyframes wave-animation {
    0% { transform: rotate( 0.0deg) }
    10% { transform: rotate(14.0deg) }
    20% { transform: rotate(-8.0deg) }
    30% { transform: rotate(14.0deg) }
    40% { transform: rotate(-4.0deg) }
    50% { transform: rotate(10.0deg) }
    60% { transform: rotate( 0.0deg) }
    100% { transform: rotate( 0.0deg) }
}

@media (max-width: 768px) {
    .welcome-banner {
        padding: 24px;
        flex-direction: column;
        text-align: center;
    }
    .welcome-decoration {
        display: none;
    }
}
</style>

<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-label">Jenis Pupuk</div>
        <div class="stat-value"><?= $total_pupuk ?></div>
        <div class="stat-icon"><i class="bx bxs-package"></i></div>
    </div>
    <div class="stat-card green">
        <div class="stat-label">Kelompok Tani</div>
        <div class="stat-value"><?= $total_kelompok ?></div>
        <div class="stat-icon"><i class="bx bxs-group"></i></div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-label">Total Stok (sak)</div>
        <div class="stat-value"><?= number_format($total_stok, 0, ',', '.') ?></div>
        <div class="stat-icon"><i class="bx bxs-box"></i></div>
    </div>
    <div class="stat-card red">
        <div class="stat-label">Stok Menipis</div>
        <div class="stat-value"><?= $total_menipis ?></div>
        <div class="stat-icon"><i class="bx bxs-error"></i></div>
    </div>
</div>

<!-- Chart Card (Full Width) -->
<div class="card animate__animated animate__fadeIn" style="margin-bottom: 28px;">
    <div class="card-header">
        <h3><i class="bx bxs-bar-chart-alt-2"></i> Ketersediaan Stok Pupuk</h3>
    </div>
    <div class="card-body">
        <div class="chart-container" style="height: 380px;">
            <canvas id="stokChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('stokChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chart_labels) ?>,
                datasets: [{
                    label: 'Stok (sak)',
                    data: <?= json_encode($chart_data) ?>,
                    backgroundColor: ['#3b82f6','#22c55e','#f59e0b','#ef4444','#8b5cf6'],
                    borderRadius: 8,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
});
</script>
