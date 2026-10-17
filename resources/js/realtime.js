let realtimeStarted = false;
let activeChat = null;
let chatPollingTimer = null;

function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function addMessageActions(container, row, message) {
  const baseUrl = new URL(container.dataset.chatUpdatesUrl, window.location.origin).pathname.replace(/\/updates$/, '');
  const messageUrl = `${baseUrl}/message/${encodeURIComponent(message.id)}`;
  const actions = document.createElement('div');
  actions.dataset.messageActions = '';
  actions.className = 'relative self-center opacity-100 transition-opacity md:opacity-0 md:group-hover:opacity-100 md:group-focus-within:opacity-100';

  const menuButton = document.createElement('button');
  menuButton.type = 'button';
  menuButton.title = 'Opsi pesan';
  menuButton.setAttribute('aria-label', 'Opsi pesan');
  menuButton.setAttribute('@click', 'menuOpen = !menuOpen');
  menuButton.className = 'flex h-7 w-7 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary';
  menuButton.innerHTML = '<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="12" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="19" cy="12" r="1.8"/></svg>';

  const menu = document.createElement('div');
  menu.setAttribute('x-show', 'menuOpen');
  menu.setAttribute('x-cloak', '');
  menu.className = 'absolute right-full top-0 z-20 mr-1 w-36 overflow-hidden rounded-lg border border-gray-200 bg-white py-1 shadow-lg';
  const editButton = document.createElement('button');
  editButton.type = 'button';
  editButton.setAttribute('@click', 'editing = true; menuOpen = false');
  editButton.className = 'flex w-full items-center gap-2 px-3 py-2 text-left text-xs text-gray-700 hover:bg-gray-50';
  editButton.innerHTML = '<svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 20h9"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg>Edit pesan';

  const deleteButton = document.createElement('button');
  deleteButton.type = 'button';
  deleteButton.setAttribute('@click', 'confirmDelete = true; menuOpen = false');
  deleteButton.className = 'flex w-full items-center gap-2 px-3 py-2 text-left text-xs text-red-600 hover:bg-red-50';
  deleteButton.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 6h18"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 6V4h8v2m3 0-1 14H6L5 6m4 4v6m6-6v6"/></svg>Hapus pesan';

  menu.append(editButton, deleteButton);
  actions.append(menuButton, menu);

  const editForm = document.createElement('form');
  editForm.dataset.messageEditor = '';
  editForm.action = messageUrl;
  editForm.method = 'POST';
  editForm.setAttribute('x-show', 'editing');
  editForm.setAttribute('x-cloak', '');
  editForm.className = 'mt-2 flex w-full items-center gap-2 rounded-xl border border-gray-200 bg-white p-2 shadow-sm';
  editForm.append(createHiddenInput('_token', csrfToken()), createHiddenInput('_method', 'PATCH'));
  const editInput = document.createElement('input');
  editInput.type = 'text';
  editInput.name = 'content';
  editInput.value = message.content;
  editInput.maxLength = 5000;
  editInput.required = true;
  editInput.setAttribute('aria-label', 'Edit pesan');
  editInput.className = 'min-w-0 flex-1 border-0 bg-transparent px-2 py-1 text-xs text-gray-800 focus:ring-0';
  const saveButton = document.createElement('button');
  saveButton.type = 'submit';
  saveButton.title = 'Simpan perubahan';
  saveButton.setAttribute('aria-label', 'Simpan perubahan');
  saveButton.className = 'flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-primary hover:bg-primary/10';
  saveButton.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/></svg>';
  const cancelButton = document.createElement('button');
  cancelButton.type = 'button';
  cancelButton.title = 'Batal edit';
  cancelButton.setAttribute('aria-label', 'Batal edit');
  cancelButton.setAttribute('@click', 'editing = false');
  cancelButton.className = 'flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100';
  cancelButton.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M18 6 6 18"/></svg>';
  editForm.append(editInput, saveButton, cancelButton);

  const deleteDialog = document.createElement('div');
  deleteDialog.dataset.messageDeleteDialog = '';
  deleteDialog.setAttribute('x-show', 'confirmDelete');
  deleteDialog.setAttribute('x-cloak', '');
  deleteDialog.setAttribute('x-transition.opacity', '');
  deleteDialog.setAttribute('@keydown.escape.window', 'confirmDelete = false');
  deleteDialog.setAttribute('role', 'dialog');
  deleteDialog.setAttribute('aria-modal', 'true');
  deleteDialog.setAttribute('aria-labelledby', `delete-message-title-${message.id}`);
  deleteDialog.className = 'fixed inset-0 z-50 flex items-center justify-center bg-gray-950/45 p-4 backdrop-blur-[2px]';
  const dialogCard = document.createElement('div');
  dialogCard.setAttribute('@click.stop', '');
  dialogCard.className = 'w-full max-w-sm rounded-2xl border border-gray-100 bg-white p-6 shadow-2xl';
  const icon = document.createElement('div');
  icon.className = 'mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600';
  icon.innerHTML = '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 6h18"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 6V4h8v2m3 0-1 14H6L5 6m4 4v6m6-6v6"/></svg>';
  const dialogTitle = document.createElement('h3');
  dialogTitle.id = `delete-message-title-${message.id}`;
  dialogTitle.className = 'text-center text-base font-semibold text-gray-900';
  dialogTitle.textContent = 'Hapus pesan ini?';
  const dialogText = document.createElement('p');
  dialogText.className = 'mt-2 text-center text-sm leading-5 text-gray-500';
  dialogText.textContent = 'Pesan akan dihapus dari percakapan.';
  const deleteForm = document.createElement('form');
  deleteForm.action = messageUrl;
  deleteForm.method = 'POST';
  deleteForm.className = 'mt-6 flex gap-3';
  deleteForm.append(createHiddenInput('_token', csrfToken()), createHiddenInput('_method', 'DELETE'));
  const cancelDeleteButton = document.createElement('button');
  cancelDeleteButton.type = 'button';
  cancelDeleteButton.setAttribute('@click', 'confirmDelete = false');
  cancelDeleteButton.className = 'flex-1 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50';
  cancelDeleteButton.textContent = 'Batal';
  const confirmDeleteButton = document.createElement('button');
  confirmDeleteButton.type = 'submit';
  confirmDeleteButton.className = 'flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2';
  confirmDeleteButton.textContent = 'Hapus';
  deleteForm.append(cancelDeleteButton, confirmDeleteButton);
  dialogCard.append(icon, dialogTitle, dialogText, deleteForm);
  deleteDialog.append(dialogCard);

  return { actions, editForm, deleteDialog };
}

function createHiddenInput(name, value) {
  const input = document.createElement('input');
  input.type = 'hidden';
  input.name = name;
  input.value = value;
  return input;
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
  const existing = container.querySelector(`[data-message-id="${message.id}"]`);
  if (existing) {
    const content = existing.querySelector('[data-message-content]');
    if (content) content.textContent = message.content;
    if (message.is_deleted) {
      existing.querySelector('[data-message-actions]')?.remove();
      existing.querySelector('[data-message-editor]')?.remove();
      existing.querySelector('[data-message-delete-dialog]')?.remove();
    }
    if (message.edited_at && !message.is_deleted && !existing.querySelector('[data-message-edited]')) {
      const marker = document.createElement('span');
      marker.dataset.messageEdited = '';
      marker.textContent = '(diedit)';
      existing.querySelector('[data-message-time]')?.append(' ', marker);
    }
    return;
  }

  container.querySelector('[data-chat-empty]')?.remove();
  const isMine = message.sender_id === container.dataset.currentUserId;
  const row = document.createElement('div');
  row.dataset.messageId = message.id;
  row.className = isMine
    ? 'group flex flex-col items-end max-w-[80%] md:max-w-[70%] ml-auto'
    : 'flex items-start gap-2.5 max-w-[80%] md:max-w-[70%]';
  if (isMine) {
    row.setAttribute('x-data', '{ menuOpen: false, editing: false, confirmDelete: false }');
    row.setAttribute('@click.outside', 'menuOpen = false');
  }

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
    const line = document.createElement('div');
    line.className = 'flex items-start justify-end gap-2.5';
    const { actions, editForm, deleteDialog } = addMessageActions(container, row, message);
    line.append(actions, contentColumn, avatar);
    row.append(line, editForm, deleteDialog);
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