import lr from 'tiny-lr';

let server = null;

function livereload()
{
  return (file, cb) => {
    if (server) {
      server.changed({ body: { files: [ file.path ] } });
    }
    cb(null, file);
  };
}

livereload.listen = function(opts)
{
  const options = {
    host: '127.0.0.1',
    port: 35729,
    ...opts || {}
  };
  
  server = new lr.Server(options);
  server.listen(options.port, options.host);
}

export default livereload;
