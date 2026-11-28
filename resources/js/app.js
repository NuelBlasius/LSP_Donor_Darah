
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

function renderChatMarkdown(markdown) {
    const fragment = document.createDocumentFragment();
    const lines = markdown
        .replace(/\\([*_`~])/g, '$1')
        .replace(/([0-9])\\\./g, '$1.')
        .split(/\r?\n/);
    let paragraphLines = [];
    let activeList = null;

    const appendInline = (container, text) => {
        const tokenPattern = /(\*\*[^*]+\*\*|__[^_]+__|\*[^*\n]+\*|_[^_\n]+_|`[^`]+`|\[[^\]]+\]\(https?:\/\/[^\s)]+\))/g;
        let previousIndex = 0;

        for (const match of text.matchAll(tokenPattern)) {
            const token = match[0];
            const index = match.index;
            container.append(document.createTextNode(text.slice(previousIndex, index)));

            if (token.startsWith('**') || token.startsWith('__')) {
                const strong = document.createElement('strong');
                strong.textContent = token.slice(2, -2);
                container.append(strong);
            } else if (token.startsWith('`')) {
                const code = document.createElement('code');
                code.textContent = token.slice(1, -1);
                container.append(code);
            } else if (token.startsWith('[')) {
                const linkMatch = token.match(/^\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)$/);
                const link = document.createElement('a');
                link.textContent = linkMatch[1];
                link.href = linkMatch[2];
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                container.append(link);
            } else {
                const emphasis = document.createElement('em');
                emphasis.textContent = token.slice(1, -1);
                container.append(emphasis);
            }

            previousIndex = index + token.length;
        }

        container.append(document.createTextNode(text.slice(previousIndex)));
    };

    const flushParagraph = () => {
        if (paragraphLines.length === 0) {
            return;
        }

        const paragraph = document.createElement('p');
        appendInline(paragraph, paragraphLines.join(' '));
        fragment.append(paragraph);
        paragraphLines = [];
    };

    const flushList = () => {
        if (activeList) {
            fragment.append(activeList);
            activeList = null;
        }
    };

    for (const line of lines) {
        const trimmedLine = line.trim();
        const listMatch = trimmedLine.match(/^([-*+]\s+|\d+\.\s+)(.+)$/);
        const headingMatch = trimmedLine.match(/^(#{1,3})\s+(.+)$/);

        if (trimmedLine === '') {
            flushParagraph();
            flushList();
            continue;
        }

        if (listMatch) {
            flushParagraph();
            const isOrdered = /^\d/.test(listMatch[1]);
            const listTag = isOrdered ? 'ol' : 'ul';

            if (!activeList || activeList.tagName.toLowerCase() !== listTag) {
                flushList();
                activeList = document.createElement(listTag);
            }

            const item = document.createElement('li');
            appendInline(item, listMatch[2]);
            activeList.append(item);
            continue;
        }

        flushList();

        if (headingMatch) {
            flushParagraph();
            const heading = document.createElement(`h${headingMatch[1].length + 2}`);
            appendInline(heading, headingMatch[2]);
            fragment.append(heading);
            continue;
        }

        paragraphLines.push(trimmedLine);
    }

    flushParagraph();
    flushList();

    return fragment;
}

/**
 * Jalankan setelah halaman selesai dimuat.
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-row-href]').forEach((row) => {
        row.addEventListener('click', (event) => {
            if (!(event.target instanceof Element)) {
                return;
            }

            if (event.target.closest('a, button, form, input, select, textarea')) {
                return;
            }

            window.location.assign(row.dataset.rowHref);
        });

        row.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                window.location.assign(row.dataset.rowHref);
            }
        });
    });

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
        let pendingSectionId = null;

        function updateIndicator() {
            const activeLink = sectionNav.querySelector('.nav-link.active');

            if (!activeLink || !indicator) {
                return;
            }

            const navBounds = sectionNav.getBoundingClientRect();
            const linkBounds = activeLink.getBoundingClientRect();

            if (window.matchMedia('(max-width: 991.98px)').matches) {
                indicator.style.width = '3px';
                indicator.style.height = `${linkBounds.height}px`;
                indicator.style.transform = `translateY(${linkBounds.top - navBounds.top}px)`;
            } else {
                indicator.style.width = `${linkBounds.width}px`;
                indicator.style.height = '3px';
                indicator.style.transform = `translateX(${linkBounds.left - navBounds.left}px)`;
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

            const isAtPageBottom = window.scrollY + window.innerHeight
                >= document.documentElement.scrollHeight - 2;

            if (pendingSectionId) {
                const pendingSection = document.getElementById(pendingSectionId);

                if (
                    ! pendingSection ||
                    pendingSection.getBoundingClientRect().top <= activationLine ||
                    isAtPageBottom
                ) {
                    pendingSectionId = null;
                } else {
                    setActiveSection(pendingSectionId);

                    return;
                }
            }

            if (isAtPageBottom) {
                currentSection = sections[sections.length - 1];
            } else {
                sections.forEach((section) => {
                    if (section.getBoundingClientRect().top <= activationLine) {
                        currentSection = section;
                    }
                });
            }

            if (currentSection) {
                setActiveSection(currentSection.id);
            }
        }

        sectionLinks.forEach((link) => {
            link.addEventListener('click', (event) => {
                const sectionId = link.dataset.sectionLink;
                const target = document.getElementById(sectionId);

                if (! target) {
                    return;
                }

                event.preventDefault();
                pendingSectionId = sectionId;
                setActiveSection(link.dataset.sectionLink);
                history.replaceState(null, '', `#${sectionId}`);
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });

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

        const cancelPendingNavigation = () => {
            if (pendingSectionId) {
                pendingSectionId = null;
                updateActiveSection();
            }
        };

        window.addEventListener('scrollend', cancelPendingNavigation, { passive: true });
        window.addEventListener('wheel', cancelPendingNavigation, { passive: true });
        window.addEventListener('touchstart', cancelPendingNavigation, { passive: true });

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
const isLogoutUrl = (value) => {
    try {
        return new URL(value, window.location.href).pathname.replace(/\/+$/, '').endsWith('/logout');
    } catch {
        return false;
    }
};

let pendingLogoutAction = null;

const getLogoutDialog = () => {
    let dialog = document.getElementById('logout-confirmation-dialog');

    if (dialog) {
        return dialog;
    }

    const style = document.createElement('style');
    style.textContent = `
        #logout-confirmation-dialog {
            width: min(100% - 2rem, 24rem);
            max-width: 24rem;
            padding: 1.5rem;
            border: 1px solid #f1d5d9;
            border-radius: 1rem;
            color: #172033;
            box-shadow: 0 1.25rem 3rem rgb(22 32 51 / 20%);
        }
        #logout-confirmation-dialog::backdrop {
            background: rgb(20 25 35 / 45%);
            backdrop-filter: blur(2px);
        }
        #logout-confirmation-dialog h2 {
            margin: 0 0 .5rem;
            font-size: 1.25rem;
        }
        #logout-confirmation-dialog p {
            margin: 0;
            color: #687386;
        }
        #logout-confirmation-dialog .logout-dialog-actions {
            display: flex;
            justify-content: flex-end;
            gap: .625rem;
            margin-top: 1.25rem;
        }
        #logout-confirmation-dialog button {
            padding: .55rem .9rem;
            border: 1px solid #cbd2dc;
            border-radius: .55rem;
            background: #fff;
            color: #344054;
            font: inherit;
            cursor: pointer;
        }
        #logout-confirmation-dialog button[data-confirm-logout] {
            border-color: #c91f32;
            background: #c91f32;
            color: #fff;
        }
    `;
    document.head.append(style);

    dialog = document.createElement('dialog');
    dialog.id = 'logout-confirmation-dialog';
    dialog.setAttribute('aria-labelledby', 'logout-confirmation-title');
    dialog.innerHTML = `
        <h2 id="logout-confirmation-title">Keluar dari akun?</h2>
        <p>Anda yakin ingin mengakhiri sesi saat ini?</p>
        <div class="logout-dialog-actions">
            <button type="button" data-cancel-logout>Batal</button>
            <button type="button" data-confirm-logout>Keluar</button>
        </div>
    `;

    dialog.querySelector('[data-cancel-logout]').addEventListener('click', () => {
        pendingLogoutAction = null;
        dialog.close();
    });

    dialog.querySelector('[data-confirm-logout]').addEventListener('click', () => {
        const action = pendingLogoutAction;
        pendingLogoutAction = null;
        dialog.close();
        action?.();
    });

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            pendingLogoutAction = null;
            dialog.close();
        }
    });

    dialog.addEventListener('cancel', () => {
        pendingLogoutAction = null;
    });

    document.body.append(dialog);

    return dialog;
};

const confirmLogout = (action) => {
    pendingLogoutAction = action;
    getLogoutDialog().showModal();
};

const confirmedLogoutForms = new WeakSet();

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || !isLogoutUrl(form.action)) {
        return;
    }

    if (confirmedLogoutForms.has(form)) {
        confirmedLogoutForms.delete(form);

        return;
    }

    event.preventDefault();
    confirmLogout(() => {
        confirmedLogoutForms.add(form);
        form.requestSubmit(event.submitter ?? undefined);
    });
});

document.addEventListener('click', (event) => {
    const logoutLink = event.target.closest('a[href]');

    if (!logoutLink || !isLogoutUrl(logoutLink.href)) {
        return;
    }

    event.preventDefault();
    confirmLogout(() => {
        window.location.assign(logoutLink.href);
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('donorChatForm');
    const transcript = document.getElementById('donorChatTranscript');
    const input = document.getElementById('donorChatInput');
    const sendButton = document.getElementById('donorChatSend');
    const status = document.getElementById('donorChatStatus');
    const clearButton = document.getElementById('clearDonorChat');

    if (!form || !transcript || !input || !sendButton || !status) {
        return;
    }

    transcript.querySelectorAll('.chat-message-content').forEach((content) => {
        content.replaceChildren(renderChatMarkdown(content.textContent));
    });

    const appendMessage = (role, text) => {
        const message = document.createElement('div');
        message.className = `chat-message chat-message-${role}`;
        message.dataset.chatTurn = 'true';

        const label = document.createElement('span');
        label.className = 'chat-message-label';
        label.textContent = role === 'user' ? 'Anda' : 'Asisten';

        const content = document.createElement('div');
        content.className = 'chat-message-content';
        content.textContent = text;
        message.append(label, content);
        content.replaceChildren(renderChatMarkdown(text));
        transcript.append(message);
        transcript.scrollTop = transcript.scrollHeight;

        return message;
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const message = input.value.trim();

        if (!message || sendButton.disabled) {
            return;
        }

        const history = Array.from(transcript.querySelectorAll('[data-chat-turn="true"]'))
            .map((chatMessage) => ({
                role: chatMessage.classList.contains('chat-message-user') ? 'user' : 'assistant',
                text: chatMessage.querySelector('.chat-message-content').textContent,
            }))
            .slice(-11);

        appendMessage('user', message);
        input.value = '';
        sendButton.disabled = true;
        status.textContent = 'Sedang menyiapkan jawaban...';
        const waitingMessage = appendMessage('assistant', 'Sedang mencari jawaban...');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify({ message, history }),
            });
            const data = await response.json();
            const replyContent = waitingMessage.querySelector('.chat-message-content');
            const replyText = response.ok
                ? data.reply
                : (data.message ?? 'Permintaan belum dapat diproses. Silakan coba kembali.');
            replyContent.replaceChildren(renderChatMarkdown(replyText));
            status.textContent = response.ok ? 'Jawaban AI bersifat edukatif.' : 'Silakan periksa konfigurasi lalu coba kembali.';
        } catch {
            waitingMessage.querySelector('.chat-message-content').textContent = 'Terjadi kendala saat menghubungi chatbot. Silakan coba kembali.';
            status.textContent = 'Koneksi chatbot terganggu.';
        } finally {
            sendButton.disabled = false;
            input.focus();
            transcript.scrollTop = transcript.scrollHeight;
        }
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            form.requestSubmit();
        }
    });

    document.querySelectorAll('.chat-suggestion').forEach((button) => {
        button.addEventListener('click', () => {
            input.value = button.textContent.trim();
            form.requestSubmit();
        });
    });

    clearButton?.addEventListener('click', () => {
        transcript.replaceChildren(transcript.firstElementChild);
        input.value = '';
        status.textContent = 'Percakapan telah dibersihkan.';
    });

    window.addEventListener('pagehide', () => {
        transcript.replaceChildren(transcript.firstElementChild);
        input.value = '';
    });

    transcript.scrollTop = transcript.scrollHeight;
});

