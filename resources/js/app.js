
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

/**
 * Format angka dengan pemisah ribuan titik.
 *
 * Contoh:
 * 150000 -> 150.000
 */
function formatRupiahDigits(value) {
    const digits = String(value ?? '').replace(/\D/g, '');

    if (!digits) {
        return '0';
    }

    return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/**
 * Jalankan setelah halaman selesai dimuat.
 */
document.addEventListener('DOMContentLoaded', () => {
    const proofLightbox = document.getElementById('proofLightbox');
    const proofLightboxImage = document.getElementById('proofLightboxImage');
    let activeProofTrigger = null;

    function closeProofLightbox() {
        if (!proofLightbox || !proofLightboxImage) {
            return;
        }

        proofLightbox.hidden = true;
        proofLightboxImage.removeAttribute('src');
        activeProofTrigger?.focus();
        activeProofTrigger = null;
    }

    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) {
            return;
        }

        const previewTrigger = event.target.closest('.proof-preview-trigger');

        if (previewTrigger && proofLightbox && proofLightboxImage) {
            activeProofTrigger = previewTrigger;
            proofLightboxImage.src = previewTrigger.dataset.proofSrc;
            proofLightbox.hidden = false;
            proofLightbox.querySelector('[data-proof-close]')?.focus();

            return;
        }

        if (
            event.target === proofLightbox ||
            event.target.closest('[data-proof-close]')
        ) {
            closeProofLightbox();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && proofLightbox && !proofLightbox.hidden) {
            closeProofLightbox();
        }
    });

    const sectionNav = document.querySelector('[data-section-nav]');

    if (sectionNav) {
        const sectionLinks = [...sectionNav.querySelectorAll('[data-section-link]')];
        const sections = sectionLinks
            .map((link) => document.getElementById(link.dataset.sectionLink))
            .filter(Boolean);
        const indicator = sectionNav.querySelector('.nav-indicator');

        function updateIndicator() {
            const activeLink = sectionNav.querySelector('.nav-link.active');

            if (!activeLink || !indicator) {
                return;
            }

            if (window.matchMedia('(max-width: 991.98px)').matches) {
                indicator.style.width = '3px';
                indicator.style.height = `${activeLink.offsetHeight}px`;
                indicator.style.transform = `translateY(${activeLink.offsetTop}px)`;
            } else {
                indicator.style.width = `${activeLink.offsetWidth}px`;
                indicator.style.height = '3px';
                indicator.style.transform = `translateX(${activeLink.offsetLeft}px)`;
            }
        }

        function setActiveSection(sectionId) {
            sectionLinks.forEach((link) => {
                const isActive = link.dataset.sectionLink === sectionId;

                link.classList.toggle('active', isActive);

                if (isActive) {
                    link.setAttribute('aria-current', 'location');
                } else {
                    link.removeAttribute('aria-current');
                }
            });

            updateIndicator();
        }

        function updateActiveSection() {
            const navbar = document.querySelector('.public-navbar');
            const activationLine = (navbar?.offsetHeight ?? 0) + 30;
            let currentSection = sections[0];

            sections.forEach((section) => {
                if (section.getBoundingClientRect().top <= activationLine) {
                    currentSection = section;
                }
            });

            if (currentSection) {
                setActiveSection(currentSection.id);
            }
        }

        sectionLinks.forEach((link) => {
            link.addEventListener('click', () => {
                setActiveSection(link.dataset.sectionLink);

                const collapse = document.getElementById('publicNavbar');

                if (collapse?.classList.contains('show') && window.innerWidth < 992) {
                    bootstrap.Collapse.getOrCreateInstance(collapse).hide();
                }
            });
        });

        let scrollFrame = null;

        window.addEventListener('scroll', () => {
            if (scrollFrame !== null) {
                return;
            }

            scrollFrame = window.requestAnimationFrame(() => {
                updateActiveSection();
                scrollFrame = null;
            });
        }, { passive: true });

        window.addEventListener('resize', updateIndicator);
        document.getElementById('publicNavbar')?.addEventListener('shown.bs.collapse', updateIndicator);

        const initialSection = window.location.hash.slice(1);

        if (sectionLinks.some((link) => link.dataset.sectionLink === initialSection)) {
            setActiveSection(initialSection);
        }

        updateActiveSection();
        window.requestAnimationFrame(updateIndicator);
    }

    /*
    |--------------------------------------------------------------------------
    | Format nominal jadwal
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.rupiah-format').forEach((input) => {
        const hiddenInput = document.getElementById('amount');

        function syncAmount() {
            const digits = input.value.replace(/\D/g, '');

            input.value = formatRupiahDigits(digits);

            if (hiddenInput) {
                hiddenInput.value = digits || '0';
            }
        }

        syncAmount();

        input.addEventListener('input', syncAmount);
    });

    /*
    |--------------------------------------------------------------------------
    | Tampilkan atau sembunyikan data sensitif
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', (event) => {
        const button = event.target.closest('.reveal-sensitive');

        if (!button) {
            return;
        }

        const valueElement = button.previousElementSibling;

        if (
            !valueElement ||
            !valueElement.classList.contains('sensitive-value')
        ) {
            return;
        }

        const revealed = button.dataset.revealed === 'true';

        if (revealed) {
            valueElement.textContent = valueElement.dataset.masked;

            button.dataset.revealed = 'false';
            button.setAttribute('aria-label', 'Tampilkan data');
        } else {
            valueElement.textContent = valueElement.dataset.full;

            button.dataset.revealed = 'true';
            button.setAttribute('aria-label', 'Sembunyikan data');
        }
    });

    /*
    |--------------------------------------------------------------------------
    | Buka modal detail menggunakan keyboard
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.registration-row').forEach((row) => {
        row.addEventListener('keydown', (event) => {
            if (
                event.key !== 'Enter' &&
                event.key !== ' '
            ) {
                return;
            }

            event.preventDefault();

            const target = row.dataset.bsTarget;

            if (!target) {
                return;
            }

            const modalElement = document.querySelector(target);

            if (!modalElement) {
                return;
            }

            bootstrap.Modal
                .getOrCreateInstance(modalElement)
                .show();
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Pilihan metode pembayaran
    |--------------------------------------------------------------------------
    */

    const vaPanel = document.getElementById('virtualAccountPanel');
    const qrisPanel = document.getElementById('qrisPanel');

    document.querySelectorAll(
        'input[name="payment_method"]'
    ).forEach((radio) => {
        radio.addEventListener('change', () => {
            if (!vaPanel || !qrisPanel) {
                return;
            }

            const isVirtualAccount =
                radio.value === 'virtual_account' &&
                radio.checked;

            const isQris =
                radio.value === 'qris' &&
                radio.checked;

            vaPanel.classList.toggle(
                'd-none',
                !isVirtualAccount
            );

            qrisPanel.classList.toggle(
                'd-none',
                !isQris
            );
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Salin nomor Virtual Account
    |--------------------------------------------------------------------------
    */

    const copyButton = document.getElementById(
        'copyVirtualAccount'
    );

    if (copyButton) {
        copyButton.addEventListener('click', async () => {
            const number = document
                .getElementById('virtualAccountNumber')
                ?.textContent
                ?.trim();

            if (!number || !navigator.clipboard) {
                return;
            }

            try {
                await navigator.clipboard.writeText(number);

                copyButton.textContent = 'Tersalin';
            } catch (error) {
                copyButton.textContent = 'Gagal menyalin';
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Timer masa berlaku pembayaran
    |--------------------------------------------------------------------------
    */

    const countdown = document.querySelector(
        '.payment-countdown'
    );

    if (countdown) {
        const expiry = Number(countdown.dataset.expiry);

        const submitButton = document.querySelector(
            '#paymentUploadForm button[type="submit"]'
        );

        function updateCountdown() {
            const remaining = Math.max(
                0,
                Math.floor((expiry - Date.now()) / 1000)
            );

            const minutes = String(
                Math.floor(remaining / 60)
            ).padStart(2, '0');

            const seconds = String(
                remaining % 60
            ).padStart(2, '0');

            countdown.textContent = `${minutes}:${seconds}`;

            if (remaining <= 0) {
                countdown.classList.add('text-danger');
                countdown.textContent = 'Kedaluwarsa';

                if (submitButton) {
                    submitButton.disabled = true;
                }
            }
        }

        updateCountdown();

        window.setInterval(updateCountdown, 1000);
    }

    /*
    |--------------------------------------------------------------------------
    | Optimasi gambar bukti pembayaran
    |--------------------------------------------------------------------------
    |
    | - Resize maksimal 1400 px
    | - Encode WebP jika didukung
    | - Kualitas 78%
    | - Maksimal file hasil 2 MB
    |--------------------------------------------------------------------------
    */

    const proofInput = document.getElementById(
        'payment_proof'
    );

    const preview = document.getElementById(
        'paymentProofPreview'
    );

    if (!proofInput) {
        return;
    }

    proofInput.addEventListener('change', () => {
        const file = proofInput.files?.[0];

        if (!preview) {
            return;
        }

        preview.textContent = file
            ? `File dipilih: ${file.name} (${Math.round(file.size / 1024)} KB). Gambar akan dioptimalkan sebelum dikirim.`
            : '';
    });

    const uploadForm = document.getElementById(
        'paymentUploadForm'
    );

    if (!uploadForm) {
        return;
    }

    uploadForm.addEventListener('submit', async (event) => {
        if (
            uploadForm.dataset.imageCompressed === 'true'
        ) {
            return;
        }

        const file = proofInput.files?.[0];

        if (!file || !file.type.startsWith('image/')) {
            return;
        }

        event.preventDefault();

        const submitButton = uploadForm.querySelector(
            'button[type="submit"]'
        );

        const originalLabel = submitButton?.textContent;

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Mengoptimalkan gambar...';
        }

        let finalFile = file;

        try {
            if (typeof createImageBitmap === 'function') {
                const bitmap = await createImageBitmap(file);

                const maxDimension = 1400;

                const scale = Math.min(
                    1,
                    maxDimension / Math.max(
                        bitmap.width,
                        bitmap.height
                    )
                );

                const canvas = document.createElement('canvas');

                canvas.width = Math.max(
                    1,
                    Math.round(bitmap.width * scale)
                );

                canvas.height = Math.max(
                    1,
                    Math.round(bitmap.height * scale)
                );

                const context = canvas.getContext('2d');

                if (!context) {
                    bitmap.close();
                    throw new Error('Canvas tidak tersedia.');
                }

                context.drawImage(
                    bitmap,
                    0,
                    0,
                    canvas.width,
                    canvas.height
                );

                bitmap.close();

                const blob = await new Promise((resolve) => {
                    canvas.toBlob(
                        resolve,
                        'image/webp',
                        0.78
                    );
                });

                if (
                    blob &&
                    blob.type === 'image/webp' &&
                    blob.size < file.size
                ) {
                    finalFile = new File(
                        [blob],
                        file.name.replace(/\.[^.]+$/, '') + '.webp',
                        {
                            type: 'image/webp',
                            lastModified: Date.now(),
                        }
                    );
                }
            }
        } catch (error) {
            /*
             * Jika WebP tidak didukung, gunakan gambar asli.
             * Server tetap akan memvalidasi format dan ukuran.
             */
        }

        if (finalFile.size > 2 * 1024 * 1024) {
            if (preview) {
                preview.textContent =
                    'Gambar masih lebih dari 2 MB setelah optimasi. Pilih gambar yang lebih kecil.';

                preview.classList.add('text-danger');
            }

            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = originalLabel;
            }

            return;
        }

        if (finalFile !== file) {
            const transfer = new DataTransfer();

            transfer.items.add(finalFile);

            proofInput.files = transfer.files;

            if (preview) {
                preview.textContent =
                    `Berhasil dioptimalkan menjadi WebP (${Math.round(finalFile.size / 1024)} KB).`;

                preview.classList.remove('text-danger');
                preview.classList.add('text-success');
            }
        }

        uploadForm.dataset.imageCompressed = 'true';

        if (submitButton) {
            submitButton.disabled = false;
            submitButton.textContent = originalLabel;
        }

        uploadForm.requestSubmit();
    });

});
