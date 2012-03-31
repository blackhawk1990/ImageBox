//AJAXUPLOAD v 0.18
//written by:Łukasz Traczewski (c) 2011-2012
//last modification: 31-03-2012

var i = 0;

//var ms = new Date().getTime();

var last_uploaded = 0;

function upload(form_id, wrapper_id, user_id)
{
    //deleting elements(if they're exists) from last uploads
    if($('#prog-status').length > 0)
    {
        $('#prog-status').remove();
    }
    
    if($('#progressbar').length > 0)
    {
        $('#progressbar').remove();
    }
                    
    if($('#file-info').length > 0)
    {
        $('#file-info').remove();
    }
    
    var form = document.getElementById(form_id);
    
    $('#' + wrapper_id).append("<div id=\"progressbar\"></div>");
    
    $('#' + wrapper_id).append("<div id=\"prog-status\"><div id=\"percent\"></div>");
    
    $('#prog-status').append("<div id=\"up-speed\"></div>");
    
    $('#progressbar').progressbar({
        'min' : 0,
        'max' : 100,
        'value' : 0
    });
    
    var xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener("progress", updateProgress, false);
    
    xhr.open("POST", "upload_" + user_id + ".html", true);
    
    xhr.onreadystatechange = function(){
        
        if(xhr.readyState == 4)
        {
            if(xhr.status == 200)
            {
                if(form.elements['file'].files.length != 0)
                {
                    $('#up-speed').fadeOut('fast');
                    $('#' + wrapper_id).append("<div id = \"file-info\">");
                    $('#file-info').append("<div id = \"close-but\"></div>");
                    $('#file-info').append("Pomyślnie załadowano plik:<br />" + form.elements['file'].files[0].name + "<br />");
                    $('#file-info').append("Rozmiar: " + form.elements['file'].files[0].size + "b<br />");
                    $('#file-info').append("Typ: " + form.elements['file'].files[0].type + "<br />");
                    $('#' + wrapper_id).append("</div>");
                    $('#file-info').hide();
                    $('#close-but').click(function(){
                        $('#file-info').fadeOut('slow', function(){
                            $('#file-info').remove();
                        });
                        $('#progressbar').fadeOut('slow', function(){
                            $('#progressbar').remove();
                        });
                        $('#prog-status').fadeOut('slow', function(){
                            $('#prog-status').remove();
                            $('body').html(xhr.responseText);
                        });
                    });
                    $('#close-but').mouseenter(function(){
                        $(this).css({'box-shadow' : '0 0 5px #ffffff'});
                    });
                    $('#close-but').mouseleave(function(){
                        $(this).css({'box-shadow' : 'none'});
                    });
                    $('#file-info').fadeIn('slow');
                    //$('body').html(xhr.responseText);
                }
                else
                {
                    $('#prog-status').remove();
                    $('#progressbar').remove();
                    
                    if($('#file-info').length > 0)
                    {
                        $('#file-info').remove();
                    }
                    
                    $('#' + wrapper_id).append("<div id = \"file-info\">");
                    $('#file-info').append("<div id = \"close-but\"></div>");
                    $('#file-info').append("<span style=\"color:red\"><strong>Nie wybrano żadnego pliku!</strong></span><br /><br />Wybierz chociaż jeden plik");
                    $('#' + wrapper_id).append("</div>");
        
                    $('#close-but').click(function(){
                        $('#file-info').fadeOut('slow', function(){
                            $('#file-info').remove();
                            location.href = "upload_" + user_id + ".html";
                        });
                    });
                }
            }
        }
        
    };
    
    xhr.send(new FormData(form));
    
    setInterval(uploadSpeed, 1000);

}

function  updateProgress(e)
{
    if (e.lengthComputable)
    {
        var complete = (e.loaded / e.total) * 100;

        $('#progressbar').progressbar({'value' : complete});
        $('#percent').text(Math.round(complete) + " % przesłane");
        current_uploaded = e.loaded;
    }
    else
    {
        alert("Filesize error!");
    }
}

function uploadSpeed()
{
    var upload_speed = Math.round(((current_uploaded - last_uploaded) * 100 / 1024) / 100);
    last_uploaded = current_uploaded;
    $('#up-speed').text(upload_speed + " Kb/s");
}