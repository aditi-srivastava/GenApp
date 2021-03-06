/*jslint white: true, plusplus: true*/
/* assumes: jquery > 1.11.0, jqtree >= 3.0.9, jquery-base64 */

ga.data = {};
ga.data.nofcrefresh = {};

// apply the data to the screen output, return an object with job_status

ga.data.update = function( mod, data, msging_f, msg_id ) {
    var output_msgs_cleared = 0,
        appended            = 0,
        state_changed       = 0,
        do_close            = 0,
        do_close2           = 0,
        mod_out             = mod + "_output",
        hmod_out            = "#" + mod_out,
        jqmod_out           = $( hmod_out ),
        retobj              = {},
        hmod_out_msgs       = hmod_out + "_" + "msgs",
        jqhmod_out_msgs     = $( hmod_out_msgs ),
        htag,
        jqhtag,
        savekey,
        tlink,
        thtml,
        t,
        match;

__~debug:data{    console.log( "ga.data.update( " + mod + " , " + data + " )" );}
__~debug:data{    console.log( "ga.data.update() hmod_out_msgs " + hmod_out_msgs );} 
__~debug:getinput{    console.log( "ga.data.update() hmod_out_msgs " + hmod_out_msgs );} 

    if ( msging_f ) {
__~debug:data{    console.log( "ga.data.update() msging_f defined" );}
        $( "#" + mod + "_progress" ).html( "" );
        jqhmod_out_msgs.text( "" );
    }

    $.each(data, function(k, v) {
        __~debug:data{console.log( "ga.data.update() k " + k + " v " + v );}
        __~debug:getinput{if ( /^_getinput/.test( k ) ) {console.log( "ga.data.update, found _getinput" );}}

        match = jqmod_out.find( "#" + k );
        if ( match.length )
        {
            if ( !output_msgs_cleared )
            {
                jqhmod_out_msgs.text( "" );
                output_msgs_cleared = 1;
            }
            switch ( match.attr( "type" ) )
            {
            case "plot2d" : 
__~debug:plottwod{                console.log( "ga.data.update v.data is " + v.data );}
                htag = "#" + k;
                if ( v.data ) {
                    ga.value.set.plot2d( htag, v.options );
                    $.plot( match, v.data, ga.value.get.plot2d.plot_options( htag, v.options ) );
                } else {
                    $.plot( match, v,  ga.value.get.plot2d.plot_options( htag ) );
                }
                    
                savekey = mod_out + ":#" + k + ":last_value";
                $( "#global_data" ).data( savekey , v ); 
                break;
            case "atomicstructure" : 
                //                               Jmol.setDocument( 0 );
                savekey = mod_out + ":#" + k + ":last_value";
                _jmol_info[ k ].script =
                    'set background [' + ga.colors.background + ']; set zoomlarge false;set echo top center;echo loading ' + v.split( '/' ).pop() + ';refresh;load "' + v + '";';
                if ( ga.set( mod + ":jsmoladd" ) ) {
                    _jmol_info[ k ].script += ga.set( mod + ":jsmoladd" );
                }
                    __~debug:jsmol{console.log( "jsmol script is " + _jmol_info[ k ].script );}
                //                               Jmol.getApplet("jmol", _jmol_info[ k ]);
__~debug:values{        console.log( "ga.data.update() atomic structure jmol script before: " + _jmol_info[ k ].script );}
                $( "#global_data" ).data( savekey , _jmol_info[ k ].script ); 
                $("#" + k ).html(Jmol.getAppletHtml( "jmolApplet" + k, _jmol_info[ k ] ));
__~debug:values{        console.log( "ga.data.update() atomic structure jmol script after: " + _jmol_info[ k ].script );}
                break;
            case "checkbox" : 
            case "radio" : 
                match.prop( "checked", true ); 
                break;
            case "div" :  
                match.html( v );
                break;
            case "video" : 
                jqhtag = $( "#" + k );
                thtml = "<video ";
                if ( jqhtag.attr( "data-width" ) ) {
                    thtml += ' width="' +  jqhtag.attr( "data-width" ) + '"';
                }
                if ( jqhtag.attr( "data-height" ) ) {
                    thtml += ' height="' +  jqhtag.attr( "data-height" ) + '"';
                }
                thtml += ' controls>';
                thtml += '<source src="' + v +'.mp4" type="video/mp4" /><source src="' + v +'.webm" type="video/webm" />';
                thtml += '</video>';
                __~debug:video{console.log( "video: thtml " + thtml );}
                jqhtag.html( thtml );
            case "filelink" : 
                tlink = "<a href=\"" + v + "\" target=\"_blank\">" + v.split( '/' ).pop() + "</a>";
                savekey = mod_out + ":#" + k + ":last_value";
                $( "#global_data" ).data( savekey , tlink );
                $( "#" + k + "_filelink" ).html( tlink );
                break;
            case "filelinkm" : 
                savekey = mod_out + ":#" + k + ":last_value";
                tlink = "";
                $.each( v, function( k2, v2 ) {
                    tlink += "<a href=\"" + v2 + "\" target=\"_blank\">" + v2.split( '/' ).pop() + "</a> ";
                } );
                $( "#global_data" ).data( savekey , tlink );
                $( "#" + k + "_filelink" ).html( tlink );
                break;
            default :
                if ( $( "#global_data" ).data( "_append:" + mod_out + "_" + k ) )
                {
                    match.val( match.val() + "\n" + v );
                    match.height( parseFloat( match.prop( 'scrollHeight' ) + parseFloat( match.css("borderTopWidth") ) + parseFloat( match.css("borderBottomWidth") ) ) );
                } else {
                    match.val( v );
                }
                break;
            }
        } else {
            if ( msging_f ) {
                if ( k.charAt( 0 ) == "_" ) {
                    if ( !/^_fs_/.test( k ) || !ga.data.nofcrefresh[ mod ] ) {
                        if ( k == "_message" )
                        { 
                            messagebox( v );
                        }
                        if ( /^_getinput/.test( k ) )
                        { 
                            __~debug:getinput{console.log( "found " + k + " in msging" );}
                            if ( k == "_getinputerror" ) {
                                __~debug:getinput{console.log( "_getinputerror " + v );}
                            } 
                            if ( k == "_getinput" ) {
                                ga.valuen.input( mod, v );
                            }
                        }
                        if ( k == "_textarea" )
                        { 
                            __~debug:textarea{console.log( "ga.data.update() textarea msg in msging " + v );}
                            ga.data.textarea( hmod_out, v );
                        }
                        if ( k == "_status" )
                        { 
                            __~debug:job{console.log( "ga.data.update() msg status is now " + v );}
                            if ( v == "complete" ) {
                                msging_f( msg_id, 0, 0 );
                            }
                        }
                    }
                } else {
                    if ( !appended )
                    {
                        jqhmod_out_msgs.append( "<p>Unexpected results:</p>" );
                        appended = 1;
                    }
                    jqhmod_out_msgs.append( "<p>" + k + " => " + v + "</p>" );
                }
            } else {
                if ( k.charAt( 0 ) == "_" ) {
                    if ( !/^_fs_/.test( k ) || !ga.data.nofcrefresh[ mod ] ) {
                        $( "#_state" ).data( k, v );
                        state_changed = 1;
                        if ( k == "_status" )
                        { 
                            __~debug:job{                    console.log( "ga.data.update() status is now " + v );}
                            retobj.job_status = v;
                        }
                        if ( /^_getinput/.test( k ) )
                        { 
                            __~debug:getinput{console.log( "found " + k + " NOT in msging" );}
                            if ( k == "_getinputerror" ) {
                                __~debug:getinput{console.log( "_getinputerror " + v );}
                            }
                            if ( k == "_getinput" ) {
                                ga.valuen.input( mod, v );
                            }
                        }
                        if ( k == "_textarea" )
                        { 
                            __~debug:textarea{console.log( "ga.data.update() textarea msg " + v );}
                            ga.data.textarea( hmod_out, v );
                        }
                    }
                } else {
                    if ( k == "-close" )
                    {
                        do_close = 1;
                    } else {
                        if ( k == "-close2" )
                        {
                            do_close2 = 1;
                        } else {
                            if ( !appended )
                            {
                                jqhmod_out_msgs.text( "" );
                                jqhmod_out_msgs.append( "<p>Unexpected results:</p>" );
                                appended = 1;
                                output_msgs_cleared = 1;
                            }
                            jqhmod_out_msgs.append( "<p>" + k + " => " + v + "</p>" );
                        }
                    }
                }
            }
        }
    });
    ga.value.saveLastValues( mod_out );
    ga.value.saveLastValue( mod_out, hmod_out_msgs );
    $( hmod_out + '_progress' ).html( "" );
    if ( state_changed )
    {
        syncState();
    }
    if ( do_close )
    {
        closeModal();
    }
    if ( do_close2 )
    {
        closeModal2();
    }
    return retobj;
};

ga.data.textarea = function( hmod_out, v ) {
    var hmod_out_textarea   = hmod_out + "_textarea",
        jqhmod_out_textarea = $( hmod_out_textarea );

__~debug:textarea{    console.log( "ga.data.textarea( " + hmod_out + " , " + v + " )" );}
    if ( jqhmod_out_textarea.is( ":hidden" ) ) {
__~debug:textarea{    console.log( "ga.data.textarea( " + hmod_out + " , " + v + " ) show" );}
        jqhmod_out_textarea.show();
        $( hmod_out_textarea + "_label" ).show(); 
    }

    jqhmod_out_textarea.val( jqhmod_out_textarea.val() + v );
    if ( !ga.set( "textarea:rows" ) ) {
        jqhmod_out_textarea.height( parseFloat( jqhmod_out_textarea.prop( 'scrollHeight' ) ) + 
                                    parseFloat( jqhmod_out_textarea.css ( 'borderTopWidth' ) ) + 
                                    parseFloat( jqhmod_out_textarea.css ( 'borderBottomWidth' ) ) );
    }
};
    
