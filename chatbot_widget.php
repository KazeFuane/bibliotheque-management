<?php
$botContext = "";
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT * FROM livres");
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($books) > 0) {
            $context = "Here is the exact current inventory of books in the database (total: " . count($books) . "):\n";
            foreach ($books as $b) {
                $context .= "- '" . htmlspecialchars($b['titre']) . "' by " . htmlspecialchars($b['auteur']) . " (" . $b['annee'] . "). Genre: " . htmlspecialchars($b['genre']) . ". Price: " . $b['prix'] . "€. Language: " . htmlspecialchars($b['langue']) . ".\n";
            }
            $botContext = $context;
        } else {
            $botContext = "The library database is currently empty (0 books).";
        }
    } catch (Exception $e) {
        $botContext = "Unable to read database.";
    }
}
?>
<!-- Chatbot Floating Widget -->
<div id="chatbot-widget">
  <!-- Floating toggle button -->
  <button id="chatbot-toggle" title="Assistant" onclick="toggleChatbot()">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
    </svg>
    <span id="chatbot-badge" style="display:none;">1</span>
  </button>

  <!-- Chat panel -->
  <div id="chatbot-panel">
    <!-- Header -->
    <div id="cb-header">
      <div id="cb-header-left">
        <div id="cb-avatar">
          <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="8" cy="6" r="3"/>
            <path d="M2 14c0-3 2.7-5 6-5s6 2 6 5"/>
          </svg>
        </div>
        <div>
          <div id="cb-title">Personal Assistant</div>
          <div id="cb-sub">Multi-provider AI</div>
        </div>
      </div>
      <div id="cb-header-actions">
        <button id="cb-clear-btn" onclick="cbClearChat()">Clear</button>
        <button id="cb-close-btn" onclick="toggleChatbot()" title="Close">✕</button>
      </div>
    </div>

    <!-- API key banner -->
    <div id="cb-api-banner">
      <span>🔑</span>
      <select id="cb-provider-select">
        <option value="openai">OpenAI</option>
        <option value="claude">Claude</option>
        <option value="gemini">Gemini</option>
        <option value="groq">Groq</option>
        <option value="mistral">Mistral</option>
      </select>
      <input type="password" id="cb-api-key-input" placeholder="Paste your API key">
      <button id="cb-save-key-btn" onclick="cbSaveKey()">Save</button>
    </div>

    <!-- Messages -->
    <div id="cb-messages">
      <div class="cb-sys-msg">Today</div>
      <div class="cb-msg cb-assistant">
        <div class="cb-msg-avatar">
          <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="8" cy="6" r="3"/>
            <path d="M2 14c0-3 2.7-5 6-5s6 2 6 5"/>
          </svg>
        </div>
        <div class="cb-bubble">Hi! I'm your personal assistant. Ask me anything.</div>
      </div>
    </div>

    <!-- Input area -->
    <div id="cb-input-area">
      <div id="cb-input-row">
        <textarea id="cb-input-box" placeholder="Message your assistant…" rows="1"></textarea>
        <button id="cb-send-btn" disabled onclick="cbSendMessage()">
          <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2L7 9M14 2L9 14l-2-5-5-2 12-5z"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  /* ===== Chatbot Widget Styles ===== */
  #chatbot-widget {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 99999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  }

  /* Toggle button */
  #chatbot-toggle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #3b5bdb, #2563eb);
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(37, 99, 235, 0.4), 0 2px 8px rgba(0,0,0,0.15);
    transition: transform 0.3s cubic-bezier(.4,0,.2,1), box-shadow 0.3s ease;
    position: relative;
  }

  #chatbot-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 28px rgba(37, 99, 235, 0.5), 0 4px 12px rgba(0,0,0,0.2);
  }

  #chatbot-toggle svg {
    width: 26px;
    height: 26px;
  }

  #chatbot-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 18px;
    height: 18px;
    background: #ef4444;
    color: #fff;
    border-radius: 50%;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
  }

  /* Pulse animation on button */
  #chatbot-toggle::after {
    content: '';
    position: absolute;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: rgba(59, 91, 219, 0.3);
    animation: cb-pulse 2.5s ease-out infinite;
    z-index: -1;
  }

  @keyframes cb-pulse {
    0% { transform: scale(1); opacity: 0.6; }
    100% { transform: scale(1.6); opacity: 0; }
  }

  /* Chat panel */
  #chatbot-panel {
    position: absolute;
    bottom: 70px;
    right: 0;
    width: 380px;
    height: 520px;
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e5e3;
    box-shadow: 0 12px 40px rgba(0,0,0,0.15), 0 4px 12px rgba(0,0,0,0.08);
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: cb-slideUp 0.3s cubic-bezier(.4,0,.2,1);
  }

  #chatbot-panel.open {
    display: flex;
  }

  @keyframes cb-slideUp {
    from { opacity: 0; transform: translateY(16px) scale(0.96); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
  }

  /* Header */
  #cb-header {
    padding: 12px 14px;
    border-bottom: 1px solid #e5e5e3;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, #f8f9ff, #eef1fb);
  }

  #cb-header-left {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  #cb-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #dbe4ff;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  #cb-avatar svg {
    width: 15px;
    height: 15px;
    color: #3b5bdb;
  }

  #cb-title {
    font-size: 13px;
    font-weight: 600;
    color: #1c1c1a;
  }

  #cb-sub {
    font-size: 11px;
    color: #888;
  }

  #cb-header-actions {
    display: flex;
    gap: 6px;
    align-items: center;
  }

  #cb-clear-btn {
    font-size: 11px;
    color: #666;
    background: none;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 3px 10px;
    cursor: pointer;
  }
  #cb-clear-btn:hover { background: #f0f0f0; }

  #cb-close-btn {
    font-size: 14px;
    color: #999;
    background: none;
    border: none;
    cursor: pointer;
    padding: 2px 6px;
    border-radius: 6px;
  }
  #cb-close-btn:hover { background: #f0f0f0; color: #333; }

  /* API banner */
  #cb-api-banner {
    background: #fff8e1;
    border-bottom: 1px solid #ffe082;
    padding: 8px 12px;
    font-size: 12px;
    color: #7a5c00;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }

  #cb-api-banner.cb-hidden { display: none; }

  #cb-api-banner select {
    border: 1px solid #ffc107;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 12px;
    background: #fff;
    outline: none;
    cursor: pointer;
  }

  #cb-api-banner input {
    flex: 1;
    border: 1px solid #ffc107;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 12px;
    background: #fff;
    outline: none;
    font-family: monospace;
    min-width: 100px;
    width: auto;
  }

  #cb-save-key-btn {
    background: #ffc107;
    border: none;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 12px;
    cursor: pointer;
    font-weight: 500;
    color: #4a3800;
  }
  #cb-save-key-btn:hover { background: #ffb300; }

  /* Messages */
  #cb-messages {
    flex: 1;
    overflow-y: auto;
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .cb-sys-msg {
    text-align: center;
    font-size: 11px;
    color: #bbb;
  }

  .cb-msg {
    display: flex;
    gap: 7px;
    max-width: 90%;
  }

  .cb-msg.cb-user {
    align-self: flex-end;
    flex-direction: row-reverse;
  }

  .cb-msg.cb-assistant {
    align-self: flex-start;
  }

  .cb-bubble {
    padding: 8px 12px;
    border-radius: 14px;
    font-size: 13px;
    line-height: 1.55;
    word-break: break-word;
    white-space: pre-wrap;
  }

  .cb-msg.cb-user .cb-bubble {
    background: #3b5bdb;
    color: #fff;
    border-bottom-right-radius: 4px;
  }

  .cb-msg.cb-assistant .cb-bubble {
    background: #f1f1ef;
    color: #1c1c1a;
    border-bottom-left-radius: 4px;
  }

  .cb-msg-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    font-weight: 600;
    margin-top: 2px;
  }

  .cb-msg.cb-user .cb-msg-avatar {
    background: #dbe4ff;
    color: #3b5bdb;
  }

  .cb-msg.cb-assistant .cb-msg-avatar {
    background: #f1f1ef;
    border: 1px solid #e5e5e3;
  }

  .cb-msg.cb-assistant .cb-msg-avatar svg {
    width: 12px;
    height: 12px;
    color: #666;
  }

  /* Typing dots */
  .cb-typing { display: flex; gap: 4px; align-items: center; padding: 3px 0; }
  .cb-dot {
    width: 5px; height: 5px; border-radius: 50%;
    background: #aaa;
    animation: cb-blink 1.2s infinite;
  }
  .cb-dot:nth-child(2) { animation-delay: 0.2s; }
  .cb-dot:nth-child(3) { animation-delay: 0.4s; }
  @keyframes cb-blink {
    0%, 80%, 100% { opacity: 0.3; }
    40% { opacity: 1; }
  }

  /* Input area */
  #cb-input-area {
    padding: 10px 12px;
    border-top: 1px solid #e5e5e3;
  }

  #cb-input-row {
    display: flex;
    gap: 6px;
    align-items: flex-end;
  }

  #cb-input-box {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 8px 12px;
    font-size: 13px;
    font-family: inherit;
    resize: none;
    min-height: 36px;
    max-height: 80px;
    color: #1c1c1a;
    outline: none;
    line-height: 1.4;
    background: #fff;
    width: auto;
  }

  #cb-input-box:focus { border-color: #adb5bd; }

  #cb-send-btn {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: none;
    background: #3b5bdb;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  #cb-send-btn:hover { background: #364fc7; }
  #cb-send-btn:disabled { background: #c5cce8; cursor: default; }

  #cb-send-btn svg {
    width: 14px;
    height: 14px;
    color: #fff;
  }

  /* Responsive: smaller screens */
  @media (max-width: 440px) {
    #chatbot-panel {
      width: calc(100vw - 32px);
      right: -8px;
      height: 70vh;
    }
  }
</style>

<script>
(function() {
  // === Chatbot Widget Logic (isolated in IIFE) ===
  const cbPanel = document.getElementById('chatbot-panel');
  const cbToggle = document.getElementById('chatbot-toggle');
  const cbMessagesEl = document.getElementById('cb-messages');
  const cbInputBox = document.getElementById('cb-input-box');
  const cbSendBtn = document.getElementById('cb-send-btn');
  const cbApiBanner = document.getElementById('cb-api-banner');
  const cbApiKeyInput = document.getElementById('cb-api-key-input');
  const cbProviderSelect = document.getElementById('cb-provider-select');

  let cbHistory = [];
  function cbSaveHistory() { sessionStorage.setItem('cb_history', JSON.stringify(cbHistory)); }
  let cbIsLoading = false;
  let cbApiKey = localStorage.getItem('chat_api_key') || '';
  let cbProvider = localStorage.getItem('chat_provider') || 'openai';
  cbProviderSelect.value = cbProvider;

  if (cbApiKey) cbApiBanner.classList.add('cb-hidden');

  const CB_PLACEHOLDERS = {
    openai: 'sk-...', claude: 'sk-ant-...', gemini: 'API key',
    groq: 'gsk_...', mistral: 'API key'
  };
  cbApiKeyInput.placeholder = CB_PLACEHOLDERS[cbProvider] || 'API key';

  cbProviderSelect.addEventListener('change', () => {
    cbProvider = cbProviderSelect.value;
    localStorage.setItem('chat_provider', cbProvider);
    cbApiKeyInput.placeholder = CB_PLACEHOLDERS[cbProvider] || 'API key';
    document.getElementById('cb-sub').textContent = 'Powered by ' + cbProviderSelect.selectedOptions[0].text;
  });

  // Toggle panel
  window.toggleChatbot = function() {
    cbPanel.classList.toggle('open');
    if (cbPanel.classList.contains('open')) {
      sessionStorage.setItem('cb_is_open', 'true');
      cbInputBox.focus();
    } else {
      sessionStorage.setItem('cb_is_open', 'false');
    }
  };

  // Restore panel state
  if (sessionStorage.getItem('cb_is_open') === 'true') {
    cbPanel.classList.add('open');
  }

  // Save key
  window.cbSaveKey = function() {
    const val = cbApiKeyInput.value.trim();
    if (!val) { alert('Please enter an API key.'); return; }
    cbApiKey = val;
    cbProvider = cbProviderSelect.value;
    localStorage.setItem('chat_api_key', cbApiKey);
    localStorage.setItem('chat_provider', cbProvider);
    cbApiBanner.classList.add('cb-hidden');
    document.getElementById('cb-sub').textContent = 'Powered by ' + cbProviderSelect.selectedOptions[0].text;
  };

  // Clear chat
  window.cbClearChat = function() {
    cbHistory = [];
    cbSaveHistory();
    cbMessagesEl.innerHTML = '<div class="cb-sys-msg">Chat cleared</div>';
    cbAddBubble('assistant', 'Chat cleared! How can I help you?');
  };

  // Input handling
  cbInputBox.addEventListener('input', () => {
    cbSendBtn.disabled = cbInputBox.value.trim() === '' || cbIsLoading;
    cbInputBox.style.height = 'auto';
    cbInputBox.style.height = Math.min(cbInputBox.scrollHeight, 80) + 'px';
  });

  cbInputBox.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); if (!cbSendBtn.disabled) cbSendMessage(); }
  });

  function cbAddBubble(role, text) {
    const div = document.createElement('div');
    div.className = `cb-msg cb-${role}`;
    const avatarHTML = role === 'user'
      ? '<div class="cb-msg-avatar">You</div>'
      : '<div class="cb-msg-avatar"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="6" r="3"/><path d="M2 14c0-3 2.7-5 6-5s6 2 6 5"/></svg></div>';
    div.innerHTML = `${avatarHTML}<div class="cb-bubble"></div>`;
    div.querySelector('.cb-bubble').textContent = text;
    cbMessagesEl.appendChild(div);
    cbMessagesEl.scrollTop = cbMessagesEl.scrollHeight;
    return div.querySelector('.cb-bubble');
  }

  function cbAddTyping() {
    const div = document.createElement('div');
    div.className = 'cb-msg cb-assistant';
    div.id = 'cb-typing-indicator';
    div.innerHTML = '<div class="cb-msg-avatar"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="6" r="3"/><path d="M2 14c0-3 2.7-5 6-5s6 2 6 5"/></svg></div><div class="cb-bubble"><div class="cb-typing"><div class="cb-dot"></div><div class="cb-dot"></div><div class="cb-dot"></div></div></div>';
    cbMessagesEl.appendChild(div);
    cbMessagesEl.scrollTop = cbMessagesEl.scrollHeight;
  }

  function cbRemoveTyping() {
    const el = document.getElementById('cb-typing-indicator');
    if (el) el.remove();
  }

  /* Provider configs */
  const CB_PROVIDERS = {
    openai: {
      url: 'https://api.openai.com/v1/chat/completions',
      headers: () => ({ 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + cbApiKey }),
      body: (msgs, sys) => JSON.stringify({ model: 'gpt-4o', max_tokens: 1024, messages: [{ role: 'system', content: sys }, ...msgs] }),
      parse: (d) => ({ error: d.error?.message, text: d.choices?.[0]?.message?.content })
    },
    claude: {
      url: 'https://api.anthropic.com/v1/messages',
      headers: () => ({ 'Content-Type': 'application/json', 'x-api-key': cbApiKey, 'anthropic-version': '2023-06-01', 'anthropic-dangerous-direct-browser-client': 'true' }),
      body: (msgs, sys) => JSON.stringify({ model: 'claude-sonnet-4-20250514', max_tokens: 1024, system: sys, messages: msgs }),
      parse: (d) => ({ error: d.error?.message, text: d.content?.find(b => b.type === 'text')?.text })
    },
    gemini: {
      url: () => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' + cbApiKey,
      headers: () => ({ 'Content-Type': 'application/json' }),
      body: (msgs, sys) => JSON.stringify({ system_instruction: { parts: [{ text: sys }] }, contents: msgs.map(m => ({ role: m.role === 'assistant' ? 'model' : 'user', parts: [{ text: m.content }] })) }),
      parse: (d) => ({ error: d.error?.message, text: d.candidates?.[0]?.content?.parts?.[0]?.text })
    },
    groq: {
      url: 'https://api.groq.com/openai/v1/chat/completions',
      headers: () => ({ 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + cbApiKey }),
      body: (msgs, sys) => JSON.stringify({ model: 'llama-3.3-70b-versatile', max_tokens: 1024, messages: [{ role: 'system', content: sys }, ...msgs] }),
      parse: (d) => ({ error: d.error?.message, text: d.choices?.[0]?.message?.content })
    },
    mistral: {
      url: 'https://api.mistral.ai/v1/chat/completions',
      headers: () => ({ 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + cbApiKey }),
      body: (msgs, sys) => JSON.stringify({ model: 'mistral-small-latest', max_tokens: 1024, messages: [{ role: 'system', content: sys }, ...msgs] }),
      parse: (d) => ({ error: d.error?.message, text: d.choices?.[0]?.message?.content })
    }
  };

  const savedHistory = sessionStorage.getItem('cb_history');
  if (savedHistory) {
    try {
      const parsed = JSON.parse(savedHistory);
      if (parsed && parsed.length > 0) {
        cbHistory = parsed;
        cbMessagesEl.innerHTML = '<div class="cb-sys-msg">Today</div>';
        cbHistory.forEach(msg => cbAddBubble(msg.role, msg.content));
      }
    } catch(e) {
      cbHistory = [];
    }
  }

  const CB_DYNAMIC_CONTEXT = <?php echo json_encode($botContext ?? ""); ?>;
  const CB_SYSTEM_PROMPT = 'You are a helpful, friendly assistant integrated into a PHP-based Library Management System (Gestion de Bibliothèque). The application allows users to view books, add new books, modify existing books, and delete books. Books have attributes like titre (title), auteur (author), genre, annee (year), iban, prix (price), and langue (language).' + CB_DYNAMIC_CONTEXT + ' Assist users with questions about the application or general knowledge. Be concise and clear.';

  window.cbSendMessage = async function() {
    if (!cbApiKey) { alert('Please enter your API key first.'); cbApiBanner.classList.remove('cb-hidden'); return; }
    const text = cbInputBox.value.trim();
    if (!text || cbIsLoading) return;

    cbIsLoading = true;
    cbSendBtn.disabled = true;
    cbInputBox.value = '';
    cbInputBox.style.height = 'auto';

    cbAddBubble('user', text);
    cbHistory.push({ role: 'user', content: text });
    cbSaveHistory();
    cbAddTyping();

    try {
      const cfg = CB_PROVIDERS[cbProvider];
      const url = typeof cfg.url === 'function' ? cfg.url() : cfg.url;
      const res = await fetch(url, {
        method: 'POST',
        headers: cfg.headers(),
        body: cfg.body(cbHistory, CB_SYSTEM_PROMPT)
      });
      const data = await res.json();
      cbRemoveTyping();
      const result = cfg.parse(data);
      if (result.error) {
        cbAddBubble('assistant', 'API error: ' + result.error);
        cbHistory.pop();
        cbSaveHistory();
      } else {
        const reply = result.text || 'No response.';
        cbHistory.push({ role: 'assistant', content: reply });
        cbSaveHistory();
        cbAddBubble('assistant', reply);
      }
    } catch (err) {
      cbRemoveTyping();
      cbAddBubble('assistant', 'Network error — check your connection.');
      cbHistory.pop();
      cbSaveHistory();
    }

    cbIsLoading = false;
    cbSendBtn.disabled = cbInputBox.value.trim() === '';
    cbInputBox.focus();
  };
})();
</script>
