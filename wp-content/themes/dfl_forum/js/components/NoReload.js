var CentralSpaceLoading=false;
var ajaxinprogress=false;
const modal = ''; 
	
// prevent all caching of ajax requests by stupid browsers like IE
jQuery.ajaxSetup({ cache: false });

// function to help determine exceptions
function basename(path) {
    return path.replace(/\\/g,'/').replace( /.*\//, '' );
}




/**
 * AJAX loading of navigation link 
 * 
 * @param {*} anchor 
 * @param {*} scrolltop 
 */

function LoadActions(pagename,id,type,ref, csrfidentifier,token)
    {
    //CentralSpaceShowLoading();
    var actionspace=jQuery('#' + id);
    url = baseurl_short+"pages/ajax/load_actions.php";
    var post_data = {
                    actiontype: type,
                    ref: ref,
                    page: pagename
                    };
                    
    post_data[csrfidentifier] = token;
    
    jQuery.ajax({
            type:'POST',
            url: url,
            data: post_data,
            async:false            
			}).done(function(response, status, xhr)
                {
                if (status=="error")
                    {				
                    actionspace.html(errorpageload  + xhr.status + " " + xhr.statusText + "<br>" + response);		
                    }
                else
                    {
                    // Load completed	
                    actionspace.html(response);
                  }
                CentralSpaceHideLoading();
                });
	return false;          
    }

/* 
* NoReload function: 
* AJAX loading of central space contents given a link 
*
*/
function NoReload(anchor, scrolltop)
	{

    alert(window.location.hostname); 
        
	anchor = this; 
	scrolltop = true; 
    // alert(typeof(anchor)); //object
    console.log(anchor); 
    alert(anchor); 

    // alert(anchor.hostname); 
	ajaxinprogress=true;
	var CentralSpace=jQuery('.bp-nouveau');
	// why not just make this 'modal=false' as a default parameter???
	
	    
	// Handle straight urls:
	if (typeof(anchor)!=='object'){ 
		var plainurl=anchor;
		var anchor = document.createElement('a');
        anchor.href=plainurl;
        alert('Line 48: ' + anchor.href); 
	}

    /* 
    * Open as standard link in new tab (no AJAX) if URL is external 
    *
    */
    if (anchor.hostname != "" && window.location.hostname != anchor.hostname)
		{
		var win=window.open(anchor.href,'_blank');win.focus();
		return false;
		}

    /* 
    * Handle link normally (no AJAX) if the CentralSpace element does not exist 
    */
	if (!CentralSpace )
		{
		location.href=anchor.href;
		return false;
		} 


    var url = anchor.href;
    alert('Line 52: ' + anchor.href);

	pagename=basename(url);
	pagename=pagename.substr(0, pagename.lastIndexOf('.'));

    alert('Line 77: ' + pagename);

	if (url.indexOf("?")!=-1)
		{
		url += '&ajax=true';
		}
	else
		{
		url += '?ajax=true';
		}
	
	// Fade out the link temporarily while loading. Helps to give the user feedback that their click is having an effect.
	if (!modal) {jQuery(anchor).fadeTo(0,0.6);}
	
	// Start the timer for the loading box.
	//CentralSpaceShowLoading(); 
	var prevtitle=document.title;

	CentralSpace.load(url, function (response, status, xhr)
		{
		if (status=="error")
			{
			//CentralSpaceHideLoading();
			CentralSpace.html(errorpageload  + xhr.status + " " + xhr.statusText + "<br>" + response);
			jQuery(anchor).fadeTo(0,1);
			}
		else
			{

			// Load completed
			//CentralSpaceHideLoading();
		
			   
			   
			}

            CentralSpace.trigger('CentralSpaceLoaded', [{url: url}]);

			// Change the browser URL and save the CentralSpace HTML state in the browser's history record.
			if(typeof(top.history.pushState)=='function' && !modal)
				{
				top.history.pushState(document.title+'&&&'+CentralSpace.html(), applicationname, anchor.href);
				}
			
			/* Scroll to top if parameter set - used when changing pages */
		    if (scrolltop==true)
				{
				if (modal)
					{
					pageScrolltop(scrolltopElementModal);
					}
				else
					{
					pageScrolltop(scrolltopElementCentral);
					}
				}
		    
			// Add accessibility enhancement:
			CentralSpace.append('<!-- Use aria-live assertive for high priority changes in the content: -->');
			CentralSpace.append('<span role="status" aria-live="assertive" class="ui-helper-hidden-accessible"></span>');

			// Add global trash bin:
			CentralSpace.append(global_trash_html);
			CentralSpace.trigger('prepareDragDrop');

			// Add Chosen dropdowns, if configured
			if (typeof chosen_config !== 'undefined' && chosen_config['#CentralSpace select']!=='undefined')
				{
				jQuery('#CentralSpace select').each(function()
					{
					ChosenDropdownInit(this, '#CentralSpace select');
					});
				}
			    
			if (typeof AdditionalJs == 'function') {   
			  AdditionalJs();  
			}

            // ReloadLinksInPlace();
			
        });
    
    ajaxinprogress=false;
	return false;
	}

