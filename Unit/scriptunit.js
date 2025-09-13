
document.addEventListener('DOMContentLoaded', ()=> {
  const seats = document.querySelectorAll('.seat');
  const modal = document.getElementById('modal');
  const modalSeat = document.getElementById('modal-seat');
  const modalStatus = document.getElementById('modal-status');
  const btnReserve = document.getElementById('btn-reserve');
  const btnCancel = document.getElementById('btn-cancel');
  const closeBtn = document.querySelector('.modal .close');
  let currentSeat = null;

  seats.forEach(s => {
    s.addEventListener('click', () => {
      currentSeat = s;
      const name = s.dataset.seat || s.textContent.trim();
      const isAvailable = s.classList.contains('available');
      modalSeat.textContent = name;
      modalStatus.textContent = isAvailable ? 'Available' : 'Booked';
      btnReserve.disabled = !isAvailable;
      btnReserve.textContent = isAvailable ? 'Reserve' : 'Already Booked';
      modal.setAttribute('aria-hidden','false');
    });
  });

  function closeModal(){
    modal.setAttribute('aria-hidden','true');
    currentSeat = null;
  }

  closeBtn.addEventListener('click', closeModal);
  btnCancel.addEventListener('click', closeModal);

  btnReserve.addEventListener('click', ()=> {
    if(!currentSeat) return;
    // Untuk demo: ubah status seat menjadi booked
    currentSeat.classList.remove('available');
    currentSeat.classList.add('booked');
    alert(currentSeat.dataset.seat + ' berhasil dipesan!');
    closeModal();
  });

  // klik luar modal untuk tutup
  modal.addEventListener('click', (e) => {
    if(e.target === modal) closeModal();
  });

  // keyboard escape untuk tutup
  document.addEventListener('keydown', (e) => {
    if(e.key === 'Escape') closeModal();
  });
});
