import { dom } from "vnet-dom";
import { validateInput } from "vnet-dom/DOM/domFormValidate";
import { recaptcha } from "./recaptcha";














export const addInputError = (input, msg) => {
  let help = getCreateHelp(input);
  help.innerHTML = msg;
  dom.removeClass(input, 'is-valid');
  dom.addClass(input, 'has-error');
  input.dataset.errorMsg = msg;
  dom.dispatch(input, 'validate-add-error');
}






export const removeInputError = input => {
  removeCompareError(input);
  removeInputErrorHandler(input);
}








const removeCompareError = input => {
  let compare = input.dataset.compare;
  if (!compare) return;
  compare = dom.findFirst(compare);
  if (!compare) return;
  removeInputErrorHandler(compare);
}







const removeInputErrorHandler = input => {
  dom.removeClass(input, 'has-error');
  dom.addClass(input, 'is-valid');
  let help = dom.findFirst('.input-help', input.parentNode);
  if (help) help.innerHTML = '';
  input.removeAttribute('data-error-msg');
  dom.dispatch(input, 'validate-rm-error');
}






export const getCreateHelp = input => {
  let help = dom.findFirst('.input-help', input.parentNode);
  if (!help) {
    help = dom.create('span', { className: 'input-help' });
    input.parentNode.appendChild(help);
  }
  return help;
}