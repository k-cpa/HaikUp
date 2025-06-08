let lastSeenIds = [];

function fetchNotifications() {
  fetch('/notifications/unread')
    .then(response => response.json())
    .then(data => {
      data.forEach(notif => {
        const link = document.createElement('a');
        if (!lastSeenIds.includes(notif.id)) {
            linkHref = `/notifications/${notif.id}/go`;
            console.log(linkHref);
          showToast(`${notif.message}`, linkHref);
          lastSeenIds.push(notif.id);
        }
      });
    })
    .catch(console.error);
}

function showToast(message, link = '/profil/notifications') {
  const toast = document.createElement('div');
  toast.className = 'notification-toast';
  toast.textContent = message;
  toast.style.cursor = 'pointer';

  toast.addEventListener('click', () => {
    window.location.href = link;
  });

  Object.assign(toast.style, {
    position: 'fixed',
    bottom: '3rem',
    right: '3rem',
    backgroundColor: '#F8C8C8',
    color: '#3B3B3B',
    padding: '3rem',
    borderRadius: '0.5rem',
    marginTop: '0.5rem',
    zIndex: 10000,
    boxShadow: '0 0 10px rgba(0,0,0,0.3)',
  });

  document.body.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, 5000);
}

// Lancer dès que la page est prête
document.addEventListener('DOMContentLoaded', fetchNotifications);

// Vérifier toutes les 10 secondes
setInterval(fetchNotifications, 10000);
