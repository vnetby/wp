import { dom } from "vnet-dom";






export const typePhone = container => {
  if (!dom.window.intlTelInput) return;

  let inputs = dom.findAll('input[type="tel"]', container);
  if (!inputs) return;

  inputs.forEach(input => {
    initInput(input);
  });
}





const initInput = input => {
  let iti = dom.window.intlTelInput(input, {
    initialCountry: 'by',
    autoPlaceholder: 'aggressive',
    nationalMode: false,
    preferredCountries: ['by', 'ru'],
    customPlaceholder: (placeholder, data) => {
      let iso = data.iso2;
      if (iso === 'ru') {
        return '+7';
      }
      if (iso === 'by') {
        return '+375';
      }
      return placeholder;
    },
    utilsScript: `${back_dates.SRC}/assets/libs/intl-tel-input/telUtils.js`
  });
  input.addEventListener('focus', e => {
    if (input.value) return;
    let data = iti.getSelectedCountryData();
    iti.setNumber(`+${data.dialCode}`);
  });
  input.addEventListener("countrychange", e => {
    let data = iti.getSelectedCountryData();
    iti.setNumber(`+${data.dialCode}`);
  });
}