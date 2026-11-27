import 'bootstrap';

const formatRupiahDigits = (value) => {
    const digits = String(value ?? '').replace(/\D/g, '');
    if (!digits) return '0';
    return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

document.addEventListener('DOMContentLoaded', () => {
    // Nominal input: tampilkan pemisah ribuan, kirim angka murni ke Laravel.
    document.querySelectorAll('.rupiah-format').forEach((displayInput) => {
        const hiddenInput = document.getElementById('amount');
        const syncAmount = () => {
            const digits = displayInput.value.replace(/\D/g, '');
            displayInput.value = formatRupiahDigits(digits);
            if (hiddenInput) hiddenInput.value = digits || '0';
        };
        syncAmount();
        displayInput.addEventListener('input', syncAmount);
    });

    // Detail pendonor: tombol mata membuka/menutup data yang disamarkan.
    document.addEventListener('click', (event) => {
        const revealButton = event.target.closest('.reveal-sensitive');
        if (!revealButton) return;
        const valueElement = revealButton.previousElementSibling;
        if (!valueElement || !valueElement.classList.contains('sensitive-value')) return;

        const isRevealed = revealButton.dataset.revealed === 'true';
        if (isRevealed) {
            valueElement.textContent = valueElement.dataset.masked;
            revealButton.dataset.revealed = 'false';
            revealButton.setAttribute('aria-label', 'Tampilkan data');
        } else {
            valueElement.textContent = valueElement.dataset.full;
            revealButton.dataset.revealed = 'true';
            revealButton.setAttribute('aria-label', 'Sembunyikan data');
        }
    });

    document.querySelectorAll('.registration-row').forEach((row) => {
        row.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                const target = row.dataset.bsTarget;
                if (target && window.bootstrap) {
                    window.bootstrap.Modal.getOrCreateInstance(document.querySelector(target)).show();
                }
            }
        });
    });

    // Metode pembayaran dan timer kode dummy.
    const vaPanel = document.getElementById('virtualAccountPanel');
    const qrisPanel = document.getElementById('qrisPanel');
    document.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
        radio.addEventListener('change', () => {
            if (!vaPanel || !qrisPanel) return;
            vaPanel.classList.toggle('d-none', radio.value !== 'virtual_account' || !radio.checked);
            qrisPanel.classList.toggle('d-none', radio.value !== 'qris' || !radio.checked);
        });
    });

    const copyButton = document.getElementById('copyVirtualAccount');
    if (copyButton) {
        copyButton.addEventListener('click', async () => {
            const number = document.getElementById('virtualAccountNumber')?.textContent?.trim();
            if (number && navigator.clipboard) {
                await navigator.clipboard.writeText(number);
                copyButton.textContent = 'Tersalin';
            }
        });
    }

    const countdown = document.querySelector('.payment-countdown');
    if (countdown) {
        const expiry = Number(countdown.dataset.expiry);
        const submitButton = document.querySelector('#paymentUploadForm button[type="submit"]');
        const updateCountdown = () => {
            const remaining = Math.max(0, Math.floor((expiry - Date.now()) / 1000));
            const minutes = String(Math.floor(remaining / 60)).padStart(2, '0');
            const seconds = String(remaining % 60).padStart(2, '0');
            countdown.textContent = `${minutes}:${seconds}`;
            if (remaining <= 0) {
                countdown.classList.add('text-danger');
                countdown.textContent = 'Kedaluwarsa';
                if (submitButton) submitButton.disabled = true;
            }
        };
        updateCountdown();
        window.setInterval(updateCountdown, 1000);
    }

    // Optimasi gambar bukti: resize maksimal 1400px dan encode WebP.
    const proofInput = document.getElementById('payment_proof');
    const preview = document.getElementById('paymentProofPreview');
    if (proofInput) {
        proofInput.addEventListener('change', () => {
            const file = proofInput.files?.[0];
            if (preview) {
                preview.textContent = file
                    ? `File dipilih: ${file.name} (${Math.round(file.size / 1024)} KB). Foto akan dioptimalkan sebelum dikirim.`
                    : '';
            }
        });

        const uploadForm = document.getElementById('paymentUploadForm');
        if (uploadForm) {
            uploadForm.addEventListener('submit', async (event) => {
                if (uploadForm.dataset.imageCompressed === 'true') return;

                const file = proofInput.files?.[0];
                if (!file || !file.type.startsWith('image/')) return;

                event.preventDefault();
                const submit = uploadForm.querySelector('button[type="submit"]');
                const originalLabel = submit?.textContent;
                if (submit) {
                    submit.disabled = true;
                    submit.textContent = 'Mengoptimalkan gambar...';
                }

                let finalFile = file;
                try {
                    if (typeof createImageBitmap === 'function') {
                        const bitmap = await createImageBitmap(file);
                        const maxDimension = 1400;
                        const scale = Math.min(1, maxDimension / Math.max(bitmap.width, bitmap.height));
                        const canvas = document.createElement('canvas');
                        canvas.width = Math.max(1, Math.round(bitmap.width * scale));
                        canvas.height = Math.max(1, Math.round(bitmap.height * scale));
                        const context = canvas.getContext('2d');
                        context.drawImage(bitmap, 0, 0, canvas.width, canvas.height);
                        bitmap.close();

                        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/webp', 0.78));
                        if (blob && blob.type === 'image/webp' && blob.size < file.size) {
                            finalFile = new File([blob], file.name.replace(/\.[^.]+$/, '') + '.webp', {
                                type: 'image/webp',
                                lastModified: Date.now()
                            });
                        }
                    }
                } catch (error) {
                    // Jika WebP tidak didukung, gunakan gambar asli jika masih sesuai batas.
                }

                if (finalFile.size > 2 * 1024 * 1024) {
                    if (preview) {
                        preview.textContent = 'Gambar masih lebih dari 2 MB setelah optimasi. Pilih gambar yang lebih kecil.';
                        preview.classList.add('text-danger');
                    }
                    if (submit) {
                        submit.disabled = false;
                        submit.textContent = originalLabel;
                    }
                    return;
                }

                if (finalFile !== file) {
                    const transfer = new DataTransfer();
                    transfer.items.add(finalFile);
                    proofInput.files = transfer.files;
                    if (preview) {
                        preview.textContent = `Berhasil dioptimalkan menjadi WebP (${Math.round(finalFile.size / 1024)} KB).`;
                        preview.classList.remove('text-danger');
                        preview.classList.add('text-success');
                    }
                }

                uploadForm.dataset.imageCompressed = 'true';
                if (submit) {
                    submit.disabled = false;
                    submit.textContent = originalLabel;
                }
                uploadForm.requestSubmit();
            });
        }
    }
});
