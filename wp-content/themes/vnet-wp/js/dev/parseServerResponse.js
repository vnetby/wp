import { dom } from "vnet-dom";
import { createAlertModal, createSuccessModal } from "./components";
import { dynamicFunctions } from "./";
import { displayModal, initCloseModal } from "./ajaxModal";



export const parseServerResponse = (res, form) => {
  res = dom.jsonParse(res);
  if (!res) return false;

  if (res.redirect) {
    window.location.href = res.redirect;
  }

  if (res.alert) {
    let modal = createAlertModal(res.alert);
    dom.addClass(dom.body, 'fancy-transparent-bg');
    $.fancybox.open(modal, {
      touch: false,
      afterShow: (instance, current) => {
        dynamicFunctions(current.src);
      },
      afterClose: () => {
        dom.removeClass(dom.body, 'fancy-transparent-bg');
      }
    });
  }

  if (res.clearInputs) {
    clearInputs(form);
  }

  if (res.success_modal) {
    displaySuccessModal(res.success_modal);
  }

  return res;
}



const clearInputs = form => {
  let inputs = dom.findAll('input, textarea, select', form);
  if (!inputs || !inputs.length) return;

  inputs.forEach(input => {
    input.value = '';
  });
}




const displaySuccessModal = msg => {
  let modal = findSuccessModal();
  addSuccessModalMsg(modal, msg);
  setTimeout(() => {
    displayModal(modal);
  }, 50);
}




const findSuccessModal = () => {
  let modal = dom.findFirst('#successModal');
  if (!modal) {
    modal = createSuccessModal();
    dom.body.appendChild(modal);
    dynamicFunctions(modal);
    initCloseModal(modal);
  }
  return modal
}




const addSuccessModalMsg = (modal, msg) => {
  dom.findFirst('.msg-container', modal).innerHTML = msg;
}