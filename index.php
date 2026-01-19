<?php
?><!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Coopy - Texto rapido</title>
  <style>
    :root {
      --bg: #f4f1ea;
      --card: #ffffff;
      --ink: #2b2b2b;
      --muted: #6f6a60;
      --accent: #2f7d6d;
      --accent-weak: #e0efe9;
      --shadow: 0 18px 40px rgba(26, 24, 21, 0.12);
      --radius: 20px;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: "Optima", "Palatino Linotype", "Book Antiqua", serif;
      color: var(--ink);
      background: radial-gradient(circle at top, #fef9f1 0%, var(--bg) 65%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px 16px 48px;
    }

    .page {
      /* width: min(980px, 100%); */
      display: grid;
      gap: 20px;
    }

    header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    header h1 {
      margin: 0;
      font-size: clamp(24px, 4vw, 34px);
      letter-spacing: 0.04em;
      text-transform: uppercase;
    }

    header span {
      font-size: 14px;
      color: var(--muted);
    }

    .card {
      background: var(--card);
      border-radius: var(--radius);
      padding: clamp(20px, 3vw, 36px);
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
      animation: rise 0.6s ease;
    }

    .card::after {
      content: "";
      position: absolute;
      inset: -20% -30% auto auto;
      width: 260px;
      height: 260px;
      background: radial-gradient(circle, rgba(47, 125, 109, 0.16), transparent 70%);
      pointer-events: none;
    }

    .grid {
      display: grid;
      gap: 16px;
    }

    .row {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
    }

    .divider {
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      align-items: center;
      gap: 14px;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.16em;
      font-size: 12px;
      margin: 26px 0;
    }

    .divider::before,
    .divider::after {
      content: "";
      height: 1px;
      background: #dcd4c6;
    }

    input, textarea {
      font: inherit;
      padding: 12px 14px;
      border-radius: 14px;
      border: 1px solid #d6d0c4;
      background: #fffaf2;
      color: var(--ink);
      outline: none;
      transition: border 0.2s ease, box-shadow 0.2s ease;
      width: 100%;
    }

    input:focus, textarea:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(47, 125, 109, 0.12);
    }

    textarea {
      min-height: 120px;
      resize: vertical;
    }

    .btn {
      border: 0;
      background: var(--accent);
      color: #fff;
      padding: 12px 18px;
      border-radius: 999px;
      font-size: 15px;
      cursor: pointer;
      transition: transform 0.15s ease, box-shadow 0.15s ease;
      box-shadow: 0 10px 18px rgba(47, 125, 109, 0.25);
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn:hover {
      transform: translateY(-1px);
    }

    .btn.secondary {
      background: #f0ebe1;
      color: var(--ink);
      box-shadow: none;
    }

    .badge {
      display: inline-flex;
      padding: 8px 16px;
      border-radius: 999px;
      background: var(--accent-weak);
      color: var(--accent);
      font-weight: 600;
      letter-spacing: 0.08em;
      font-size: 18px;
    }

    .section-title {
      font-size: 14px;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.1em;
      margin-bottom: 6px;
    }

    .code-inputs {
      display: grid;
      grid-template-columns: repeat(4, minmax(46px, 1fr));
      gap: 10px;
      max-width: 260px;
    }

    .code-inputs input {
      text-align: center;
      font-size: 22px;
      letter-spacing: 0.12em;
      text-transform: uppercase;
    }

    .hidden {
      display: none;
    }

    .live-box {
      border: 1px dashed #c8c2b5;
      padding: 16px;
      border-radius: 16px;
      background: #fffdf8;
      min-height: 60px;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    .live-box:hover {
      background: #f7f1e7;
    }

    .history {
      display: grid;
      gap: 10px;
    }

    .history-item {
      padding: 12px 14px;
      border-radius: 14px;
      background: #f6f2ea;
      cursor: pointer;
      transition: transform 0.15s ease, background 0.2s ease;
    }

    .history-item:hover {
      transform: translateY(-1px);
      background: #efe8db;
    }

    .toast {
      position: fixed;
      right: 22px;
      bottom: 22px;
      background: #2b2b2b;
      color: #fff;
      padding: 12px 18px;
      border-radius: 999px;
      box-shadow: 0 14px 30px rgba(0, 0, 0, 0.2);
      opacity: 0;
      transform: translateY(10px);
      transition: opacity 0.25s ease, transform 0.25s ease;
      pointer-events: none;
    }

    .toast.show {
      opacity: 1;
      transform: translateY(0);
    }

    footer {
      font-size: 13px;
      color: var(--muted);
    }

    @keyframes rise {
      from {
        opacity: 0;
        transform: translateY(18px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 720px) {
      header {
        flex-direction: column;
        align-items: flex-start;
      }

      .row {
        flex-direction: column;
        align-items: stretch;
      }

      .btn {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <div class="page">
    <header>
      <h1>Coopy</h1>
      <span>Leve seu texto do celular para o computador.</span>
    </header>

    <main class="card">
      <section id="landing" class="grid">
        <div>
          <div class="section-title">Entrar na sessao</div>
          <div class="row">
            <div class="code-inputs" id="codeInputs">
              <input class="code-digit" inputmode="text" maxlength="1" aria-label="Codigo 1" />
              <input class="code-digit" inputmode="text" maxlength="1" aria-label="Codigo 2" />
              <input class="code-digit" inputmode="text" maxlength="1" aria-label="Codigo 3" />
              <input class="code-digit" inputmode="text" maxlength="1" aria-label="Codigo 4" />
            </div>
            <button class="btn secondary" id="joinBtn">Conectar</button>
          </div>
        </div>
        <div class="divider">ou</div>
        <div>
          <div class="section-title">Ou comece agora</div>
          <button class="btn" id="createBtn">Criar sessao</button>
        </div>
      </section>

      <section id="session" class="grid hidden">
        <div class="row" style="justify-content: space-between;">
          <div>
            <div class="section-title">Codigo da sessao</div>
            <div class="badge" id="sessionCode"></div>
          </div>
          <!-- <button class="btn secondary" id="refreshBtn">Atualizar</button> -->
        </div>

        <div id="sendSection">
          <div class="section-title">Enviar texto</div>
          <textarea id="textInput" placeholder="Cole ou digite aqui..."></textarea>
          <div class="row" style="margin-top: 10px;">
            <button class="btn" id="sendBtn">Enviar</button>
            <span id="status" class="section-title"></span>
          </div>
        </div>

        <div>
          <div class="section-title">Historico da sessao (clique para copiar)</div>
          <div class="history" id="history"></div>
        </div>
      </section>
    </main>

    <footer>
      Os textos expiram automaticamente em 24 horas (limpeza ocorre em cada acesso).
    </footer>
  </div>

  <div class="toast" id="toast">Enviado!</div>

  <script>
    const apiUrl = 'api.php';
    const storageKey = 'coopy_device_key';
    let sessionCode = '';
    let deviceKey = localStorage.getItem(storageKey) || '';
    let lastMessageId = 0;
    let pollTimer = null;
    let statusTimer = null;
    let isSender = false;

    const landing = document.getElementById('landing');
    const sessionArea = document.getElementById('session');
    const sessionCodeEl = document.getElementById('sessionCode');
    const codeInputs = Array.from(document.querySelectorAll('.code-digit'));
    const sendSection = document.getElementById('sendSection');
    const createBtn = document.getElementById('createBtn');
    const joinBtn = document.getElementById('joinBtn');
    const sendBtn = document.getElementById('sendBtn');
    const refreshBtn = document.getElementById('refreshBtn');
    const textInput = document.getElementById('textInput');
    const historyEl = document.getElementById('history');
    const toast = document.getElementById('toast');
    const statusEl = document.getElementById('status');

    function getSessionCode() {
      return codeInputs.map((input) => input.value).join('').toUpperCase();
    }

    function setSessionCode(code) {
      const clean = code.replace(/[^A-Z2-9]/gi, '').toUpperCase().slice(0, 4);
      codeInputs.forEach((input, index) => {
        input.value = clean[index] || '';
      });
      const next = codeInputs.find((input) => !input.value);
      if (next) {
        next.focus();
      }
    }

    function autoJoinIfComplete() {
      if (codeInputs.every((input) => input.value)) {
        joinSession(true);
      }
    }

    function showToast(message) {
      toast.textContent = message;
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 1600);
    }

    function setLoading(button, loading) {
      if (loading) {
        button.dataset.label = button.textContent;
        button.textContent = 'Carregando...';
        button.disabled = true;
      } else {
        button.textContent = button.dataset.label || button.textContent;
        button.disabled = false;
      }
    }

    async function post(action, data) {
      const form = new FormData();
      form.append('action', action);
      Object.entries(data || {}).forEach(([key, value]) => form.append(key, value));
      const response = await fetch(apiUrl, { method: 'POST', body: form });
      return response.json();
    }

    function setRole(sender) {
      isSender = sender;
      sendSection.classList.toggle('hidden', !sender);
      if (!sender) {
        textInput.value = '';
        statusEl.textContent = '';
      }
    }

    function enterSession(code) {
      sessionCode = code;
      sessionCodeEl.textContent = code;
      landing.classList.add('hidden');
      sessionArea.classList.remove('hidden');
      historyEl.innerHTML = '';
      lastMessageId = 0;
      startPolling();
    }

    function startPolling() {
      stopPolling();
      fetchUpdates();
      pollTimer = setInterval(fetchUpdates, 4000);
    }

    function stopPolling() {
      if (pollTimer) {
        clearInterval(pollTimer);
        pollTimer = null;
      }
    }

    async function fetchUpdates() {
      if (!sessionCode) {
        return;
      }
      const params = new URLSearchParams({
        action: 'fetch',
        session_code: sessionCode,
        device_key: deviceKey,
        since_id: String(lastMessageId),
      });
      const response = await fetch(`${apiUrl}?${params.toString()}`);
      const data = await response.json();
      if (!data.ok) {
        return;
      }

      if (Array.isArray(data.messages) && data.messages.length) {
        data.messages.forEach((message) => {
          const item = document.createElement('div');
          item.className = 'history-item';
          item.textContent = message.text;
          item.addEventListener('click', () => copyToClipboard(message.text));
          historyEl.prepend(item);
        });
      }

      if (typeof data.last_id === 'number') {
        lastMessageId = Math.max(lastMessageId, data.last_id);
      }
    }

    async function createSession() {
      setLoading(createBtn, true);
      try {
        const data = await post('create_session', {});
        if (!data.ok) {
          showToast('Erro ao criar sessao');
          return;
        }
        deviceKey = data.device_key;
        localStorage.setItem(storageKey, deviceKey);
        setRole(true);
        enterSession(data.session_code);
      } finally {
        setLoading(createBtn, false);
      }
    }

    async function joinSession(auto = false) {
      if (joinBtn.disabled) {
        return;
      }
      const code = getSessionCode();
      if (code.length < 4) {
        if (!auto) {
          showToast('Digite o codigo');
        }
        return;
      }
      setLoading(joinBtn, true);
      try {
        const data = await post('join_session', { session_code: code, device_key: deviceKey });
        if (!data.ok) {
          showToast(data.error || 'Codigo invalido');
          return;
        }
        deviceKey = data.device_key;
        localStorage.setItem(storageKey, deviceKey);
        setRole(false);
        enterSession(data.session_code);
      } finally {
        setLoading(joinBtn, false);
      }
    }

    async function sendMessage() {
      if (!isSender) {
        showToast('Apenas o criador envia texto');
        return;
      }
      const text = textInput.value.trim();
      if (!text) {
        showToast('Nada para enviar');
        return;
      }
      sendBtn.disabled = true;
      statusEl.textContent = 'Enviando...';
      const data = await post('send_message', {
        session_code: sessionCode,
        device_key: deviceKey,
        text,
      });
      sendBtn.disabled = false;
      statusEl.textContent = '';
      if (data.ok) {
        textInput.value = '';
        showToast('Enviado!');
        fetchUpdates();
      } else {
        showToast('Falha ao enviar');
      }
    }

    function copyToClipboard(text) {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
          showToast('Copiado!');
        }).catch(() => {
          showToast('Nao foi possivel copiar');
        });
        return;
      }
      const helper = document.createElement('textarea');
      helper.value = text;
      helper.style.position = 'fixed';
      helper.style.opacity = '0';
      document.body.appendChild(helper);
      helper.select();
      try {
        document.execCommand('copy');
        showToast('Copiado!');
      } catch (error) {
        showToast('Nao foi possivel copiar');
      }
      document.body.removeChild(helper);
    }

    createBtn.addEventListener('click', createSession);
    joinBtn.addEventListener('click', joinSession);
    if (sendBtn) {
      sendBtn.addEventListener('click', sendMessage);
    }
    if (refreshBtn) {
      refreshBtn.addEventListener('click', fetchUpdates);
    }

    if (textInput) {
      textInput.addEventListener('input', () => {
        if (!isSender) {
          return;
        }
        statusEl.textContent = 'Digitando...';
        clearTimeout(statusTimer);
        statusTimer = setTimeout(() => {
          statusEl.textContent = '';
        }, 500);
      });
    }

    codeInputs.forEach((input, index) => {
      input.addEventListener('input', () => {
        input.value = input.value.replace(/[^A-Z2-9]/gi, '').toUpperCase();
        if (input.value && index < codeInputs.length - 1) {
          codeInputs[index + 1].focus();
        }
        autoJoinIfComplete();
      });
      input.addEventListener('keydown', (event) => {
        if (event.key === 'Backspace' && !input.value && index > 0) {
          codeInputs[index - 1].focus();
        }
      });
      input.addEventListener('paste', (event) => {
        const pasted = event.clipboardData.getData('text');
        setSessionCode(pasted);
        autoJoinIfComplete();
        event.preventDefault();
      });
    });

  </script>
</body>
</html>
