import { dom } from "vnet-dom";



export const setCompensateScrollbar = () => {
  let style = dom.create('style');

  style.innerHTML = `
    .vnet-compensate-for-scrollbar {
      margin-right: ${dom.scrollBarWidth}px;
    }
    .vnet-compensate-for-scrollbar .compens-p-right {
      padding-right: ${dom.scrollBarWidth}px;
    }
    .compensate-for-scrollbar .is-fixed .bottom-bar-bg {
      padding-right: ${dom.scrollBarWidth}px;
    }
    .compensate-for-scrollbar .mobile-bottom-bar {
      padding-right: ${dom.scrollBarWidth}px;
    }
    body.compensate-for-scrollbar {
      margin-right: ${dom.scrollBarWidth}px;
    }
    body.compensate-for-scrollbar .mobile-menu {
      padding-right: ${dom.scrollBarWidth}px;
    }
    body.compensate-for-scrollbar .is-fixed .catalog-menu .tabs-links {
      padding-right: ${dom.scrollBarWidth}px;
    }
  `;

  dom.document.head.appendChild(style);
}