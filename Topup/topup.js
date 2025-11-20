const amountButtons = document.querySelectorAll('.amount-btn');
const customAmountInput = document.getElementById('customAmount');
const topupForm = document.getElementById('topupForm');

let selectedAmount = 0;

// Klik tombol amount
amountButtons.forEach(button => {
    button.addEventListener('click', function () {

        // Hilangkan active dari semua tombol
        amountButtons.forEach(btn => btn.classList.remove('active'));

        // Tambahkan active ke tombol yang dipilih
        this.classList.add('active');

        // Set nilai
        selectedAmount = parseInt(this.getAttribute('data-amount'));

        // Kosongkan custom input
        customAmountInput.value = '';
    });
});

// Custom input amount
customAmountInput.addEventListener('input', function () {
    // Hapus active dari semua tombol
    amountButtons.forEach(btn => btn.classList.remove('active'));

    let value = parseInt(this.value) || 0;

    // Maksimal 500.000
    if (value > 500000) {
        value = 500000;
        this.value = value;
    }

    // Minimal 5.000 → namun hanya jika user mulai mengetik
    if (value < 5000) {
        selectedAmount = 0;
        return;
    }

    // Pembulatan otomatis ke kelipatan 5000
    if (value % 5000 !== 0) {
        value = Math.floor(value / 5000) * 5000;
        this.value = value;
    }

    selectedAmount = value;
});

// Submit form
topupForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const selectedMethod = document.querySelector('input[name="method"]:checked');

    if (!selectedMethod) {
        Swal.fire('Error', 'Please select a payment method.', 'error');
        return;
    }

    if (selectedAmount < 5000 || selectedAmount % 5000 !== 0) {
        Swal.fire('Error', 'Amount minimal 5000 dan harus kelipatan 5000.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('amount', selectedAmount);
    formData.append('method', selectedMethod.value);

    Swal.fire({
        title: 'Processing...',
        text: 'Please wait...',
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch('process_topup.php', {
        method: 'POST',
        body: formData
    })
        .then(r => r.json())
        .then(data => {
            Swal.close();

            if (data.success) {
                Swal.fire('Success', data.message, 'success')
                    .then(() => {

                        // REDIRECT LANGSUNG KE LANDING PAGE ID UNIT
                        window.location.href = "../index.php#unit";

                    });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(err => {
            Swal.close();
            Swal.fire('Error', 'Something went wrong.', 'error');
            console.error(err);
        });
});
