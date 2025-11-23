<!-- User CRUD Modals -->

<!-- Add User Modal -->
<div id="modalAddUser" class="custom-modal" role="dialog" aria-modal="true" aria-labelledby="modalAddUserLabel" hidden>
  <div class="custom-modal-overlay"></div>
  <div class="custom-modal-content" tabindex="-1">
    <header class="custom-modal-header">
      <h2 id="modalAddUserLabel">Tambah User</h2>
      <button type="button" class="custom-modal-close-btn" aria-label="Close modal" data-modal-close>&times;</button>
    </header>
    <div class="custom-modal-body">
      <div id="modalAddUserAlert" class="modal-alert" role="alert" aria-live="assertive" hidden></div>

      <form id="addUserForm" novalidate>
        <div class="form-group">
          <label for="addUsername">Username</label>
          <input type="text" name="username" id="addUsername" required aria-required="true" placeholder="Username">
          <span class="input-error" aria-live="polite"></span>
        </div>
        <div class="form-group">
          <label for="addEmail">Email</label>
          <input type="email" name="email" id="addEmail" required aria-required="true" placeholder="Email">
          <span class="input-error" aria-live="polite"></span>
        </div>
        <div class="form-group">
          <label for="addPassword">Password</label>
          <input type="password" name="password" id="addPassword" required aria-required="true" placeholder="Password">
          <span class="input-error" aria-live="polite"></span>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-secondary" data-modal-close>Batal</button>
          <button type="submit" class="btn-primary">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit User Modal (Custom Modal Implementation) -->
<div id="modalEditUser" class="custom-modal" role="dialog" aria-modal="true" aria-labelledby="modalEditUserLabel" hidden>
  <div class="custom-modal-overlay" data-modal-close></div>
  <div class="custom-modal-content" tabindex="-1">
    <header class="custom-modal-header">
      <h2 id="modalEditUserLabel">Edit User</h2>
      <button type="button" class="custom-modal-close-btn" aria-label="Close modal" data-modal-close>&times;</button>
    </header>
    <div class="custom-modal-body">
      <div id="modalEditUserAlert" class="modal-alert" role="alert" aria-live="assertive" hidden></div>

      <form id="editUserForm" method="POST" action="edit_user.php" novalidate>
        <input type="hidden" name="id" id="editId">
        <div class="form-group">
          <label for="editUsername">Username</label>
          <input type="text" name="username" id="editUsername" required aria-required="true" placeholder="Username">
          <span class="input-error" aria-live="polite"></span>
        </div>
        <div class="form-group">
          <label for="editEmail">Email</label>
          <input type="email" name="email" id="editEmail" required aria-required="true" placeholder="Email">
          <span class="input-error" aria-live="polite"></span>
        </div>
        <div class="form-group">
          <label for="editPassword">Password Baru (opsional)</label>
          <input type="password" name="password" id="editPassword" placeholder="Isi jika ingin mengganti">
          <span class="input-error" aria-live="polite"></span>
        </div>
        <div class="form-group">
          <label for="editStatus">Status</label>
          <select name="status" id="editStatus" required aria-required="true">
            <option value="online">Online</option>
            <option value="offline">Offline</option>
          </select>
          <span class="input-error" aria-live="polite"></span>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-secondary" data-modal-close>Batal</button>
          <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete User Modal (Custom Modal Implementation) -->
<div id="modalDeleteUser" class="custom-modal" role="dialog" aria-modal="true" aria-labelledby="modalDeleteUserLabel" hidden>
  <div class="custom-modal-overlay" data-modal-close></div>
  <div class="custom-modal-content" tabindex="-1">
    <header class="custom-modal-header">
      <h2 id="modalDeleteUserLabel">Konfirmasi Hapus User</h2>
      <button type="button" class="custom-modal-close-btn" aria-label="Close modal" data-modal-close>&times;</button>
    </header>
    <div class="custom-modal-body">
      <div id="modalDeleteUserAlert" class="modal-alert" role="alert" aria-live="assertive" hidden></div>

      <p>Apakah Anda yakin ingin menghapus user <strong id="deleteUsername"></strong>?</p>

      <div class="modal-actions">
        <button type="button" class="btn-secondary" data-modal-close>Batal</button>
        <button type="button" class="btn-danger" id="confirmDeleteUserBtn">Hapus</button>
      </div>
    </div>
  </div>
</div>
