let realtimeStarted = false;
let activeChat = null;
let chatPollingTimer = null;

function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

async function sendHeartbeat() {
  const url = document.body.dataset.presenceHeartbeatUrl;
  if (!url || document.hidden) return;

  try {
    await fetch(url, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
      credentials: 'same-origin',
    });
  } catch (_) {}
}

async function refreshPresence() {
  const url = document.body.dataset.presenceStatusUrl;
  const indicators = [...document.querySelectorAll('[data-presence-user]')];
  if (!url || !indicators.length || document.hidden) return;

  const query = new URLSearchParams();
  [...new Set(indicators.map((indicator) => indicator.dataset.presenceUser))]
    .forEach((id) => query.append('ids[]', id));

  try {
    const response = await fetch(`${url}?${query}`, {
      headers: { Accept: 'application/json' },
      credentials: 'same-origin',
    });
    if (!response.ok) return;

    const { users } = await response.json();
    indicators.forEach((indicator) => {
      const online = users[indicator.dataset.presenceUser]?.online ?? false;
      const dot = indicator.querySelector('[data-presence-dot]');
      const label = indicator.querySelector('[data-presence-label]');
      if (dot) {
        dot.classList.toggle('bg-emerald-500', online);
        dot.classList.toggle('bg-gray-300', !online);
      }
      if (label) label.textContent = online ? 'Online' : 'Offline';
    });
  } catch (_) {}
}

async function refreshNotifications() {
  const url = document.body.dataset.notificationUpdatesUrl;
  if (!url || document.hidden) return;

  try {
    const response = await fetch(url, {
      headers: { Accept: 'application/json' },
      credentials: 'same-origin',
    });
    if (!response.ok) return;

    const data = await response.json();
    document.querySelectorAll('[data-notification-list]').forEach((list) => {
      list.innerHTML = data.html;
    });
    document.querySelectorAll('[data-notification-count]').forEach((badge) => {
      badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
      badge.classList.toggle('hidden', data.unread_count === 0);
    });
    document.querySelectorAll('[data-notification-mark-all]').forEach((form) => {
      form.classList.toggle('hidden', data.unread_count === 0);
    });

    const pageList = document.querySelector('[data-notification-page-list]');
    const currentPage = new URLSearchParams(window.location.search).get('page') ?? '1';
    if (pageList && currentPage === '1') pageList.innerHTML = data.page_html;
  } catch (_) {}
}

function renderMessage(container, message) {
  if (container.querySelector(`[data-message-id="${message.id}"]`)) return;

  container.querySelector('[data-chat-empty]')?.remove();
  const isMine = message.sender_id === container.dataset.currentUserId;
  const row = document.createElement('div');
  row.dataset.messageId = message.id;
  row.className = isMine
    ? 'flex items-start justify-end gap-2.5 max-w-[80%] md:max-w-[70%] ml-auto'
    : 'flex items-start gap-2.5 max-w-[80%] md:max-w-[70%]';

  const contentColumn = document.createElement('div');
  contentColumn.className = isMine ? 'flex flex-col items-end' : '';
  const bubble = document.createElement('div');
  bubble.className = isMine
    ? 'bg-[#0070F3] text-white text-sm px-4 py-2.5 rounded-2xl rounded-tr-sm shadow-sm'
    : 'bg-white text-gray-900 text-sm px-4 py-2.5 rounded-2xl rounded-tl-sm shadow-sm border border-gray-100';
  bubble.textContent = message.content;

  const time = document.createElement('span');
  time.className = isMine
    ? 'text-[10px] text-gray-400 mr-2 mt-1 block'
    : 'text-[10px] text-gray-400 ml-2 mt-1 block';
  time.textContent = message.send_at
    ? new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit' }).format(new Date(message.send_at))
    : '';

  contentColumn.append(bubble, time);
  if (!isMine) {
    const avatar = document.createElement('div');
    avatar.className = 'w-8 h-8 rounded-full bg-gray-200 text-gray-700 font-semibold flex items-center justify-center text-xs shrink-0 select-none mt-0.5';
    avatar.textContent = container.dataset.otherInitial;
    row.append(avatar, contentColumn);
  } else {
    const avatar = document.createElement('div');
    avatar.className = 'w-8 h-8 rounded-full bg-gray-200 text-gray-700 font-semibold flex items-center justify-center text-xs shrink-0 select-none mt-0.5';
    avatar.textContent = container.dataset.currentInitial;
    row.append(contentColumn, avatar);
  }

  container.append(row);
}

async function refreshConversation(container) {
  if (document.hidden) return;

  const url = new URL(container.dataset.chatUpdatesUrl, window.location.origin);
  if (container.dataset.after) url.searchParams.set('after', container.dataset.after);

  try {
    const response = await fetch(url, {
      headers: { Accept: 'application/json' },
      credentials: 'same-origin',
    });
    if (!response.ok) return;

    const { messages } = await response.json();
    const wasNearBottom = container.scrollHeight - container.clientHeight - container.scrollTop < 100;
    messages.forEach((message) => {
      renderMessage(container, message);
      if (message.send_at && (!container.dataset.after || message.send_at > container.dataset.after)) {
        container.dataset.after = message.send_at;
      }
    });
    if (messages.length && wasNearBottom) container.scrollTop = container.scrollHeight;
  } catch (_) {}
}

function initializeChat() {
  const container = document.getElementById('chatMessages');
  const form = document.getElementById('chatForm');
  const input = document.getElementById('messageInput');
  if (!container || !form || !input) {
    cleanupChat();
    return;
  }
  if (activeChat?.container === container) return;
  cleanupChat();

  container.scrollTop = container.scrollHeight;
  input.focus();
  const onInput = () => input.setCustomValidity('');
  input.addEventListener('input', onInput);

  const onSubmit = async (event) => {
    event.preventDefault();
    const content = input.value.trim();
    if (!content) return;

    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
          'X-CSRF-TOKEN': csrfToken(),
          Accept: 'application/json',
        },
        credentials: 'same-origin',
      });
      if (!response.ok) throw new Error('Message could not be sent');

      const data = await response.json();
      renderMessage(container, data.chat_message);
      container.dataset.after = data.chat_message.send_at;
      container.scrollTop = container.scrollHeight;
      input.value = '';
      input.setCustomValidity('');
      input.focus();
      refreshNotifications();
    } catch (_) {
      input.setCustomValidity('Pesan belum terkirim. Periksa koneksi lalu coba lagi.');
      input.reportValidity();
    } finally {
      submitButton.disabled = false;
    }
  };
  form.addEventListener('submit', onSubmit);

  activeChat = { container, form, input, onInput, onSubmit };
  refreshConversation(container);
  chatPollingTimer = window.setInterval(() => refreshConversation(container), 2000);
}

function cleanupChat() {
  if (chatPollingTimer) window.clearInterval(chatPollingTimer);
  chatPollingTimer = null;

  if (activeChat) {
    activeChat.form.removeEventListener('submit', activeChat.onSubmit);
    activeChat.input.removeEventListener('input', activeChat.onInput);
    activeChat = null;
  }
}

function initializeRealtime() {
  if (!realtimeStarted && document.body.dataset.presenceHeartbeatUrl) {
    realtimeStarted = true;
    sendHeartbeat();
    refreshPresence();
    refreshNotifications();
    window.setInterval(sendHeartbeat, 30000);
    window.setInterval(refreshPresence, 5000);
    window.setInterval(refreshNotifications, 5000);
    document.addEventListener('visibilitychange', () => {
      if (!document.hidden) {
        sendHeartbeat();
        refreshPresence();
        refreshNotifications();
      }
    });
  }

  initializeChat();
}

document.addEventListener('DOMContentLoaded', initializeRealtime, { once: true });
document.addEventListener('turbo:load', initializeRealtime);
document.addEventListener('turbo:before-cache', cleanupChat);