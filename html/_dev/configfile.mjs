import { env } from 'process';
import { join } from 'path';
import gulp from './libs/taskrunner2/index.mjs';
import sass from 'sass';
import postcss from 'postcss';
import postcssrc from 'postcss-load-config';
import { rollup } from 'rollup';
import { transform } from '@babel/core';
import livereload from './plugins/livereload.mjs';
import proxy from './plugins/server.mjs';
import { readFileSync as fread } from 'fs';

function devSCSS()
{
  return gulp.src('scss/*.scss')
  .pipe((file, cb) => {

    if (file.isNull()) {
      return cb(null, file);
    }
    
    if (file.isStream()) {
      return cb(new Error('STREAMS are unsupported'), file);
    }

    try {
      sass.render({
        includePaths: ['scss'],
        file: file.path,
      }, function(err, result) {
        
        if (err) return cb(err, file);
        
        file.extname = 'css';
        file.contents = result.css;
        
        cb(null, file);
      });
    } catch (err) {
      cb(err, file);
    }
  })
  .pipe((file, cb) => {
    postcssrc().then(({ plugins, options }) => {
      // set filename
      options.from = file.path;
      postcss(plugins).process(file.contents, options).then(result => {
        file.contents = result.css;
        cb(null, file);
      })
    });
  })
  .pipe(proxy.dest('/static/css'))
  .pipe(livereload());
}

function devJS()
{
  return gulp.src(['js/*.js', 'js/libs/pquery/src/*.js'])
  .pipe(async (file) => {
    const bundle = await rollup({
      //external: ['jquery'], // import $ from 'jquery';
      input: file.path,
    });
    
    const { output } = await bundle.generate({
      format: 'iife',
      name: file.stem.replace(/\W+/g, '_'),
      //globals: {jquery: '$'}, // import $ from 'jquery';
    });
    
    for (const chunkOrAsset of output) {
      file.basename = chunkOrAsset.fileName;
      if (chunkOrAsset.type === 'asset') {
        file.contents = chunkOrAsset.source;
      } else {
        file.contents = chunkOrAsset.code;
      }
    }
    
    return file;
  })
  .pipe((file, cb) => {
    transform(file.contents, {
      presets: ['@babel/preset-env'],
    }, function(err, result) {
      if (err) {
        return cb(err, null);
      }
      file.contents = result.code;
      cb(null, file);
    });
  })
  .pipe(proxy.dest('/static/js'))
  .pipe(livereload());
}

function livereloadTask(cb)
{
  livereload.listen({
    port: 35729,
    host: 'pop-pc.lan',
    cert: fread(join(env.PATH_CERTS, 'localhost-cert.pem')),
    key: fread(join(env.PATH_CERTS, 'localhost-key.pem')),
  });

  proxy.listen({
    port: 8080,
    host: 'pop-pc.lan',
    cert: fread(join(env.PATH_CERTS, 'localhost-cert.pem')),
    key: fread(join(env.PATH_CERTS, 'localhost-key.pem')),
  });
  
  cb();
}

function watch(cb)
{
  gulp.watch('js/**/*.js', gulp.series(devJS));
  gulp.watch('scss/**/*.scss', gulp.series(devSCSS));
  
  cb();
}

const dev = gulp.parallel(devSCSS, devJS);

export default async function() {
  
  const def = gulp.series(dev, livereloadTask, watch);

  await def();
};
