import $ from '/_dev/js/libs/pquery/src/pQuery.js';

QUnit.module('pQuery/core');

QUnit.test('empty', assert => {

  const $empty = $([]); 
  assert.equal($empty.length, 0);
  
  $empty.each(() => {
	assert.fail('Must not me loaded');
  });
});

QUnit.test('populated', assert => {
	
  const $data = $([4,5,6]);
	
  assert.equal($data.length, 3);
  
  var i = 0;
  
  $data.each(() => {
	i++;
  });
  
  assert.ok(3 === i);
});
