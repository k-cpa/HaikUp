document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.getElementById('notification-toggle');
  const dropdown = document.querySelector('.notification-dropdown');

  toggle.addEventListener('click', (e) => {
    e.preventDefault();
    dropdown.classList.toggle('show');

    if (dropdown.classList.contains('show')) {
      fetch('/notifications/unread')
        .then(res => res.json())
        .then(data => {
          dropdown.innerHTML = '';

          if (data.length === 0) {
            dropdown.innerHTML = '<p>Aucune notification</p>';
          } else {
            data.forEach(notif => {
              const item = document.createElement('div');
              item.className = 'notification-item';
              item.textContent = notif.message;
              item.addEventListener('click', () => {
                window.location.href = `/notifications/${notif.id}/go`;
              });
              dropdown.appendChild(item);
            });
          }
        })
        .catch(() => {
          dropdown.innerHTML = '<p>Erreur de chargement</p>';
        });
    }
  });

  // Fermer en cliquant à l’extérieur
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.notification-wrapper')) {
      dropdown.classList.remove('show');
    }
  });
});
