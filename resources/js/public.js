import './bootstrap';
import Alpine from 'alpinejs';
import { promoCarousel } from './Components/publicCatalog';
import { initPayment } from './Components/payment';

window.Alpine = Alpine;

Alpine.data('promoCarousel', promoCarousel);

Alpine.start();
