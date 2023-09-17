import $ from '/_dev/js/libs/pquery/src/pQuery.js';

QUnit.module('pQuery/utils');

QUnit.test('isString', assert => {
  assert.equal($.isString('foo'), true);
  assert.equal($.isString(new String('bar')), true);
  assert.equal($.isString(''), true);
  assert.equal($.isString(new String('')), true);

  assert.equal($.isString([]), false);
  assert.equal($.isString({}), false);
  assert.equal($.isString(false), false);
  assert.equal($.isString(null), false);
  assert.equal($.isString(undefined), false);
  assert.equal($.isString(1.1), false);
});
