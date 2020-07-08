import { dom } from "vnet-dom";



export const typeFile = (container) => {
  let inputsWrap = dom.findAll('.file-iunput-wrap', container);
  if (!inputsWrap) return;

  inputsWrap.forEach(wrap => {
    initInput(wrap);
  });
}





const initInput = wrap => {
  let input = dom.findFirst('input', wrap);
  let label = dom.findFirst('.label', wrap);
  if (!input || !label) return;
  initDefaultLabel(label);
  initChange(input, label, wrap);
  initClearInput(input, label, wrap);
}




const initDefaultLabel = label => {
  let text = label.innerHTML;
  label.dataset.default = text;
}




const initChange = (input, label, wrap) => {
  dom.on('change', input, e => {
    if (!input.files.length) {
      setDefaultLabel(label);
      dom.removeClass(wrap, 'has-files');
    } else {
      setInputLabel(label, input.files);
      dom.addClass(wrap, 'has-files');
    }
  });
}





const setDefaultLabel = label => {
  label.innerHTML = label.dataset.default;
}




const setInputLabel = (label, files) => {
  let names = [];
  for (let i = 0; i < files.length; i++) {
    let file = files[i];
    names.push(file.name);
  }
  label.innerHTML = names.join(', ');
}





const initClearInput = (input, label, wrap) => {
  let btn = dom.create('div', 'clear-file-input');
  wrap.appendChild(btn);
  dom.onClick(btn, e => {
    e.preventDefault();
    e.stopPropagation();
    clearInput(input);
    setDefaultLabel(label);
    dom.removeClass(wrap, 'has-files');
  });
}




const clearInput = input => {
  input.value = '';
}