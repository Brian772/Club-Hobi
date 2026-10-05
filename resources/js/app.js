// import './bootstrap';
import './elements/turbo-echo-stream-tag';
import './realtime';
import './libs';
// import './animations/scale';
// import './animations/horizontal-scroll';
import './chart/user-chart';
import 'preline';
import Alpine from 'alpinejs';
import anchor from '@alpinejs/anchor'
import { HSStaticMethods } from 'preline/non-auto';

window.Alpine = Alpine;
Alpine.plugin(anchor);
Alpine.start();

document.addEventListener('turbo:load', () => {
  HSStaticMethods.autoInit();
});