window.Vue = require('vue');
window.axios = require('axios');
window.bus = new Vue();

require('./core.js');
//require('./pages/cms/index.js');
require('./pages/cms/partials/modal.js');
require('./pages/cms/configure/index.js');
require('./pages/cms/schedules/index.js');

