/*jslint white: true, plusplus: true*/
/* assumes: jquery > 1.11.0, jqtree >= 3.0.9, jquery-base64 */

var ga = {};
ga.tmp = {};

// extend jstree for singleselect & conditional select plugins:

(function ($, undefined) {
  "use strict";
  $.jstree.defaults.conditionalselect = function () { return true; };
  $.jstree.plugins.conditionalselect = function (options, parent) {
    this.activate_node = function (obj, e) {
      if(this.settings.conditionalselect.call(this, this.get_node(obj))) {
        parent.activate_node.call(this, obj, e);
      }
    };
  };
  $.jstree.plugins.singleselect = function (options, parent) {
    this.activate_node = function (obj, e) {
      if(this.is_leaf( obj )) {
        parent.activate_node.call(this, obj, e);
      }
    };
  };
  $.jstree.plugins.selectonlyleaf = function (options, parent) {
    this.activate_node = function (obj, e) {
      if(this.is_leaf( obj )) {
        parent.activate_node.call(this, obj, e);
      }
    };
  };
  $.jstree.plugins.singleselectpath = function (options, parent) {
    this.activate_node = function (obj, e) {
      if(!this.is_leaf( obj )) {
        parent.activate_node.call(this, obj, e);
      }
    };
  };
  $.jstree.plugins.selectnoleaf = function (options, parent) {
    this.activate_node = function (obj, e) {
      if(!this.is_leaf( obj )) {
        parent.activate_node.call(this, obj, e);
      }
    };
  };
  $.jstree.defaults.sort = function (a,b) {
      return this.get_node( a ).data.time < this.get_node( b ).data.time ? 1 : -1;
  };
})(jQuery);

RegExp.quote = function(str) {
   return str.replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
};

Object.size = function(obj) {
__~debug:pull{   console.log( "object.size called" )}
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
__~debug:pull{   console.log( "object.size size " + size )}
    return size;
};

ga.colors = function( colors ) {
    ga.colors.background = colors.background;
    ga.colors.text       = colors.text;
};

ga.plot_options = function () {
    var textcolor = "rgb( " + ga.colors.text + " )",
        retobj = {
            font : {
                color : textcolor
            },
            grid : {
                hoverable: false
            },
            xaxis : {
                color : "gray",
                lineWidth : 0.5,
                font : {
                    color : textcolor
                }
            },
            yaxis : {
                color : "gray",
                lineWidth : 0.5,
                font : {
                    color : textcolor
                }
            },
            lines: { 
                lineWidth : 1.0
            },
            zoom: {
                interactive: false
            },
            pan: {
                interactive: false
            }
        };

    return retobj;
};

ga.set = function( param, value ) {
    if ( value ) {
        ga.set.data[ param ] = value;
    }
    return ga.set.data[ param ];
}

ga.set.data = {};

ga.restricted = {};

ga.restricted.add = function( group, menu ) {
    __~debug:restricted{console.log( "ga.restricted.add( " + group + " , " + menu + " )" );}
    ga.restricted.ids[ group ] = ga.restricted.ids[ group ] || []; 
    ga.restricted.ids[ group ].push( menu );
}

ga.restricted.hideall = function() {
    var i;
    __~debug:restricted{console.log( "ga.restricted.hideall()" );}
    for ( i in ga.restricted.ids ) {
        __~debug:restricted{console.log( "ga.restricted.hideall " + ga.restricted.ids[ i ].join() );}
        $( ga.restricted.ids[ i ].join() ).hide();
    }
}

ga.restricted.show = function( restricted ) {
    var i;
    __~debug:restricted{console.log( "ga.restricted.show( " + restricted.join() + " )" );}
    for ( i in restricted ) {
        if ( ga.restricted.ids[ restricted[ i ] ] ) {
            $( ga.restricted.ids[ restricted[ i ] ].join() ).show();
        }
    }
}

ga.specproj = function( id,  value ) {
    __~debug:specproj{console.log( "ga.specproj( " + id + " , " + value + " )");}
    var t = {};
    t.id = id;
    t.value = value;
    ga.specproj.data.push( t );
}
    
ga.specproj.data = [];

ga.specproj.clear = function() {
    __~debug:specproj{console.log( "ga.specproj.clear" );}
    ga.specproj.data = [];
}

ga.specproj.gname = function() {
    var i, add, name = "";
    __~debug:specproj{console.log( "ga.specproj.name" );}
    
    for ( i in ga.specproj.data ) {
        add = ga.specproj.data[ i ].id + $( ga.specproj.data[ i ].value ).val();
        name += add.replace( /[^A-z0-9.-]+/g, "_" );
        __~debug:specproj{console.log( "ga.specproj.name() add = " + add + " name = " + name );}
    }
    return name;
}

        
