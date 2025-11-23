<!-- Edit Booking Modal -->
<div id="modalEditBooking" class="custom-modal" role="dialog" aria-modal="true" aria-labelledby="modalEditBookingLabel" hidden>
  <div class="custom-modal-overlay" data-modal-close></div>
  <div class="custom-modal-content" tabindex="-1">
    <header class="custom-modal-header">
      <h2 id="modalEditBookingLabel">Edit Booking Time</h2>
      <button type="button" class="custom-modal-close-btn" aria-label="Close modal" data-modal-close>&times;</button>
    </header>
    <div class="custom-modal-body">
      <form id="editBookingForm" method="POST" action="update_booking.php">
        <input type="hidden" name="bookingId" id="editBookingId" value="">
        <div class="mb-3">
          <label for="editBookingDate" class="form-label">Date</label>
          <input type="date" class="form-control" name="date" id="editBookingDate" required>
        </div>
        <div class="mb-3">
          <label for="editBookingTime" class="form-label">Time</label>
          <input type="time" class="form-control" name="time" id="editBookingTime" required>
        </div>
        <div class="mb-3">
          <label for="editBookingDuration" class="form-label">Duration (minutes)</label>
          <input type="number" min="1" class="form-control" name="duration" id="editBookingDuration" required>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-secondary" data-modal-close>Cancel</button>
          <button type="submit" class="btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
