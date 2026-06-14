<!-- Ubah Password View -->
<div class="card animate__animated animate__fadeIn" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3><i class="bx bx-key"></i> Ubah Password Administrator</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= $admin_url ?>?page=ubah_password" onsubmit="return validatePasswordForm(event)">
            <div class="form-group">
                <label class="form-label" for="current_password">Password Saat Ini</label>
                <div style="position: relative;">
                    <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Masukkan password saat ini" required style="padding-right: 45px;">
                    <button type="button" onclick="togglePasswordInput('current_password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--gray-400); cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 4px;">
                        <i class="bx bx-show" style="font-size: 1.25rem;"></i>
                    </button>
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label class="form-label" for="new_password">Password Baru</label>
                <div style="position: relative;">
                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Masukkan password baru (min. 6 karakter)" required style="padding-right: 45px;">
                    <button type="button" onclick="togglePasswordInput('new_password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--gray-400); cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 4px;">
                        <i class="bx bx-show" style="font-size: 1.25rem;"></i>
                    </button>
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label class="form-label" for="confirm_password">Konfirmasi Password Baru</label>
                <div style="position: relative;">
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Ulangi password baru" required style="padding-right: 45px;">
                    <button type="button" onclick="togglePasswordInput('confirm_password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--gray-400); cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 4px;">
                        <i class="bx bx-show" style="font-size: 1.25rem;"></i>
                    </button>
                </div>
            </div>

            <div class="btn-group" style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 10px;">
                <a href="<?= $admin_url ?>?page=dashboard" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePasswordInput(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bx bx-hide';
    } else {
        input.type = 'password';
        icon.className = 'bx bx-show';
    }
}

function validatePasswordForm(event) {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;

    if (newPass.length < 6) {
        Swal.fire({
            title: 'Kesalahan!',
            text: 'Password baru minimal harus 6 karakter.',
            icon: 'error',
            confirmButtonColor: '#10b981',
            background: '#1e293b',
            color: '#fff'
        });
        return false;
    }

    if (newPass !== confirmPass) {
        Swal.fire({
            title: 'Kesalahan!',
            text: 'Konfirmasi password baru tidak cocok.',
            icon: 'error',
            confirmButtonColor: '#10b981',
            background: '#1e293b',
            color: '#fff'
        });
        return false;
    }

    return true;
}
</script>

