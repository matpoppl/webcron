import { createServer as createServerHttps } from 'https';
import { createServer as createServerHttp } from 'http';
import { extname } from 'path';

const mime = {
  '.txt': 'text/plain',
  '.html': 'text/html',
  '.css': 'text/css',
  '.js': 'application/javascript',
  '.json': 'application/json',
  '.gif': 'image/gif',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.png': 'image/png',
  '.webp': 'image/webp',
};

const storage = {};

function requestHandler(req, res)
{
  const url = new URL(req.url, `http://${req.headers.host}`);
  const path = url.pathname;
  
  const headers = {
    'Cache-Control':  'no-store',
  };
  
  if ('/debug/storage.json' === path) {
    headers['Content-Type'] = 'application/json';
    res.writeHead(200, headers);
    res.end(JSON.stringify(storage));
    return;
  }
  
  if (! (path in storage)) {
    headers['Content-Type'] = 'text/plain';
    res.writeHead(404, headers);
    res.end('404 File not found');
    return;
  }
  
  const ext = extname(path).toLowerCase();
  if (ext in mime) {
    headers['Content-Type'] = mime[ext];
  } else {
    headers['Content-Type'] = 'application/octet-stream';
  }
  res.writeHead(200, headers);
  res.end(storage[path]);
}

function listen(opts)
{
  const options = {
    port: 8080,
    host: '127.0.0.1',
    cert: null,
    key: null,
    ...opts || {}
  };

  const server = (options.cert && options.key)
    ? createServerHttps(options, requestHandler)
    : createServerHttp(options, requestHandler);
  
  server.listen(options.port, options.host);
}

function dest(target)
{
  return async (file) => {
    file.base = '';
    file.dirname = target;
    storage[file.relative] = file.contents;
    return file;
  };
}

export default { listen, dest };
