/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.css";
import "bootstrap/dist/css/bootstrap.min.css";

// start the Stimulus application
import "./bootstrap";
import Index from "./js/components/Index";

import { createApp } from "vue";

const app = createApp({});

app.component("index", Index);

app.mount("#app");
