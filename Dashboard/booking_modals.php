<!-- Cancel Booking Modal -->
<div id="modalCancelBooking" class="custom-modal" role="dialog" aria-modal="true" aria-labelledby="modalCancelBookingLabel" hidden>
  <div class="custom-modal-overlay" data-modal-close></div>
  <div class="custom-modal-content" tabindex="-1">
    <header class="custom-modal-header">
      <h2 id="modalCancelBookingLabel">Konfirmasi Cancel Booking</h2>
      <button type="button" class="custom-modal-close-btn" aria-label="Close modal" data-modal-close>&times;</button>
    </header>
    <div class="custom-modal-body">
      <p>Apakah Anda yakin ingin membatalkan booking dengan ID <strong id="cancelBookingIdText"></strong>?</p>
      <form id="cancelBookingForm" method="POST" action="cancel_booking.php">
        <input type="hidden" name="bookingId" id="cancelBookingIdInput" value="">
        <div class="modal-actions">
          <button type="button" class="btn-secondary" data-modal-close>Batal</button>
          <button type="submit" class="btn-warning">Cancel Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Booking Modal -->
<div id="modalDeleteBooking" class="custom-modal" role="dialog" aria-modal="true" aria-labelledby="modalDeleteBookingLabel" hidden>
  <div class="custom-modal-overlay" data-modal-close></div>
  <div class="custom-modal-content" tabindex="-1">
    <header class="custom-modal-header">
      <h2 id="modalDeleteBookingLabel">Konfirmasi Delete Booking</h2>
      <button type="button" class="custom-modal-close-btn" aria-label="Close modal" data-modal-close>&times;</button>
    </header>
    <div class="custom-modal-body">
      <p>Apakah Anda yakin ingin menghapus booking dengan ID <strong id="deleteBookingIdText"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
      <form id="deleteBookingForm" method="POST" action="delete_booking.php">
        <input type="hidden" name="bookingId" id="deleteBookingIdInput" value="">
        <div class="modal-actions">
          <button type="button" class="btn-secondary" data-modal-close>Batal</button>
          <button type="submit" class="btn-danger">Delete Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>
