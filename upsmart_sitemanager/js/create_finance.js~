/**
  * @package UpSmart_SiteManager
  */

	window.onload = findTotalAssets();
	function findTotalAssets(){
	    var arr = document.getElementsByClassName('assets');
	    var tot=0;
	    for(var i=0;i<arr.length;i++){
		if(parseInt(arr[i].value))
		    tot += parseInt(arr[i].value);
	    }
	    document.getElementById('total_assets').value = tot;
	}
	window.onload = findTotalAssets;

	window.onload = findTotalLiabilities();
	function findTotalLiabilities(){
	    var arr = document.getElementsByClassName('liabilities');
	    var tot=0;
	    for(var i=0;i<arr.length;i++){
		if(parseInt(arr[i].value))
		    tot += parseInt(arr[i].value);
	    }
	    document.getElementById('total_liabilities').value = tot;
	}

	window.onload = findGrandTotal();
	function findGrandTotal(){
	    var ass = document.assetsandliabilitiesform.total_assets.value;
	    var liab = document.assetsandliabilitiesform.total_liabilities.value;
	    var tota=0;
	    tota = +ass + +liab;
	    document.getElementById('grand_total').value = tota;
	}
