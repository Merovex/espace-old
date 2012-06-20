// note:  This code is protected by U.S. Copyright Law.
// (c) Eric Hamilton, 2004
// See the Creative Commons Link for usage terms.

var quant=document.getElementById('quant');

function buildDeck() {
  window.prefixs = new Array('ab', 'ac', 'acr', 'acl', 'ad', 'adr', 'ah', 'ar', 'aw', 'ay', 'br', 'bl', 'cl', 'cr', 'ch', 'dr', 'dw', 'en', 'ey', 'in', 'im', 'iy', 'oy', 'och', 'on', 'qu', 'sl', 'sh', 'sw', 'tr', 'th', 'thr', 'un');
  window.dipthongs = new Array('ae', 'au', 'ea','ou','ei','ie','ia','ee','oo','eo','io');
  window.l337Thongs = new Array('43', '4u', '34','0u','31','13','14','33','00','30','10');
  window.consonantPairs = new Array('bb', 'bl', 'br', 'ck', 'cr', 'ch', 'dd', 'dr', 'gh', 'gr', 'gg', 'lb', 'ld', 'lk', 'lp', 'mb', 'mm', 'nc', 'nch', 'nd', 'ng', 'nn', 'nt', 'pp', 'pl', 'pr', 'rr', 'rch', 'rs', 'rsh', 'rt', 'sh', 'th', 'tt');
  window.postfixs = new Array('able', 'act', 'am', 'ams', 'ect', 'ed', 'edge', 'en', 'er', 'ful', 'ia', 'ier', 'ies', 'illy', 'im', 'ing', 'ium', 'is', 'less', 'or', 'up', 'ups', 'y', 'igle', 'ogle', 'agle');
  window.vowels = new Array('a', 'e', 'i', 'o', 'u');
  window.l33tvowels = new Array('4', '3', '1', '0')
  window.consonants = new Array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z');
  window.capConsonants = new Array('B', 'C', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'R', 'S', 'T', 'V', 'W', 'X', 'Y', 'Z');
}


function getPrefix() {
  var result;
  if ((Math.ceil(Math.random()*10))>7) {
    result = window.prefixs[Math.ceil(Math.random()*(window.prefixs.length-1))];
  } else {
    result = getConsonant(true);
  }
  return (result);
}

function getVowel() {
  var result;
  if (document.getElementById('numbers').checked) {
    if ((Math.ceil(Math.random()*10))>7) {
        result= window.l337Thongs[Math.ceil(Math.random()*(window.l337Thongs.length-1))];
      } else {
        result = window.l33tvowels[Math.ceil(Math.random()*(window.l33tvowels.length-1))];
    }
  } else {
    if ((Math.ceil(Math.random()*10))>7) {
      result = window.dipthongs[Math.ceil(Math.random()*(window.dipthongs.length-1))];
    } else {
      result = window.vowels[Math.ceil(Math.random()*(window.vowels.length-1))];
    }
  }
  return (result);
}

function getConsonant(single) {
var result;
var single = single;
  if (document.getElementById('upper').checked) {
    result = window.capConsonants[Math.ceil(Math.random()*(window.capConsonants.length-1))];
  } else {
    if ((Math.ceil(Math.random()*10))>7&&!single) {
      result = window.consonantPairs[Math.ceil(Math.random()*(window.consonantPairs.length-1))];
    } else {
      result = window.consonants[Math.ceil(Math.random()*(window.consonants.length-1))];
    }
  }
  return (result);  
}

function createWord(length) {
// start with prefix
// select either a vowel or dipthong -- include l33tvowells if 'numbers' is checked, caps if upper is checked
// select a consonant -- include l33tconsonants if 'numbers' is checked, caps if upper is checked
// select a postfix
// if word.length is greater than 'length', truncate to length.  If shorter, cat a new word
  var word;
  var numTest;
  word = getPrefix();
  word = word + getVowel();
  word = word + getConsonant();
  word = word + window.postfixs[Math.ceil(Math.random()*(window.postfixs.length-1))];
  if (word.length > length) {
    word = word.substring(0, length);
  } else if (word.length < length) {
    word = word + createWord(length - word.length);
  }
  return (word);
}

function createList() {
  var length = document.getElementById('length').value
  var result='';
  buildDeck();
  for (i=0;i<document.getElementById('quant').value;i++) {
    result = result + createWord(length) + '\n';
  }
  return(result);
}

