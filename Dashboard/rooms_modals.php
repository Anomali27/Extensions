<div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="roomModalLabel">Room Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <h6>Room Information</h6>
          <p><strong>Name:</strong> <span id="modalRoomName"></span></p>
          <p><strong>Type:</strong> <span id="modalRoomType"></span></p>
          <p><strong>Status:</strong> <span id="modalRoomStatus" class="badge"></span></p>
        </div>

        <div class="mb-3">
          <h6>Today's Bookings</h6>
          <div id="modalBookings">
            <p class="text-muted">Loading...</p>
          </div>
        </div>

        <div class="mb-3">
          <h6>Available Time Slots (Today)</h6>
          <div id="modalSlots">
            <p class="text-muted">Loading...</p>
          </div>
        </div>

        <div class="mb-3">
          <h6>Actions</h6>
          <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-success" id="btnSetAvailable">Set Available</button>
            <button class="btn btn-danger" id="btnSetBooked">Set Booked</button>
            <button class="btn btn-info" id="btnViewHistory">View Booking History</button>
            <button class="btn btn-primary" id="btnEditRoom">Edit Room</button>
            <button class="btn btn-danger" id="btnDeleteRoom">Delete Room</button>
          </div>
        </div>

        <div id="editRoomForm" style="display: none;">
          <h6>Edit Room</h6>
          <form id="formEditRoom">
            <input type="hidden" name="id" id="editRoomId"> 
            <div class="mb-3">
              <label for="editRoomName" class="form-label">Name</label>
              <input type="text" name="name" class="form-control" id="editRoomName" required> 
            </div>
            <div class="mb-3">
              <label for="editRoomType" class="form-label">Type</label>
              <input type="text" name="type" class="form-control" id="editRoomType" required> 
            </div>
            <button type="submit" class="btn btn-success">Save Changes</button>
            <button type="button" class="btn btn-secondary" id="cancelEdit">Cancel</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="roomHistoryModal" tabindex="-1" aria-labelledby="roomHistoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="roomHistoryModalLabel">Booking History</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>User</th>
              <th>Date</th>
              <th>Time</th>
              <th>Duration</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="historyTableBody">
            <tr>
              <td colspan="5" class="text-center">Loading...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>