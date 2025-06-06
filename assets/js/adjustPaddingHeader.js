function adjustContentPadding() {
  const headerMobile = document.querySelector('.header_mobile');
  const headerDesktop = document.querySelector('.header_desktop');
  const content = document.querySelector('.content_wrapper'); // à adapter à ta classe de contenu

  let headerHeight = 0;

  // Fonction pour vérifier si un élément est visible (display != none)
  function isVisible(elem) {
    return !!elem && window.getComputedStyle(elem).display !== 'none';
  }

  if (isVisible(headerMobile)) {
    headerHeight = headerMobile.offsetHeight;
  } else if (isVisible(headerDesktop)) {
    headerHeight = headerDesktop.offsetHeight;
  }

  if (content) {
    content.style.paddingTop = `${headerHeight}px`;
  }
}

window.addEventListener('DOMContentLoaded', adjustContentPadding);
window.addEventListener('resize', adjustContentPadding);
