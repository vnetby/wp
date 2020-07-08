import { dom } from "vnet-dom";



export const clickEvents = () => {
  dom.onClick(dom.body, e => {
    let path = dom.getEventPath(e);
    parseClick(path, e);
  });
}



const parseClick = (path, e) => {
  let isDropdown = false;

  path.forEach(target => {
    if (!target.tagName) return;

    if (target.classList && target.classList.contains('disabled')) {
      e.preventDefault();
    }

    if (target.classList && target.classList.contains('dismis-modals') && $ && $.fancybox) {
      $.fancybox.close();
    }

    if (target.classList && target.classList.contains('prevent-default')) {
      e.preventDefault();
    }

    if (target.classList && target.classList.contains('toggle-dropdown')) {
      e.preventDefault();
      dom.toggleClass(target.parentNode, 'active');
    }

    if (target.classList && target.classList.contains('dropdown')) {
      isDropdown = true;
    }

  });

  if (!isDropdown) {
    dom.removeClass('.dropdown.active', 'active');
  }
}







