// import './bootstrap';
import './elements/turbo-echo-stream-tag';
import './libs';
import './animations/scale';
import './animations/horizontal-scroll';
import './chart/user-chart';
import 'preline';
import Alpine from 'alpinejs';
import { HSStaticMethods } from 'preline/non-auto';

document.addEventListener('turbo:load', () => {
  HSStaticMethods.autoInit();
});

window.Alpine = Alpine;
Alpine.start();