<div class="modal fade" id="modalEditInventory" tabindex="-1" aria-labelledby="modalEditInventoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditInventoryLabel">Edit Inventory Quantity</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editInventoryForm" method="POST" action="edit_inventory.php">
          <input type="hidden" name="id" id="editInventoryId">
          <div class="mb-3">
            <label for="editInventoryType" class="form-label">Type</label>
            <input type="text" name="type" id="editInventoryType" class="form-control" placeholder="Type" readonly>
          </div>
          <div class="mb-3">
            <label for="editInventoryQuantity" class="form-label">Quantity Available</label>
            <input type="number" name="quantity_available" id="editInventoryQuantity" class="form-control" placeholder="Quantity Available" required min="0">
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeEditInventoryModal">Batal</button>
            <button type="submit" class="btn btn-primary">Update Quantity</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>