document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.toggleMenuBtn');

    buttons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            // On empêche la propag du click
            e.stopPropagation();
            // On récupère le menu déroulant qui est juste après le bouton
            const menu = this.nextElementSibling;
            
            if (menu.style.display === 'block') {
                menu.style.display = 'none';
            } else {
                menu.style.display = 'block';
            }
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.dropToggle').forEach(menu => {
            menu.style.display = 'none';
        });
    });
});
