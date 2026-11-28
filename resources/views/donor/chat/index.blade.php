@extends('layouts.app')

@section('title', 'AI Chatbot Donor Darah')
@section('page-title', 'AI Chatbot')

@section('content')
    <section class="chat-page">
        <div class="chat-heading">
            <div>
                <span class="chat-eyebrow">Pusat bantuan pendonor</span>
                <h2 class="display-6 fw-bold mb-2">Tanya seputar donor darah</h2>
                <p class="text-secondary mb-0">Dapatkan informasi umum tentang persiapan, proses, dan perawatan setelah donor.</p>
            </div>
            <div class="chat-heading-mark" aria-hidden="true">✦</div>
        </div>

        <div class="chat-disclaimer">
            <span aria-hidden="true">ⓘ</span>
            Informasi chatbot bersifat edukatif. Untuk penilaian kondisi kesehatan dan kelayakan donor, konsultasikan dengan petugas kesehatan atau PMI.
        </div>

        <section class="chat-panel" aria-label="Percakapan dengan AI Chatbot donor darah">
            <div class="chat-toolbar">
                <div class="d-flex align-items-center gap-3">
                    <span class="chat-avatar" aria-hidden="true">✦</span>
                    <div>
                        <h3 class="h5 fw-bold mb-0">Asisten Donor Darah</h3>
                        <span class="small text-secondary">Siap membantu pertanyaan donor darah</span>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" id="clearDonorChat">Bersihkan chat</button>
            </div>

            <div class="chat-transcript" id="donorChatTranscript" aria-live="polite" aria-relevant="additions text">
                <div class="chat-message chat-message-assistant">
                    <span class="chat-message-label">Asisten</span>
                    <div class="chat-message-content">Halo, saya siap membantu menjawab pertanyaan seputar donor darah. Apa yang ingin Anda ketahui?</div>
                </div>
            </div>

            <div class="chat-suggestions" aria-label="Contoh pertanyaan">
                <button type="button" class="chat-suggestion">Apa yang perlu disiapkan sebelum donor?</button>
                <button type="button" class="chat-suggestion">Apa yang sebaiknya dilakukan setelah donor?</button>
                <button type="button" class="chat-suggestion">Bagaimana proses donor darah?</button>
            </div>

            <form class="chat-composer" id="donorChatForm" action="{{ route('donor.chat.message') }}" method="POST">
                @csrf
                <label class="visually-hidden" for="donorChatInput">Tulis pertanyaan tentang donor darah</label>
                <textarea id="donorChatInput" name="message" rows="1" maxlength="1000" placeholder="Tulis pertanyaan tentang donor darah..." required></textarea>
                <button type="submit" class="btn btn-danger chat-send-button" id="donorChatSend">
                    <span>Kirim</span><span aria-hidden="true">➤</span>
                </button>
                <div class="chat-composer-note" id="donorChatStatus" role="status">Percakapan tidak disimpan dan terhapus saat Anda meninggalkan halaman.</div>
            </form>
        </section>
    </section>
@endsection
