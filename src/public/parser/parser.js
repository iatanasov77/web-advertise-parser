

var numAltFields = 2;
var numAltPictures = 4;


var curNode = null;
var currentKey = null;

/**
 *
 * TinyMCE onclick callback method
 */
function browserClicked(ed) {
	if(!currentKey)
		return;
	
	/* If is clicked to same node again , return */
	if(curNode == ed.selection.getNode())
		return;
	
	/* Remove Marker from previous selected node */
	if(curNode)
		tinyMCE.activeEditor.dom.removeClass(curNode, 'parserMarker');

	/* Add Marker to current selected */
    curNode = ed.selection.getNode();
    tinyMCE.activeEditor.dom.addClass(curNode, 'parserMarker');
    
   var path = '';
	
	//currentKey = $('select[name="project_fields"]').val();
	var iNode = curNode;
	while(iNode.nodeName != 'BODY') {
		var pathNode = iNode.nodeName.toLowerCase();
		
		if((pathNode != 'tbody')) {
			if(iNode.hasAttribute('id') && (iNode.getAttribute('id') != '')) {
	    		pathNode += '#' + iNode.getAttribute('id');
	    		path = '/' + pathNode + path;
	    		break;
			}
			
			siblingIndex = _siblingIndex(iNode);
			if(siblingIndex > 1) {
				pathNode += '[' + siblingIndex + ']';
			}
			
	    	path = '/' + pathNode + path;
		}
		
    	iNode = iNode.parentNode;
    	siblingIndex = 1;
	}
	
	path = trim(path, '., ');
	$('input[name="'+currentKey+'"]').val(path);

}

function _siblingIndex(curNode) {
	var siblingIndex = 1;
	var curSibling = curNode;
    while(curSibling = curSibling.previousSibling) {
    	if(curSibling.nodeName == curNode.nodeName) {
    		siblingIndex++;
    	}
    }
	
    return siblingIndex;
}

var trackStatusOnFocus = function(event) {
	currentKey = event.target.name;
	var labelFor = $(this).attr("id").replace(/_\d+$/, '');
	var labelText = $('label[for=' + labelFor + ']').text();

	if(labelText.length == 0) labelText = currentKey;
	$('#currentField').text(labelText);
}

function initAddDelButtons() {
	/* Init Picture Buttons  */
	$('.btnAddPicture').each(function (index) {
		//alert($(this)[0].value);

		var parts = explode('__', $(this)[0].parentNode.id);
        	var caption = parts[1];
        	var num     = $('#cloned__' + caption).children().length;
        	num = !num ? 1 : num / 4 + 1;
    		var suffix = '_' + num;
		
		if(num >= numAltPictures) {
			$(this).attr('disabled','true');
		}
		else if(num <= 0) {
			$(this).next('.btnDelPicture').attr('disabled','true');
		}

	});
	
	/* Init Picture Buttons  */
	$('.btnAdd').each(function () {
		var parts = explode('__', $(this)[0].parentNode.id);
        	var caption = parts[1];
        	var num     = $('#cloned__' + caption).children().length;
        	num = num ? num / 2 : 0;
		var suffix = '_' + num;

		if(num >= numAltFields) {
			$(this).attr('disabled','true');
		}
		else if(num <= 0) {
			$(this).next('.btnDel').attr('disabled','true');
		}
	});
	

}

$(document).ready(function () {
	
	$('.trackStatus').focus(trackStatusOnFocus);
	initAddDelButtons();

	$('.btnAddPicture').click(function(event) {
        var parts = explode('__', event.target.parentNode.id);
        var caption = parts[1];
        var num     = $('#cloned__' + caption).children().length;
        num = !num ? 1 : num / 4 + 1;
    	var suffix = '_' + num;

        var newElemXpath = $('#' + caption + '_xpath').clone()
        	.attr('id', caption + '_xpath' + suffix)
        	.attr('name', 'projectFieldsAdsPictures[' + num + '][xquery]')
        	.attr('value', '')
        	.focus(trackStatusOnFocus);
        var newElemRegex = $('#' + caption + '_regex').clone()
    		.attr('id', caption + '_regex' + suffix)
	    	.attr('name', 'projectFieldsAdsPictures[' + num + '][regex]')
	    	.attr('value', '');
        var newElemReplace = $('#' + caption + '_replace').clone()
	    	.attr('id', caption + '_replace' + suffix)
	    	.attr('name', 'projectFieldsAdsPictures[' + num + '][replace]')
	    	.attr('value', '');
        
        $('#cloned__' + caption)
		.append(newElemXpath)
        	.append(newElemRegex)
        	.append(newElemReplace)
		.append('<br />');
        
	$('.btnDelPicture').removeAttr('disabled');

        if(num >= numAltPictures)
            $('.btnAddPicture').attr('disabled','true');
    });
	
	$('.btnDelPicture').click(function(event) {
		var parts = explode('__', event.target.parentNode.id);
        	var caption = parts[1];
		var num = $('#cloned__' + caption).children().length / 4;

	     $('#' + caption + '_xpath_' + num).remove();
	     $('#' + caption + '_regex_' + num).remove();
	     $('#' + caption + '_replace_' + num).remove();
		$('#cloned__' + caption).children().last('br').remove();

	     $('.btnAddPicture').removeAttr('disabled');

	     if (num <= 1)
	         $('.btnDelPicture').attr('disabled','true');
    });
   	
	$('.btnAdd').click(function(event) {
        var parts = explode('__', event.target.parentNode.id);
        var caption = parts[1];
	var num     = $('#cloned__' + caption).children().length;
	num = num ? num / 2 + 1 : 1;
        var suffix = '_' + num;
        var newElem = $('#' + caption).clone()
                .attr('id', caption + suffix)
                .attr('name', 'projectFieldsAds[' + caption + suffix + ']')
                .attr('value', '')
                .focus(trackStatusOnFocus);

        $('#cloned__' + caption).append(newElem).append('<br />');
	$(event.target).next(":button").removeAttr('disabled');

        if(num >= numAltFields)
		$(event.target).attr('disabled', 'true');
    });
	
	$('.btnDel').click(function(event) {
		var parts = explode('__', event.target.parentNode.id);
        var caption = parts[1];
		var num = $('#cloned__' + caption).children().length;
		num = num ? num / 2 : 0;
	     $('#' + caption + '_' + num).remove();
		$('#cloned__' + caption).children().last('br').remove();

	     $(event.target).prev(":button").removeAttr('disabled');

	     if (num <= 1)
	         $(event.target).attr('disabled','true');
    });
    	
	/**
	 * OnChange Event Handler of Ads Link Field
	 */
	$('#AdsLink').change(function () {
		$('input[name="formAction"]').val("ads_url_changed");
		$('#formProject').submit();
	});
	
	/**
	 * OnClick Event Handler of Preview Button
	 */
	$('#btnPreviewProject').click(function () {
		$("#formProject").attr("action", "/index/preview");
		$('#formProject').submit();
	});
	
	/**
	 * OnClick Event Handler of Save Button
	 */
	$('#btnSaveProject').click(function () {
		$('input[name="formAction"]').val("save");
		$('#formProject').submit();
	});
	
	/**
	 * OnClick Event Handler of Browse Button
	 */
	$('#btnBrowse').click(function () {
		$('input[name="formAction"]').val("browse");
		$('#formProject').submit();
	});
	
	/**
	 * OnChange Event Handler of Category Select Box
	 */
	$('#category').change(function () {
		$('input[name="formAction"]').val("catchange");
		$('#formProject').submit();
	});
	
});

