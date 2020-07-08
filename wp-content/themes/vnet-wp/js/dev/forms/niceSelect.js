import { dom } from "vnet-dom";




export const niceSelect = container => {
  if (!$ || !$.fn.niceSelect) return;

  let inputs = dom.findAll('.custom-select', container);
  if (!inputs || !inputs.length) return;
  let $inputs = $(inputs);
  $inputs.niceSelect();
  $inputs.on('change', e => {
    setLabelClass(e.target);
  });
  inputs.forEach(input => {
    setLabelClass(input);
  });
}






const setLabelClass = input => {
  let label = dom.findFirst('.custom-select .current', input.parentNode);
  if (!label) return;
  if (!input.value) {
    dom.addClass(label, 'select-label');
  } else {
    dom.removeClass(label, 'select-label');
  }
}