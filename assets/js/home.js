/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import 'jquery';
import 'bootstrap';
const $ = require('jquery');
import '../css/home.scss';

// carousel interval time in micro seconds
$('.carousel').carousel({
    interval: 30000
});