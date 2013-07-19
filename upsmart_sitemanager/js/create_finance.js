/**
  * @package UpSmart_SiteManager
  */

function calc() {
  var total = document.getElementById('checking').value + document.getElementById('savings').value;
  document.getElememntById('total').value = total;
}