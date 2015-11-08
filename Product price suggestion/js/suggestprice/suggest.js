document.observe('dom:loaded', function(){
var ps_sign = $('currency').value;
    function getPrice() {
		var pattern = /[.,\d]+/g;
		var originalprice = $('orig_price').value;
		var originalprice = originalprice.trim().match(pattern);
        return parseFloat(originalprice);
    }

    function getCurrentPrice() {
        var pattern = /[.,\d]+/g;
        var currentprice = $('newprice').value;
		var currentprice = currentprice.trim().match(pattern);
        return parseFloat(currentprice);
    }

    function countReduction() {
        var diff = getPrice() - getCurrentPrice();
        document.getElementById('reductprice').innerHTML = ps_sign + diff.toFixed(2);
        document.getElementById('ps_reduced').innerHTML = ps_sign + diff.toFixed(2);
    }

    $('priceminus').observe('click', function(e) {
        var cp = parseFloat(getCurrentPrice());
        var tmp = (cp - 10);
        if (tmp < 1) {
            document.getElementById('newprice').value = ps_sign + "1.00";
        } else {
            document.getElementById('newprice').value = ps_sign + tmp.toFixed(2);
        }
        countReduction();
    });

    $('priceplus').observe('click', function(e) {
        var cp = parseFloat(getCurrentPrice());
		var orig = getPrice();
        var tmp = cp + 10;
        if (tmp >= orig) {
            $('newprice').value = ps_sign + orig.toFixed(2);
        } else {
            $('newprice').value = ps_sign + tmp.toFixed(2);
        }
        countReduction();
    });

    $('newprice').observe('change', function(e) {
		var cp = parseFloat(getCurrentPrice());
		var orig = getPrice();
        if (cp < 1 || getCurrentPrice() == null) {
            $('newprice').value = ps_sign + " 1.00";
        } else if (cp >= orig) {
            $('newprice').value = ps_sign + orig.toFixed(2);
        } else
            $('newprice').value = ps_sign + cp.toFixed(2);
        countReduction();
    });

});