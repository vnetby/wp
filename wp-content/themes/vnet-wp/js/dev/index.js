import "../../css/dev/index.scss";

import { dom } from "vnet-dom";

// DOM FUNCTIONS


// PROJECT FUNCTIONS
import { setCompensateScrollbar } from "./setCompensateScrollbar";
import { initDynamicFormFunctions, initStaticFormFunction } from "./forms";







export const dynamicFunctions = wrap => {
  let container = dom.getContainer(wrap);
  if (!container) return;
  initDynamicFormFunctions(container);
}





const staticFunctions = () => {
  setCompensateScrollbar();
  initStaticFormFunction();
}




staticFunctions();
dynamicFunctions();