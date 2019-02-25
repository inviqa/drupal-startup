'use strict';

/*
* Require the path module
*/
const path = require('path');

/*
 * Require the Fractal module
 */
const fractal = module.exports = require('@frctl/fractal').create();

/*
 * Give your project a title.
 */
fractal.set('project.title', 'Startup');

/*
 * Tell Fractal where to look for components.
 */
fractal.components.set('path', path.join(__dirname, '../docroot/themes/custom/startup_theme/assets/src/components'));

/*
 * Tell Fractal where to look for documentation pages.
 */
fractal.docs.set('path', path.join(__dirname, '../docroot/themes/custom/startup_theme/assets/src/docs'));

/*
 * Tell the Fractal web preview plugin where to look for static assets.
 */
fractal.web.set('static.path', path.join(__dirname, '../docroot/themes/custom/startup_theme/assets/dist'));

/*
 * Set the path for the styleguide
 */
fractal.web.set('builder.dest', path.join(__dirname, '../docroot/themes/custom/startup_theme/styleguide'));
