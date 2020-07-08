import { dom } from "vnet-dom";
import { React } from "vnet-dom/DOM/domReact";



const createPreloader = () => {
  let preloader = (
    <div className="ajax-preloader">
    </div>
  );
  preloader.innerHTML = createPreloadSvg();
  return preloader;
}



export const createAlertModal = html => {
  let modal = (
    <div className="alert-modal-content">
    </div>
  );
  modal.innerHTML = html;
  return modal;
}



export const createConfirmModal = html => {
  let modal = (
    <div className="alert-modal-content confirm-modal">
      <div className="content fs-24 fw-bold"></div>
      <div className="btn-row center">
        <a href="#" className="btn btn-primary fw-bold fs-12 tt-upper min-120 confirm-btn">
          <span className="text">Да</span>
        </a>
        <a href="#" className="btn btn-border-primary fw-bold fs-12 tt-upper min-120 dismiss-btn">
          <span className="text">Нет</span>
        </a>
      </div>
    </div>
  );
  dom.findFirst('.content', modal).innerHTML = html;
  return modal;
}



export const dispalyConfirmModal = ({ html, onConfirm, onDismiss, beforeClose, afterClose }) => {
  let modal = createConfirmModal(html);

  dom.findFirst('.confirm-btn', modal).addEventListener('click', e => {
    e.preventDefault();
    onConfirm && onConfirm(modal);
    $.fancybox.close();
  });

  dom.findFirst('.dismiss-btn', modal).addEventListener('click', e => {
    e.preventDefault();
    onDismiss && onDismiss(modal);
    $.fancybox.close();
  });

  dom.addClass(dom.body, 'fancy-transparent-bg');

  $.fancybox.open(modal, {
    touch: false,
    beforeClose: (instance, current, e) => {
      beforeClose && beforeClose(instance, current, e);
    },
    afterClose: (instance, current, e) => {
      dom.addClass(dom.body, 'fancy-transparent-bg');
      afterClose && afterClose(instance, current, e);
    }
  });
}



export const displayTimeoutAlert = ({ msg, btn }) => {
  if (btn.classList.contains('has-modal')) return;
  dom.addClass(btn, 'has-modal');
  let modal = (
    <div className="timeout-alert top-page-alert">
      <div className="ico-col"></div>
      <div className="content-col"></div>
    </div>
  );
  dom.findFirst('.content-col', modal).innerHTML = msg;

  let alert = createAlertSVG();
  dom.findFirst('.ico-col', modal).innerHTML = alert;
  let close = dom.create('a', { href: '#', className: 'close-alert' });
  close.innerHTML = createCloseSVG();
  modal.appendChild(close);
  initCloseTimeoutModal(modal, btn);
  appendTimoutModal(modal);
}




const appendTimoutModal = modal => {
  let container = getTimoutModalWrapper();
  container.appendChild(modal);
  setTimeout(() => {
    dom.addClass(modal, 'visible');
  }, 20);
}



const getTimoutModalWrapper = () => {
  let container = dom.findFirst('.timeout-modals-wrap');
  if (!container) {
    container = dom.create('div', 'timeout-modals-wrap');
    dom.body.appendChild(container);
  }
  return container;
}



const initCloseTimeoutModal = (modal, btn) => {
  let timeout = false;
  dom.onClick('.close-alert', e => {
    e.preventDefault();
    if (modal.classList.contains('in-close')) return;
    clearTimeout(timeout);
    closeTimeoutAlert(modal, btn);
  }, modal);
  timeout = setTimeout(() => {
    closeTimeoutAlert(modal, btn);
  }, 5000);
}




const closeTimeoutAlert = (modal, btn) => {
  dom.addClass(modal, 'in-close');
  setTimeout(() => {
    modal && modal.parentNode && modal.parentNode.removeChild(modal);
    dom.removeClass(btn, 'has-modal');
  }, 500);
}





export const createSuccessModal = () => {
  let modal = (
    <div className="modal success-modal" id="successModal">
      <div className="modal-content">
        <div className="content">
          <div className="check-ico-wrap">

          </div>
          <div className="msg-container tt-upper fw-bold fs-32">

          </div>
          <div className="btn-row">
            <a href="#" class="dismiss-modal btn btn-primary fw-bold tt-upper fs-12 min-80">
              <span className="text">OK</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  );
  dom.findFirst('.check-ico-wrap', modal).innerHTML = createCheckSVG();

  return modal;
}




const createCheckSVG = () => {
  return `
  <svg width="81" height="81" viewBox="0 0 81 81" fill="none">
    <path d="M79.5 40.5C79.5 62.0391 62.0391 79.5 40.5 79.5C18.9609 79.5 1.5 62.0391 1.5 40.5C1.5 18.9609 18.9609 1.5 40.5 1.5C62.0391 1.5 79.5 18.9609 79.5 40.5Z" stroke="#3C9730" stroke-width="3"/>
    <path d="M24.5 38.5L36.8986 50.8986" stroke="#3C9730" stroke-width="3" stroke-linecap="square"/>
    <path d="M57.5 30.5L37.1014 50.8986" stroke="#3C9730" stroke-width="3" stroke-linecap="square"/>
  </svg>
  `;
}




const createCloseSVG = () => {
  return `
  <svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M3.10949 2.04932L3.63982 1.51899L3.10949 0.988656L2.57916 1.51899L3.10949 2.04932ZM3.63982 2.57965L4.17015 2.04932L4.17015 2.04932L3.63982 2.57965ZM2.04883 3.10998L1.5185 2.57965L0.988168 3.10998L1.5185 3.64031L2.04883 3.10998ZM2.57916 3.64031L2.04883 4.17064L2.04883 4.17064L2.57916 3.64031ZM6.43886 7.50001L6.96919 8.03034L7.49952 7.50001L6.96919 6.96968L6.43886 7.50001ZM2.75629 11.1826L3.28662 11.7129L3.28662 11.7129L2.75629 11.1826ZM2.22596 11.7129L1.69563 11.1826L1.1653 11.7129L1.69563 12.2432L2.22596 11.7129ZM3.28662 12.7736L2.75629 13.3039L3.28662 13.8342L3.81695 13.3039L3.28662 12.7736ZM3.81695 12.2432L3.28662 11.7129L3.28662 11.7129L3.81695 12.2432ZM7.49952 8.56067L8.02985 8.03034L7.49952 7.50001L6.96919 8.03034L7.49952 8.56067ZM11.1821 12.2432L11.7124 11.7129L11.7124 11.7129L11.1821 12.2432ZM11.7124 12.7736L11.1821 13.3039L11.7124 13.8342L12.2428 13.3039L11.7124 12.7736ZM12.7731 11.7129L13.3034 12.2432L13.8337 11.7129L13.3034 11.1826L12.7731 11.7129ZM12.2428 11.1826L11.7124 11.7129L11.7124 11.7129L12.2428 11.1826ZM8.56018 7.50001L8.02985 6.96968L7.49952 7.50001L8.02985 8.03034L8.56018 7.50001ZM12.4199 3.64031L12.9502 4.17064L12.9502 4.17064L12.4199 3.64031ZM12.9502 3.10998L13.4805 3.64031L14.0109 3.10998L13.4805 2.57965L12.9502 3.10998ZM11.8896 2.04932L12.4199 1.51899L11.8896 0.988656L11.3592 1.51899L11.8896 2.04932ZM11.3592 2.57965L11.8896 3.10998L11.8896 3.10998L11.3592 2.57965ZM7.49952 6.43935L6.96919 6.96968L7.49952 7.50001L8.02985 6.96968L7.49952 6.43935ZM2.57916 2.57965L3.10949 3.10998L4.17015 2.04932L3.63982 1.51899L2.57916 2.57965ZM2.57916 3.64031L3.63982 2.57965L2.57916 1.51899L1.5185 2.57965L2.57916 3.64031ZM3.10949 3.10998L2.57916 2.57965L1.5185 3.64031L2.04883 4.17064L3.10949 3.10998ZM6.96919 6.96968L3.10949 3.10998L2.04883 4.17064L5.90853 8.03034L6.96919 6.96968ZM5.90853 6.96968L2.22596 10.6522L3.28662 11.7129L6.96919 8.03034L5.90853 6.96968ZM2.22596 10.6522L1.69563 11.1826L2.75629 12.2432L3.28662 11.7129L2.22596 10.6522ZM1.69563 12.2432L2.75629 13.3039L3.81695 12.2432L2.75629 11.1826L1.69563 12.2432ZM3.81695 13.3039L4.34728 12.7736L3.28662 11.7129L2.75629 12.2432L3.81695 13.3039ZM4.34728 12.7736L8.02985 9.091L6.96919 8.03034L3.28662 11.7129L4.34728 12.7736ZM11.7124 11.7129L8.02985 8.03034L6.96919 9.091L10.6518 12.7736L11.7124 11.7129ZM12.2428 12.2432L11.7124 11.7129L10.6518 12.7736L11.1821 13.3039L12.2428 12.2432ZM12.2428 11.1826L11.1821 12.2432L12.2428 13.3039L13.3034 12.2432L12.2428 11.1826ZM11.7124 11.7129L12.2428 12.2432L13.3034 11.1826L12.7731 10.6522L11.7124 11.7129ZM8.02985 8.03034L11.7124 11.7129L12.7731 10.6522L9.09051 6.96968L8.02985 8.03034ZM9.09051 8.03034L12.9502 4.17064L11.8896 3.10998L8.02985 6.96968L9.09051 8.03034ZM12.9502 4.17064L13.4805 3.64031L12.4199 2.57965L11.8896 3.10998L12.9502 4.17064ZM13.4805 2.57965L12.4199 1.51899L11.3592 2.57965L12.4199 3.64031L13.4805 2.57965ZM11.3592 1.51899L10.8289 2.04932L11.8896 3.10998L12.4199 2.57965L11.3592 1.51899ZM10.8289 2.04932L6.96919 5.90902L8.02985 6.96968L11.8896 3.10998L10.8289 2.04932ZM3.10949 3.10998L6.96919 6.96968L8.02985 5.90902L4.17015 2.04932L3.10949 3.10998Z" fill="#6E6E6E"/>
  </svg>
  `;
}




const createAlertSVG = () => {
  return `
  <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path fill-rule="evenodd" clip-rule="evenodd" d="M0 9C0 4.02564 4.02529 0 9 0C13.9744 0 18 4.02529 18 9C18 13.9744 13.9747 18 9 18C4.02564 18 0 13.9747 0 9ZM1.40625 9C1.40625 13.1971 4.80259 16.5938 9 16.5938C13.1971 16.5938 16.5938 13.1974 16.5938 9C16.5938 4.80287 13.1974 1.40625 9 1.40625C4.80287 1.40625 1.40625 4.80259 1.40625 9Z" fill="#F05A00"/>
    <path d="M9 4.53076C8.61166 4.53076 8.29688 4.84555 8.29688 5.23389V9.76177C8.29688 10.1501 8.61166 10.4649 9 10.4649C9.38834 10.4649 9.70312 10.1501 9.70312 9.76177V5.23389C9.70312 4.84555 9.38834 4.53076 9 4.53076Z" fill="#F05A00"/>
    <path d="M9 13.2244C9.52424 13.2244 9.94922 12.7994 9.94922 12.2751C9.94922 11.7509 9.52424 11.3259 9 11.3259C8.47576 11.3259 8.05078 11.7509 8.05078 12.2751C8.05078 12.7994 8.47576 13.2244 9 13.2244Z" fill="#F05A00"/>
  </svg>
  `;
}



const createPreloadSvg = () => {
  return `
  <svg xmlns="http://www.w3.org/2000/svg" 
  xmlns:xlink="http://www.w3.org/1999/xlink" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
  <circle cx="50" cy="50" r="29.3346" fill="none" stroke="#df1317" stroke-width="2">
    <animate attributeName="r" repeatCount="indefinite" dur="1s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline" begin="-0.5s"></animate>
    <animate attributeName="opacity" repeatCount="indefinite" dur="1s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline" begin="-0.5s"></animate>
  </circle>
  <circle cx="50" cy="50" r="7.66824" fill="none" stroke="#e4934b" stroke-width="2">
    <animate attributeName="r" repeatCount="indefinite" dur="1s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline"></animate>
    <animate attributeName="opacity" repeatCount="indefinite" dur="1s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline"></animate>
  </circle>
</svg>
  `;
}










export const preloader = createPreloader();