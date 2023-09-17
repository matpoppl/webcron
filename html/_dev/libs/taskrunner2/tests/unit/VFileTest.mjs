import { createVFile } from './../../VFile.mjs';
import { strict as assert } from 'assert';

const file = createVFile('/test/file.js', '/test/');

assert.equal(file.path, '/test/file.js');
assert.equal(file.relative, 'file.js');
assert.equal(file.dirname, '/test');

file.dirname = '/specs';
assert.equal(file.path, '/specs/file.js');

assert.equal(file.basename, 'file.js');
file.basename = 'file.txt';
assert.equal(file.path, '/specs/file.txt');

assert.equal(file.stem, 'file');
file.stem = 'foo';
assert.equal(file.path, '/specs/foo.txt');

assert.equal(file.extname, '.txt');
file.extname = '.js';
assert.equal(file.path, '/specs/foo.js');
