import $ from '/_dev/js/libs/pquery/src/pQuery.js';

QUnit.module('pQuery/in-browser/event');

QUnit.test('trigger', assert => {
  
  assert.expect( 2 );
  
  $('<div />').on('foo', () => {
    assert.ok( true, 'foo' );
  }).trigger('foo').trigger('foo');

});

QUnit.test('trigger.once', assert => {
  
  assert.expect( 1 );
  
  $('<div />').one('foo', () => {
    assert.ok( true, 'foo' );
  }).trigger('foo').trigger('foo');

});

QUnit.test('data', assert => {
  
  assert.expect( 1 );
  
  $('<div />').on('foo', {
	bar: 'baz'
  }, (evt) => {
    assert.equal( evt.data.bar, 'baz' );
  }).trigger('foo');

});

QUnit.test('off', assert => {
  
  assert.expect( 2 );
  
  function listener()
  {
	assert.ok( true, 'foo' );
  }
  
  $('<div />').on('foo', listener).trigger('foo').trigger('foo').off('foo', listener).trigger('foo').trigger('foo');

});
