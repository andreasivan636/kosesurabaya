{{-- ═══════════════════════════════════════════════════════
    AI Customer Service - Floating Chat Widget
    Menggunakan Ollama (TinyLlama) lokal.
    Bisa diakses tanpa login (Guest).
    TIDAK menggunakan API WhatsApp.
═══════════════════════════════════════════════════════ --}}
<div x-data="aiChatWidget()" x-cloak>

    {{-- Floating Action Button --}}
    <button @click="toggleChat()"
            class="fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full bg-gradient-to-br from-brand-500 to-brand-600 text-white shadow-2xl shadow-brand-500/30 flex items-center justify-center hover:scale-110 transition-all duration-300 group"
            :class="{ 'animate-float': !isOpen }">
        <i data-feather="message-circle" class="w-6 h-6 transition-transform" :class="{ 'rotate-90 scale-0 hidden': isOpen }"></i>
        <i data-feather="x" class="w-6 h-6 transition-transform absolute" :class="{ 'rotate-0 scale-100': isOpen, 'rotate-90 scale-0 hidden': !isOpen }"></i>

        {{-- Notification dot --}}
        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-[10px] font-bold flex items-center justify-center animate-pulse"
              x-show="hasUnread">!</span>
    </button>

    {{-- Chat Window --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-24 right-4 z-50 w-[360px] max-w-[calc(100vw-2rem)] h-[500px] max-h-[70vh] flex flex-col rounded-2xl shadow-2xl overflow-hidden"
         style="background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.08);">

        {{-- Chat Header --}}
        <div class="flex items-center gap-3 px-4 py-3 border-b border-white/5 bg-gradient-to-r from-brand-600/20 to-brand-500/10">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center flex-shrink-0">
                <span class="text-lg">🤖</span>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-semibold text-white">CS KoSeSurabaya</h3>
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-brand-400 animate-pulse"></div>
                    <span class="text-xs text-gray-400">AI Assistant — Online 24 Jam</span>
                </div>
            </div>
            <button @click="clearChat()" class="p-1.5 rounded-lg hover:bg-white/10 transition text-gray-400 hover:text-white" title="Hapus Percakapan">
                <i data-feather="trash-2" class="w-4 h-4"></i>
            </button>
        </div>

        {{-- Chat Messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3" id="ai-chat-messages" x-ref="chatMessages">
            {{-- Welcome message --}}
            <template x-if="messages.length === 0">
                <div class="text-center py-6">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-gradient-to-br from-brand-400/20 to-brand-600/20 flex items-center justify-center">
                        <span class="text-3xl">🏠</span>
                    </div>
                    <h4 class="text-sm font-semibold text-white mb-1">Halo, Selamat Datang!</h4>
                    <p class="text-xs text-gray-400 mb-4 leading-relaxed">
                        Saya CS AI KoSeSurabaya. Tanyakan apa saja tentang kos di Surabaya!
                    </p>
                    {{-- Quick prompts --}}
                    <div class="space-y-2">
                        <button @click="sendQuickPrompt('Carikan kos murah dekat ITS')"
                                class="w-full text-left px-3 py-2 rounded-xl bg-white/5 text-xs text-gray-300 hover:bg-brand-500/10 hover:text-brand-400 transition border border-white/5">
                            🎓 Kos murah dekat ITS
                        </button>
                        <button @click="sendQuickPrompt('Kos putri yang aman dekat Unair')"
                                class="w-full text-left px-3 py-2 rounded-xl bg-white/5 text-xs text-gray-300 hover:bg-brand-500/10 hover:text-brand-400 transition border border-white/5">
                            🔒 Kos putri aman dekat Unair
                        </button>
                        <button @click="sendQuickPrompt('Rekomendasi kos dengan WiFi dan AC di bawah 1 juta')"
                                class="w-full text-left px-3 py-2 rounded-xl bg-white/5 text-xs text-gray-300 hover:bg-brand-500/10 hover:text-brand-400 transition border border-white/5">
                            💰 Kos WiFi + AC &lt; 1 juta
                        </button>
                        <button @click="sendQuickPrompt('Tips mencari kos agar tidak tertipu')"
                                class="w-full text-left px-3 py-2 rounded-xl bg-white/5 text-xs text-gray-300 hover:bg-brand-500/10 hover:text-brand-400 transition border border-white/5">
                            🛡️ Tips anti-penipuan
                        </button>
                    </div>
                </div>
            </template>

            {{-- Message bubbles --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.role === 'user'
                        ? 'bg-brand-500/20 text-brand-100 rounded-2xl rounded-tr-sm max-w-[85%] px-4 py-2.5'
                        : 'bg-white/5 text-gray-200 rounded-2xl rounded-tl-sm max-w-[85%] px-4 py-2.5'">
                        <p class="text-sm leading-relaxed whitespace-pre-wrap" x-text="msg.content"></p>
                        <span class="text-[10px] mt-1 block opacity-50" x-text="msg.time"></span>
                    </div>
                </div>
            </template>

            {{-- Typing indicator --}}
            <div x-show="isTyping" class="flex justify-start">
                <div class="bg-white/5 rounded-2xl rounded-tl-sm px-4 py-3">
                    <div class="flex gap-1.5">
                        <div class="w-2 h-2 bg-brand-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                        <div class="w-2 h-2 bg-brand-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                        <div class="w-2 h-2 bg-brand-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chat Input --}}
        <div class="px-4 py-3 border-t border-white/5">
            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                <input type="text"
                       x-model="newMessage"
                       placeholder="Tanyakan tentang kos..."
                       class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none transition"
                       :disabled="isTyping"
                       maxlength="1000">
                <button type="submit"
                        class="w-10 h-10 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 text-white flex items-center justify-center hover:shadow-lg hover:shadow-brand-500/25 transition-all active:scale-90 disabled:opacity-50"
                        :disabled="!newMessage.trim() || isTyping">
                    <i data-feather="send" class="w-4 h-4"></i>
                </button>
            </form>
            <p class="text-[10px] text-gray-600 mt-1.5 text-center">Ditenagai AI TinyLlama via Ollama • Tanpa WhatsApp API</p>
        </div>
    </div>
</div>

<script>
function aiChatWidget() {
    return {
        isOpen: false,
        messages: [],
        newMessage: '',
        isTyping: false,
        hasUnread: false,
        currentKosId: null,

        init() {
            // Expose function globally untuk diakses dari halaman detail
            window.openAIChat = (kosId) => {
                this.currentKosId = kosId;
                this.isOpen = true;
                if (kosId && this.messages.length === 0) {
                    this.sendQuickPrompt('Ceritakan tentang kos ini secara detail');
                }
            };
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            this.hasUnread = false;
            if (this.isOpen) {
                this.$nextTick(() => {
                    feather.replace();
                    this.scrollToBottom();
                });
            }
        },

        clearChat() {
            this.messages = [];
            this.currentKosId = null;
        },

        sendQuickPrompt(prompt) {
            this.newMessage = prompt;
            this.sendMessage();
        },

        async sendMessage() {
            const message = this.newMessage.trim();
            if (!message || this.isTyping) return;

            // Tambah pesan user
            const now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            this.messages.push({ role: 'user', content: message, time: now });
            this.newMessage = '';
            this.isTyping = true;
            this.scrollToBottom();

            try {
                const conversationHistory = this.messages.slice(-10).map(m => ({
                    role: m.role === 'user' ? 'user' : 'assistant',
                    content: m.content
                }));

                const response = await fetch('{{ route("api.ai.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        message: message,
                        kos_id: this.currentKosId,
                        conversation_history: conversationHistory.slice(0, -1),
                    }),
                });

                const data = await response.json();
                const aiTime = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                this.messages.push({
                    role: 'assistant',
                    content: data.response || 'Maaf, saya tidak bisa menjawab saat ini.',
                    time: aiTime
                });

                if (!this.isOpen) {
                    this.hasUnread = true;
                }

            } catch (error) {
                console.error('AI Chat Error:', error);
                this.messages.push({
                    role: 'assistant',
                    content: '⚠️ Maaf, layanan AI sedang tidak tersedia. Pastikan Ollama berjalan di localhost:11434.',
                    time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
                });
            } finally {
                this.isTyping = false;
                this.scrollToBottom();
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.chatMessages;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        }
    };
}
</script>
