<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                </svg>
            </div>
            <div>
                <h2 class="text-base font-semibold leading-tight text-gray-800">HR Assistant</h2>
                <p class="mt-0.5 text-xs leading-none text-gray-400">Ask me anything about leave, attendance, or payroll
                </p>
            </div>
            <div class="ml-auto flex items-center gap-1.5">
                <span class="inline-block h-2 w-2 rounded-full bg-green-400"></span>
                <span class="text-xs text-gray-400">Online</span>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto flex max-w-3xl flex-col px-4 py-6" style="height: calc(100vh - 140px);">

        {{-- Chat window --}}
        <div id="chat-window" class="mb-4 flex-1 space-y-4 overflow-y-auto scroll-smooth pr-1"
            style="scroll-behavior: smooth;">

            {{-- Welcome bubble (server-rendered) --}}
            <div class="flex items-start gap-3">
                <div class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div
                        class="inline-block max-w-full rounded-2xl rounded-tl-sm border border-gray-100 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm">
                        <p>👋 Hello, <strong>{{ auth()->user()->name }}</strong>! I'm your HR assistant.</p>
                        <p class="mt-1 text-gray-500">I can help you with leave balances, attendance records, payslips,
                            and HR policies. What would you like to know?</p>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <button onclick="sendQuick('attendance today')"
                            class="quick-btn rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs text-indigo-700 transition-colors hover:bg-indigo-100">
                            🕐 Attendance today
                        </button>
                        <button onclick="sendQuick('leave policy')"
                            class="quick-btn rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs text-indigo-700 transition-colors hover:bg-indigo-100">
                            📖 Leave policy
                        </button>
                        <button onclick="sendQuick('contact hr')"
                            class="quick-btn rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs text-indigo-700 transition-colors hover:bg-indigo-100">
                            📞 Contact HR
                        </button>
                        <button onclick="sendQuick('help')"
                            class="quick-btn rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs text-indigo-700 transition-colors hover:bg-indigo-100">
                            ❓ Help
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input bar --}}
        <div class="flex items-end gap-2 rounded-2xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
            <textarea id="chat-input" rows="1" placeholder="Ask me about your leave balance, attendance, payslip..."
                class="flex-1 resize-none bg-transparent text-sm leading-relaxed text-gray-700 placeholder-gray-400 focus:outline-none"
                style="max-height: 120px; overflow-y: auto;" onkeydown="handleKey(event)" oninput="autoResize(this)"></textarea>
            <button id="send-btn" onclick="sendMessage()"
                class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-indigo-600 transition-colors hover:bg-indigo-700 disabled:opacity-40">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13" />
                    <polygon points="22 2 15 22 11 13 2 9 22 2" />
                </svg>
            </button>
        </div>

        <p class="mt-2 text-center text-xs text-gray-300">
            Rule-based assistant · No AI · Your data stays private
        </p>
    </div>

    @push('scripts')
        <script>
            const CSRF = '{{ csrf_token() }}';
            const ROUTE = '{{ route('chatbot.message') }}';
            let busy = false;

            // ── Auto-resize textarea ──
            function autoResize(el) {
                el.style.height = 'auto';
                el.style.height = Math.min(el.scrollHeight, 120) + 'px';
            }

            // ── Enter to send, Shift+Enter for new line ──
            function handleKey(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            }

            // ── Quick-reply buttons ──
            function sendQuick(text) {
                document.getElementById('chat-input').value = text;
                sendMessage();
            }

            // ── Send message ──
            async function sendMessage() {
                if (busy) return;
                const input = document.getElementById('chat-input');
                const text = input.value.trim();
                if (!text) return;

                input.value = '';
                input.style.height = 'auto';
                appendUser(text);
                showTyping();
                busy = true;
                document.getElementById('send-btn').disabled = true;

                try {
                    const res = await fetch(ROUTE, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            message: text
                        }),
                    });
                    const data = await res.json();
                    removeTyping();
                    if (data.success) {
                        appendBot(data.response);
                    } else {
                        appendBot({
                            text: 'Something went wrong. Please try again.'
                        });
                    }
                } catch (err) {
                    removeTyping();
                    appendBot({
                        text: 'Network error. Please check your connection.'
                    });
                } finally {
                    busy = false;
                    document.getElementById('send-btn').disabled = false;
                    document.getElementById('chat-input').focus();
                }
            }

            // ── Append user bubble ──
            function appendUser(text) {
                const win = document.getElementById('chat-window');
                const wrap = document.createElement('div');
                wrap.className = 'flex justify-end';
                wrap.innerHTML = `
            <div class="bg-indigo-600 text-white rounded-2xl rounded-tr-sm px-4 py-3 text-sm max-w-xs shadow-sm">
                ${escHtml(text)}
            </div>`;
                win.appendChild(wrap);
                scrollBottom();
            }

            // ── Append bot bubble ──
            function appendBot(response) {
                const win = document.getElementById('chat-window');
                const wrap = document.createElement('div');
                wrap.className = 'flex items-start gap-3';

                const avatar = `
            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>`;

                // Parse markdown-like bold (**text**)
                const formatText = (t) => t
                    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\n/g, '<br>');

                let html = `
            <div class="bg-white border border-gray-100 rounded-2xl rounded-tl-sm px-4 py-3 text-sm text-gray-700 shadow-sm">
                ${formatText(response.text)}
            </div>`;

                // Cards
                if (response.cards && response.cards.length > 0) {
                    const colorMap = {
                        green: 'bg-green-50 border-green-200 text-green-800',
                        red: 'bg-red-50 border-red-200 text-red-800',
                        amber: 'bg-amber-50 border-amber-200 text-amber-800',
                        blue: 'bg-blue-50 border-blue-200 text-blue-800',
                        indigo: 'bg-indigo-50 border-indigo-200 text-indigo-800',
                        purple: 'bg-purple-50 border-purple-200 text-purple-800',
                    };
                    const cards = response.cards.map(c => {
                        const cls = colorMap[c.color] || colorMap.blue;
                        return `
                    <div class="border rounded-xl px-3 py-2.5 ${cls}">
                        <p class="text-xs font-medium opacity-70 mb-0.5">${escHtml(c.title)}</p>
                        <p class="text-sm font-semibold">${escHtml(c.value)}</p>
                        ${c.sub ? `<p class="text-xs opacity-60 mt-0.5">${escHtml(c.sub)}</p>` : ''}
                    </div>`;
                    }).join('');

                    html += `<div class="grid grid-cols-2 gap-2 mt-2">${cards}</div>`;
                }

                // Quick-reply buttons
                if (response.buttons && response.buttons.length > 0) {
                    const btns = response.buttons.map(b =>
                        `<button onclick="sendQuick('${escAttr(b.value)}')"
                    class="quick-btn text-xs px-3 py-1.5 rounded-full border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                    ${escHtml(b.label)}
                </button>`
                    ).join('');
                    html += `<div class="flex flex-wrap gap-2 mt-2">${btns}</div>`;
                }

                wrap.innerHTML = avatar + `<div class="flex-1 min-w-0">${html}</div>`;
                win.appendChild(wrap);
                scrollBottom();
            }

            // ── Typing indicator ──
            function showTyping() {
                const win = document.getElementById('chat-window');
                const el = document.createElement('div');
                el.id = 'typing-indicator';
                el.className = 'flex items-start gap-3';
                el.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm inline-flex items-center gap-1">
                <span class="typing-dot w-2 h-2 rounded-full bg-gray-300 inline-block"></span>
                <span class="typing-dot w-2 h-2 rounded-full bg-gray-300 inline-block" style="animation-delay:.15s"></span>
                <span class="typing-dot w-2 h-2 rounded-full bg-gray-300 inline-block" style="animation-delay:.3s"></span>
            </div>`;
                win.appendChild(el);
                scrollBottom();
            }

            function removeTyping() {
                document.getElementById('typing-indicator')?.remove();
            }

            // ── Helpers ──
            function scrollBottom() {
                const win = document.getElementById('chat-window');
                setTimeout(() => win.scrollTop = win.scrollHeight, 50);
            }

            function escHtml(s) {
                return String(s)
                    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            function escAttr(s) {
                return String(s).replace(/'/g, "\\'");
            }
        </script>

        <style>
            .typing-dot {
                animation: bounce 0.8s ease-in-out infinite;
            }

            @keyframes bounce {

                0%,
                100% {
                    transform: translateY(0);
                    opacity: .4;
                }

                50% {
                    transform: translateY(-4px);
                    opacity: 1;
                }
            }

            #chat-window::-webkit-scrollbar {
                width: 4px;
            }

            #chat-window::-webkit-scrollbar-track {
                background: transparent;
            }

            #chat-window::-webkit-scrollbar-thumb {
                background: #e5e7eb;
                border-radius: 2px;
            }
        </style>
    @endpush
</x-app-layout>
