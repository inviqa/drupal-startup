// Imports.
const gulp = require('gulp');
const fractal = require('./fractal.js');
const logger = fractal.cli.console;

// Serve dynamic site.
function fractal_start() {
  const server = fractal.web.server({
    port: 3000,
    sync: false,
  });

  // Output Error.
  server.on('error', err => logger.error(err.message));

  // Start the server.
  return server.start().then(() => {
    logger.success(`Fractal server is now running`);
  });
}

// Build static site.
function fractal_build() {
  const builder = fractal.web.builder();

  builder.on('progress', (completed, total) => logger.update(`Exported ${completed} of ${total} items`, 'info'));
  builder.on('error', err => logger.error(err.message));

  return builder.build().then(() => {
    logger.success('Fractal build completed!');
  });
}

// Fractal tasks.
gulp.task('start:fractal', fractal_start);
gulp.task('build:fractal', fractal_build);
