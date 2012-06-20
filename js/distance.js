function calc_distance() {
    var start=document.getElementById("start").value;
    var destination=document.getElementById("destination").value;
    var distance=0;
    var s_gal0=""; var sgal1=0; var sgal2=0;
    var s_reg0=0; var s_reg1=0; var s_sys0=0; var s_sys1=0; var s_ast0=0; var s_ast1=0;
    var t_gal0=""; var t_gal1=0; var t_gal2=0;
    var t_reg0=0; var t_reg1=0; var t_sys0=0; var t_sys1=0; var t_ast0=0; var t_ast1=0;
    var s_sys_x=0; var s_sys_y=0; var t_sys_x=0; var t_sys_y=0; var var_gal=0; var var_sys=0; var var_ast0=0;  var var_ast1=0;

    s_gal0=String(start.charAt(0));     s_gal1=Number(start.charAt(1)); s_gal2=Number(start.charAt(2));
    s_reg0=Number(start.charAt(4));     s_reg1=Number(start.charAt(5));
    s_sys0=Number(start.charAt(7));     s_sys1=Number(start.charAt(8));
    s_ast0=Number(start.charAt(10));    s_ast1=Number(start.charAt(11));

    t_gal0=String(destination.charAt(0));  t_gal1=Number(destination.charAt(1));  
    t_gal2=Number(destination.charAt(2));
    t_reg0=Number(destination.charAt(4));  t_reg1=Number(destination.charAt(5));
    t_sys0=Number(destination.charAt(7));  t_sys1=Number(destination.charAt(8));
    t_ast0=Number(destination.charAt(10)); t_ast1=Number(destination.charAt(11));

    s_sys_x=s_reg1*10+s_sys1; s_sys_y=s_reg0*10+s_sys0;
    t_sys_x=t_reg1*10+t_sys1; t_sys_y=t_reg0*10+t_sys0;

    var_gal=Math.abs((s_gal1-t_gal1)*19+s_gal2-t_gal2);
    
    var_sys=Math.ceil(Math.sqrt(Math.pow(t_sys_x-s_sys_x,2)+Math.pow(t_sys_y-s_sys_y,2)));
    var_ast0=Math.abs(t_ast0-s_ast0);
    var_ast1=Math.abs(t_ast1-s_ast1);

    if (var_gal) {
        if (t_gal1==s_gal1) { distance=var_gal*200; }
        if (t_gal1>s_gal1) { distance=(9-s_gal2)*200+2000+t_gal2*200; }
        if (t_gal1<s_gal1) { distance=s_gal2*200+2000+(9-t_gal2)*200; }
    }
    else {   
        if (var_sys) { distance=var_sys; } else {   if (var_ast0) { distance=var_ast0/5; } else { distance=0.1; } }
    }

    if (start==document.getElementById("destination").value) distance=0;

    document.getElementById("distance").innerHTML=distance;
    calc_duration();
}

function calc_duration() {
    var distance=0;
    var speed=0;
    var logistics=0;
    var duration=0;

    distance=Number(document.getElementById("distance").innerHTML);
    speed=Number(document.getElementById("maxspeed").value);

    if ((distance>0) && (speed>0)) { 
        duration=Math.ceil((distance/speed)*3600); 
        duration=Math.ceil(duration); 
        document.getElementById("duration").innerHTML=tempo(duration); 
    }

    if ((distance==0) || (speed==0)) { document.getElementById("duration").innerHTML=""; }
}
function tempo(s) {
    var m=0;
    var h=0;
    
    if(s>59) m=Math.floor(s/60); s=s-m*60;
    if(m>59) h=Math.floor(m/60); m=m-h*60;
    if(s<10) s="0"+s;
    if(m<10) m="0"+m;
    
    if (h>0) return h+"h "+m+"m "+s+"s";
    if (m>0) return m+"m "+s+"s";
             return s+"s";
}
