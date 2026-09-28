// import './bootstrap';
import './elements/turbo-echo-stream-tag';
import './libs';
import './animations/scale';
import './animations/horizontal-scroll';
import './chart/user-chart';
import 'preline';
// import Alpine from 'alpinejs';
import anchor from '@alpinejs/anchor'
import { HSStaticMethods } from 'preline/non-auto';

document.addEventListener('turbo:load', () => {
  HSStaticMethods.autoInit();
});

document.addEventListener('alpine:init', () => {
  Alpine.plugin(anchor)
});

// Alpine.plugin(anchor)
// window.Alpine = Alpine;
// Alpine.start();