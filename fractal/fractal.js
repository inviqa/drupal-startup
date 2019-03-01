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
 * Require the Twig adapter
 */
const twigAdapter = require('@frctl/twig')();
fractal.components.engine(twigAdapter);
fractal.components.set('ext', '.twig');

/*
 * Give your project a title.
 */
fractal.set('project.title', 'Startup');

/*
 * Tell Fractal where to look for components.
 */
fractal.components.set('path', path.join(__dirname, '../docroot/themes/custom/startup_theme/components'));

/*
 * Tell Fractal where to look for documentation pages.
 */
fractal.docs.set('path', path.join(__dirname, '../docroot/themes/custom/startup_theme/docs'));

/*
 * Tell the Fractal web preview plugin where to look for static assets.
 */
fractal.web.set('static.path', path.join(__dirname, '../docroot/themes/custom/startup_theme/assets/dist'));

/*
 * Set the path for the styleguide
 */
fractal.web.set('builder.dest', path.join(__dirname, '../docroot/themes/custom/startup_theme/styleguide'));
