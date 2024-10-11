function showAlert(result)
{
    var sBgGradiant = "linear-gradient(to right, #00b09b, #96c93d)";
    var sUrlDestination = "";
    var text = result;
    if(result.startsWith("ERROR:"))
    {
        sBgGradiant = "linear-gradient(to right, #FF6961, #FF033E)";
        text = result.substring(7);
    }
    if(result.startsWith("WARNING:"))
    {
        sBgGradiant = "linear-gradient(to right, #FFAA1D, #FF8000)";
        text = result.substring(9);
    }
    if(result.startsWith("SUCCESS:"))
    {
        sBgGradiant = "linear-gradient(to right, #00b09b, #96c93d)";
        text = result.substring(9);
    }
    if(result.startsWith("UPDATE:"))
    {
        sBgGradiant = "linear-gradient(to right, #8000FF, #FF00FF)";        
        text = result.substring(8);
    }
    Toastify({
        text: text,
        duration: 3000,
        destination: sUrlDestination,
        newWindow: true,
        close: true,
        gravity: "bottom", // `top` or `bottom`
        position: "right", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
          background: sBgGradiant,
        },
        onClick: function(){} // Callback after click
    }).showToast();
}

function showSweetAlert(result, sHeading="", position = "center")
{
    var icon = "info";
    var text = result;
    var title = "";
    if(result.startsWith("ERROR:"))
    {
        icon = "error";
        title = (sHeading != "") ? sHeading : "Error";
        text = result.substring(7);
    }
    if(result.startsWith("WARNING:"))
    {
        icon = "warning";
        title = (sHeading != "") ? sHeading : "Warning";
        text = result.substring(9);
    }
    if(result.startsWith("SUCCESS:"))
    {
        icon = "success";
        title = (sHeading != "") ? sHeading : "Success";
        text = result.substring(9);
    }
    if(result.startsWith("UPDATE:"))
    {
        icon = "info";
        title = (sHeading != "") ? sHeading : "Information";
        text = result.substring(8);
    }
    Swal.fire({
        icon: icon,
        title: title,
        text: text,
        showCloseButton: true,
        showCancelButton: true,
        position: position, /*top-end, bottom-end, top-left, bottom-left*/
        footer: ''
    });
}

function ajaxAndAlert(queryString, webUrl, displayType='')
{
    // showAlert("result");
    // e.preventDefault();
    var spinner = document.getElementById("spinner");
    //var queryString = "act=edit&modal=1&quick=1&id=" + id;
    //var targetUrl = window.web_root+'v4/control/interview.php';
    $.ajax(
    {
        type:'post',
        url:webUrl/*targetUrl*/,
        data:queryString,
        processData: false,  // tell jQuery to convert data in query string
        beforeSend:function()
        {
            if(spinner != null) spinner.style.display = "block";
        },
        complete:function()
        {
            if(spinner != null) spinner.style.display = "none";
        },
        success:function(result)
        {
            console.log(result);
            showAlert(result);
            //alert(result);
        }
    });
}

function ajaxAndLabel(queryString, webUrl, domLabelElementId, sendingText, isReEnable = true)
{
    // event.preventDefault();
    var spinner = document.getElementById("spinner");
    // try { var btnDom = event.currentTarget; } catch(err){}
    //var queryString = "act=edit&modal=1&quick=1&id=" + id;
    //var targetUrl = window.web_root+'v4/control/interview.php';
    $.ajax(
    {
        type:'post',
        url:webUrl/*targetUrl*/,
        data:queryString,
        processData: false,  // tell jQuery to convert data in query string
        beforeSend:function()
        {
            if(domLabelElementId != null) domLabelElementId.innerHTML = sendingText;
            try { btnDom.disabled = true; } catch(err){}
        },
        complete:function()
        {
            if (isReEnable == true)
                try { btnDom.disabled = false; } catch(err){}
        },
        success:function(result)
        {
            if(domLabelElementId != null)
            {
                if(result.startsWith("ERROR:"))
                {
                    showAlert(result);
                }
                else
                {
                    domLabelElementId.innerHTML = result;
                }
            }
            else showAlert(result);
        }
    });
}

function backgroundPostFormAndAlert(myForm, webUrl, sDomId=null)
{
    //showAlert("SUCCESS: result");
    //console.log("ok");
    // event.preventDefault();
    var spinner = document.getElementById("spinner");
    var formData = new FormData($(myForm)[0]);
    $.ajax(
    {
        type:'post',
        url:webUrl,
        data:formData,
        processData: false,  // tell jQuery not to process the data
        contentType: false,   // tell jQuery not to set contentType
        timeout: 400000,
        beforeSend:function()
        {
            if(spinner != null) spinner.style.display = "block";
        },
        complete:function()
        {
            if(spinner != null) spinner.style.display = "none";
        },
        success:function(result)
        {
            showAlert(result);
            //sDomId is used for bulkUpload Js Feature
            if (sDomId != null)
            {
                var sId = document.getElementById(sDomId);
                sId.classList.add('text-success');
                sId.classList.add('fas');
                sId.classList.add('fa-check');
            }
        }
    });
}

function backgroundPostFormAndLabel(event, myForm, webUrl, domLabelElementId, sendingText, sentText, isReEnable = true, showSpinner = false, responseHandler=false)
{
    if(event!=null) event.preventDefault();
    if (showSpinner = true) var spinner = document.getElementById("spinner");
    var spinner = document.getElementById("spinner");
    var formData = new FormData($(myForm)[0]);
    try { formData.append(document.activeElement.name, document.activeElement.value); } catch(err){}
    try { var btnDom = event.currentTarget; } catch(err){}
    $.ajax(
    {
        type:'post',
        url:webUrl,
        data:formData,
        processData: false,  // tell jQuery not to process the data
        contentType: false,   // tell jQuery not to set contentType
        timeout: 400000,
        beforeSend:function()
        {
            if(showSpinner = true && spinner != null) spinner.style.display = "block";
            if(domLabelElementId != null) domLabelElementId.innerHTML = sendingText;
            try { btnDom.disabled = true; } catch(err){}
        },
        complete:function()
        {
            if(showSpinner = true && spinner != null) spinner.style.display = "none";
            if (isReEnable == true)
                try { btnDom.disabled = false; } catch(err){}
        },
        success:function(result)
        {
            if (responseHandler == true)
            {
                domLabelElementId.innerHTML = createTabView(result);
                scrollIntoPrint();
                //console.log(result);
                return;
            }

            if(domLabelElementId != null)
            {
                if(result.startsWith("ERROR:"))
                {
                    showAlert(result);
                }
                else if(result.startsWith("SUCCESS:"))
                {
                    domLabelElementId.innerHTML = result.substring(9);
                }
                else if(result.startsWith("WARNING:"))
                {
                    domLabelElementId.innerHTML = result.substring(9);
                }
                else
                {
                    domLabelElementId.innerHTML = result;
                }
            }
            else showAlert(result);
        }
    });
}
