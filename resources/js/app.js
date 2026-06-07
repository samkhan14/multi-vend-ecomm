// resources/js/app.js

import $ from 'jquery';
window.$ = window.jQuery = $;

// 1️⃣ jQuery sabse pehle
import '../frontend/js/jquery-3.7.1.min.js'

// 2️⃣ Bootstrap (jQuery ke baad)
import '../frontend/js/boostrap.bundle.min.js'

// 3️⃣ jQuery UI
import '../frontend/js/jquery-ui.js'

// 4️⃣ Ab baaki sab plugins
import '../frontend/js/aos.js'
import '../frontend/js/count-down.js'
import '../frontend/js/counter.min.js'
import '../frontend/js/main.js'
import '../frontend/js/marque.min.js'
import '../frontend/js/phosphor-icon.js'
import '../frontend/js/select2.min.js'
import '../frontend/js/slick.min.js'
import '../frontend/js/vanilla-tilt.min.js'
import '../frontend/js/wow.min.js'

// 5️⃣ Tumhara custom Quill
import './quill-editor';