<?php

namespace app\index\controller;

use think\Controller;
use app\index\model\Workorder as ModelWorkorder;
use app\index\validate\WorkOrderValidate as Validate;

class Image extends Controller
{
    public function index()
    {
      $this->zzy();
      // var_dump(123);
    }

    public function zzy()
    {
      $f = "cachefile.html"; //保存的文件名
$cache = '
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>现场维护工作单</title>
<style type="text/css">
table{
    table-layout:fixed;
}
td{
    word-wrap:break-word;
}
.biaoti {
	font-size: 28px;
	font-weight: bold;
	line-height:54px;
	height:54px;
	font-family: "宋体";
	text-align: center;
	width: 780px;
	margin: 0px;
}
	 
body,td,th {
	font-size: 12px;
}
.cuti {
	font-size: 15px;
}
/*table{border:1px solid #000;border-width:1px 0 0 1px;margin:2px 0 2px 0;text-align:center;border-collapse:collapse;}
td,th{border:1px solid #000;border-width:0 1px 1px 0;margin:2px 0 2px 0;text-align:left;}
th{text-align:center;font-weight:600;font-size:12px;background-color:#F4F4F4;}*/
#rt1{
	border-left:1px solid #c0c0c0;
	border-top:1px solid #c0c0c0;
	border-right:1px solid #000000;
	border-bottom:1px solid #c0c0c0;
	border-collapse:collapse;
	}
#rt2{
	border-left:1px solid #c0c0c0;
	border-top:1px solid #c0c0c0;
	border-right:1px solid #000000;
	border-bottom:1px solid #c0c0c0;
	border-collapse:collapse;
	}
#rt3{
	border-left:1px solid #c0c0c0;
	border-top:1px solid #c0c0c0;
	border-right:1px solid #000000;
	border-bottom:1px solid #c0c0c0;
	border-collapse:collapse;
	}			
.sty1{
border-left:1px solid #000000;
border-top:1px solid #000000;
border-right:1px solid #c0c0c0;
border-bottom:1px solid #c0c0c0;
border-collapse:collapse;
}
.sty2{
border-left:1px solid #000000;
border-top:1px solid #000000;
border-right:1px solid #000000;
border-bottom:1px solid #c0c0c0;
border-collapse:collapse;
}
.sty3{
	border-left:1px solid #000000;
	border-top:1px solid #000000;
	border-right:1px solid #000000;
	border-bottom:1px solid #000000;
	border-collapse:collapse;
	font-size: 12px;
}
.sty4{
border-left:1px solid #000000;
border-top:1px solid #c0c0c0;
border-right:1px solid #000000;
border-bottom:1px solid #c0c0c0;
border-collapse:collapse;
}
.sty5{
border-left:1px solid #000000;
border-top:1px solid #000000;
border-right:1px solid #000000;
border-bottom:1px solid #c0c0c0;
border-collapse:collapse;
}
.sty6{
border-left:1px solid #000000;
border-top:1px solid #c0c0c0;
border-right:1px solid #000000;
border-bottom:1px solid #000000;
border-collapse:collapse;
}
.sty7{
border-left:1px solid #000000;
border-top:1px solid #000000;
border-right:1px solid #c0c0c0;
border-bottom:1px solid #c0c0c0;
border-collapse:collapse;
}
.sty8{
border-left:1px solid #c0c0c0;
border-top:1px solid #000000;
border-right:1px solid #c0c0c0;
border-bottom:1px solid #c0c0c0;
border-collapse:collapse;
}
.sty9{
border-left:1px solid #c0c0c0;
border-top:1px solid #000000;
border-right:1px solid #000000;
border-bottom:1px solid #c0c0c0;
border-collapse:collapse;
}
.sty10{
border-left:1px solid #000000;
border-top:1px solid #c0c0c0;
border-right:1px solid #000000;
border-bottom:1px solid #000000;
border-collapse:collapse;
}
.sty11{
border-left:1px solid #000000;
border-top:1px solid #000000;
border-right:1px solid #c0c0c0;
border-bottom:1px solid #000000;
border-collapse:collapse;
}
.sty12{
border-left:1px solid #c0c0c0;
border-top:1px solid #000000;
border-right:1px solid #c0c0c0;
border-bottom:1px solid #000000;
border-collapse:collapse;
}
.sty13{
border-left:1px solid #c0c0c0;
border-top:1px solid #000000;
border-right:1px solid #000000;
border-bottom:1px solid #000000;
border-collapse:collapse;
}
.sty14{
border-left:1px solid #000000;
border-top:1px solid #c0c0c0;
border-right:1px solid #c0c0c0;
border-bottom:1px solid #c0c0c0;
border-collapse:collapse;
}
.sty15{
border-left:1px solid #000000;
border-top:1px solid #c0c0c0;
border-right:1px solid #c0c0c0;
border-bottom:1px solid #000000;
border-collapse:collapse;
}
.sty16{
border-left:1px solid #c0c0c0;
border-top:1px solid #c0c0c0;
border-right:1px solid #c0c0c0;
border-bottom:1px solid #c0c0c0;
border-collapse:collapse;
}
.sty17{
border-left:1px solid #c0c0c0;
border-top:1px solid #c0c0c0;
border-right:1px solid #000000;
border-bottom:1px solid #c0c0c0;
border-collapse:collapse;
}
.sty18{
border-left:1px solid #c0c0c0;
border-top:1px solid #c0c0c0;
border-right:1px solid #000000;
border-bottom:1px solid #000000;
border-collapse:collapse;
}
.sty19{
border-left:1px solid #c0c0c0;
border-top:1px solid #c0c0c0;
border-right:1px solid #c0c0c0;
border-bottom:1px solid #000000;
border-collapse:collapse;
}
.zuobiaoti {
	font-weight: bold;
	font-size: 16px;
	text-align:center;
	border-left:1px solid #000000;
	border-top:1px solid #000000;
	border-right:1px solid #000000;
	border-bottom:1px solid #000000;
	border-collapse:collapse;
}

.cuti {
	font-weight: bold;
	text-align: left;
	/*display:block;
	margin-top:2px;*/
}
.cuti1 {
	text-align: left;
	font-size:12px;
}
.cuti2 {
	text-align: left;
	font-size:12px;
	display:block;
	margin-top:6px;
}
.qiangoudan {
	text-align: center;
	font-size: 36px;
}
.youxia {
	font-weight: normal;
}
</style>
<script type="text/javascript" language="javascript">
function printpreview(){
// 打印页面预览
WebBrowser1.ExecWB(7,1);
}

function printme()
{
document.body.innerHTML=document.getElementById("div1").innerHTML;
window.print();
}

var acq_id = "";

function request(paras){    
        var url = location.href;     
        var paraString = url.substring(url.indexOf("?")+1,url.length).split("&");     
        var paraObj = {}     
        for (i=0; j=paraString[i]; i++){     
            paraObj[j.substring(0,j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=")+1,j.length);     
        }     
        var returnValue = paraObj[paras.toLowerCase()];     
        if(typeof(returnValue)=="undefined"){     
            return "";     
        }else{     
            return returnValue;    
        }  
    }
    
    
    
    var work_order_no = request("work_order_no");
    
    
    
     //领取工作单信息
    //查询虚拟终端信息表

    function BindData() {
        var temp="";
        var temp1="";
        var data = {};
        data.fc = "0024009R";
        data.data = {};
        data.data.where = {};
        data.data.where.work_order_no = work_order_no;               
        var params = $.toJSON(data);
        $.ajax({
            type: "post",
            dataType: "json",
            contentType: "application/json",
            url: "/term.action",
            data: params,
            success: function(data) {
                if (data.success == "00") {
					acq_id = data.data.rows[0]["acq_inst_id"];
					if(data.data.rows[0]["acq_inst_id"] == "J4385500" && data.data.rows[0]["acq_inst_id2"] == "J000026C"){
						$("#xcwhgzd").html("浏阳农商银行现场维护工作单");
					}
                    $.each(data.data.rows, function(i, n) {
                       var tno = data.data.rows[0]["term_no"];
                    	if(tno!=""&&tno!=undefined){
                       Parameter(tno);
                     }
                       for(key in data.data.rows[0]){
							var _key="#"+key;
							if(key=="install_date"){
								$(_key).html("    装机日期："+data.data.rows[0]["install_date"])
							}else if(key=="acq_inst_name2"){
								$(_key).html("收单机构："+data.data.rows[0][key]);
							}else{
								$(_key).html(data.data.rows[0][key]);
							}
                      }
//$("#work_name").html(data.data.rows[0][work_name])
                       	$("#mcht_id").html("档案编号："+data.data.rows[0].mcht_id+"");
                       	$("#work_order_no").html("工作单号：");
                       	//设备信息
						

                         //解析打印纸类型
                         data.data.rows[0].paper_type=data.data.rows[0].paper_type.trim();
                         if(data.data.rows[0].paper_type=="0"){
                         	$("#pt_0").attr("checked","true");
                         }
                 
                         if(data.data.rows[0].paper_type=="1"){
                         	$("#pt_1").attr("checked","true");
                         	}
                         if(data.data.rows[0].paper_type=="2"||data.data.rows[0].paper_type=="3"){
                         	$("#pt_2").attr("checked","true");
                         	}
                         if(data.data.rows[0].paper_type=="4"||data.data.rows[0].paper_type=="5"){
                         	$("#pt_4").attr("checked","true");
                         	}
                         //解析机具类型
						 data.data.rows[0].device_type=data.data.rows[0].device_type.trim();
						 for(var ji = 0 ;ji<9;ji++){
							 if(data.data.rows[0].device_type==ji){
								 $("#jjlx_"+ji).attr("checked","true");
							  }
						 }
                        var work_flag=data.data.rows[0].work_flag.trim();

                        for(var i=0;i<work_flag.length;i++){
                        	//alert(work_flag.charAt(i));
														if(work_flag.charAt(i)=="1"){

														   //$("#work_flag"+(i+1)).attr("checked","checked");//打勾
														   $("#work_flag"+(i+1)).attr("checked","true"); 														   
														   
														}
													} 
													/*var termno = data.data.rows[0].term_no;
													termno = termno.trim();
													if(termno!=null){
														deviceinfo(termno);
														} 	*/	 
                    });
                    $("input[name=btype]").click(function(){
          if ($(this).attr("id") == "datamatrix") showConfig2D(); else showConfig1D();
        });
        $("input[name=renderer]").click(function(){
          if ($(this).attr("id") == "canvas") $("#miscCanvas").show(); else $("#miscCanvas").hide();
        });
        var work_order_no=data.data.rows[0].work_order_no.replace(/\s/g,"");
        var term_no=data.data.rows[0].term_no.replace(/\s/g,"");
        generateBarcode(work_order_no+"_"+term_no);
                    //alert(temp);
                   // temp = temp.substring(0,temp.lastIndexOf(","));
    		          //  temp1 = temp1.substring(0,temp1.lastIndexOf(","));
                  //  $("#mcht_no").html(temp);
                   // $("#terminal_no").html(temp1);
                    if(data.data.rows[0]["manage_ins_name"]){
                       	$("#manage_ins_name").html("二级行:"+data.data.rows[0]["manage_ins_name"]);
                    }
					$("#expand_manager_id").html(data.data.rows[0]["expand_manager_id"]+"&nbsp;"+data.data.rows[0]["expand_manager_telno"]);
                       	$("#sales_manager_id").html(data.data.rows[0]["sales_manager_id"]+"&nbsp;"+data.data.rows[0]["sales_manager_telno"]);
					if(data.data.rows[0]["acq_inst_id"]=="03095510"){
                       	$("#mcht_name").html(data.data.rows[0]["mcht_name3"]);
                    }
                }
                else {
                    cwts(data.msg);
                  }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                //alert("error:" + errorThrown.message);
            }
        });
    }
    
    //查询业务参数-分页
   /* function Parameter() {     
        var data = {};
        data.fc = "0024027R";
        data.data = {};
        data.data.where = {};
        data.data.where.work_order_no = work_order_no;               
        var params = $.toJSON(data);
        $.ajax({
            type: "post",
            dataType: "json",
            contentType: "application/json",
            url: "/shop.action",
            data: params,
            success: function(data) {
                if (data.success == "00") {
                    $.each(data.data.rows, function(i, n) {
                       if(n.parameter_type==0){
                       	$("#checkbox22").attr("checked","true");
                       	}
                       else if(n.parameter_type==1){
                       	$("#checkbox23").attr("checked","true");
                       	}                       
                       else if(n.parameter_type==5){
                       	$("#checkbox25").attr("checked","true");
                       	}
                       else {
                       	$("#checkbox26").attr("checked","true");
                       	}
                     
                       	
                    });

                   
                }
                else {
                    cwts(data.msg);
                  }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                //alert("error:" + errorThrown.message);
            }
        });
    }*/
    
   //查询业务参数-分页
    function Parameter(termno) {     
        var data = {};
        data.fc = "0016017R";
        data.data = {};
        data.data.where = {};
        data.data.where.term_no = termno;               
        var params = $.toJSON(data);
        $.ajax({
            type: "post",
            dataType: "json",
            contentType: "application/json",
            url: "/shop.action",
            data: params,
            success: function(data) {
                if (data.success == "00") {
                    $.each(data.data.rows, function(i, n) {
                    	//$("#checkbox_"+data.data.rows[i].parameter_type).attr("checked","true");  
                    	var flag = (data.data.rows[i]["paramter_name"]).substring(0,1);
                    	$("#ywlx_"+flag).attr("checked","true"); 
						if(flag == 9){
							$("#ywlx_9").attr("checked","true");
						}
                    	if(flag==0||flag==1){
                    		  $("#mcht_no").html(data.data.rows[i]["mcht_no"]);
                    		  $("#terminal_no").html(data.data.rows[i]["terminal_no"]);
                    		  $("#settle_name").html(data.data.rows[i]["settle_name"]);
                    		  $("#settle_pan").html(data.data.rows[i]["settle_pan"]);
                    		  $("#bill_id").html(data.data.rows[i]["bill_id"]);
							   if(acq_id!="03095510"){
                    		  		$("#mcht_name").html(data.data.rows[i]["mcht_name"]);
							   }
                    	}else if(flag==2){
                    		  $("#psam_no").html(data.data.rows[i]["psam_no"]);
                    		  $("#bind_telno").html(data.data.rows[i]["bind_telno"]);
                    		  if(acq_id!="03095510"&&($("#mcht_name").html()=="")){
                    		  	$("#mcht_name").html(data.data.rows[i]["mcht_name"]);
                    		  }
                    	}else if(flag==3||flag==4){
                    		  $("#pan").html(data.data.rows[i]["settle_pan"]);
                    		  $("#fee").html(data.data.rows[i]["bill_id"]);
                    	}else if(flag==7){
							if(n.busi_type){
								n.busi_type = n.busi_type.trim();
								$("#"+n.busi_type).attr("checked","true");
							}
						}  
						                 	
                    });

                   $("#settle_bank_name").html(data.data.rows[0]["settle_bank_name"]); 
                }
                else if(data.msg!=""){
                    cwts(data.msg);
                  }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                //alert("error:" + errorThrown.message);
            }
        });
    }
     $(function() {
        BindData();
    });

//查询设备信息
/*function deviceinfo(termno){
	   		var data = {};
        data.fc = "0016012R";//查询正式表
        data.data = {};
        data.data.where = {};
        data.data.where.term_no = termno;
        var params = $.toJSON(data);
        $.ajax({
            type: "post",
            dataType: "json",
            contentType: "application/json",
            url: "/term.action",
            data: params,
            success: function(data) {
                if (data.success = "00") {
                	if(data.data.rows.length>0){
                	for(var i=0; i < data.data.rows.length;i++){
                		 for(key in data.data.rows[i]){
                		 	//alert(key);
														if(key == "device_flag"){
															 var values = data.data.rows[i][key];
														   values = values.replace(/^\s+|\s+$/g,""); 
														   if(values=="0"){
														   	   $("#devicetype").html(data.data.rows[i]["device_type"]);
														   	  $("#device_no").html(data.data.rows[i]["device_no"]);
														   	}else if(values=="1"){
														   		$("#devicetype1").html(data.data.rows[i]["device_type"]);
														   	  $("#device_no1").html(data.data.rows[i]["device_no"]);
														   		}
															}
														}
									          }
									        }
												}else if(data.msg!=null){
									  	cwts(data.msg);
									  }
									},
			              error: function(jqXHR, textStatus, errorThrown) {
                        if(jqXHR.status!=null){
				                if(jqXHR.status!=200){
									       alert("错误代码"+jqXHR.status+":" +jqXHR.statusText);
										     }   
										  }
            }
			  });
  		}*/
</script>
<SCRIPT>
String.prototype.trim=function(){
    return(this.split(/(^\s+|\s+$)/g)[0]);
}

</SCRIPT>
<script type="text/javascript">   
      function generateBarcode(value){
      	/*var _gzdh=$("#work_order_no").text();
      	 var str = new Array(); //定义一数组
			 		str = _gzdh.split("：");
        var value = str[1].replace(/\s/g,"");*/
        var btype = $("input[name=btype]:checked").val();
        var renderer = $("input[name=renderer]:checked").val();
        
		var quietZone = false;
        if ($("#quietzone").is(":checked") || $("#quietzone").attr("checked")){
          quietZone = true;
        }
		
        var settings = {
          output:renderer,
          bgColor: $("#bgColor").val(),
          color: $("#color").val(),
          barWidth: $("#barWidth").val(),
          barHeight: $("#barHeight").val(),
          moduleSize: $("#moduleSize").val(),
          posX: $("#posX").val(),
          posY: $("#posY").val(),
          addQuietZone: $("#quietZoneSize").val()
        };
        if ($("#rectangular").is(":checked") || $("#rectangular").attr("checked")){
          value = {code:value, rect: true};
        }
        if (renderer == "canvas"){
          clearCanvas();
          $("#barcodeTarget").hide();
          $("#canvasTarget").show().barcode(value, btype, settings);
        } else {
          $("#canvasTarget").hide();
          $("#barcodeTarget").html("").show().barcode(value, btype, settings);
        }
      }
          
      function showConfig1D(){
        $(".config .barcode1D").show();
        $(".config .barcode2D").hide();
      }
      
      function showConfig2D(){
        $(".config .barcode1D").hide();
        $(".config .barcode2D").show();
      }
      
      function clearCanvas(){
        var canvas = $("#canvasTarget").get(0);
        var ctx = canvas.getContext("2d");
        ctx.lineWidth = 1;
        ctx.lineCap = "butt";
        ctx.fillStyle = "#FFFFFF";
        ctx.strokeStyle  = "#000000";
        ctx.clearRect (0, 0, canvas.width, canvas.height);
        ctx.strokeRect (0, 0, canvas.width, canvas.height);
      }
      
     /* $(function(){
        $("input[name=btype]").click(function(){
          if ($(this).attr("id") == "datamatrix") showConfig2D(); else showConfig1D();
        });
        $("input[name=renderer]").click(function(){
          if ($(this).attr("id") == "canvas") $("#miscCanvas").show(); else $("#miscCanvas").hide();
        });
        generateBarcode();
      });*/
  
    </script>
</head>

<body>
<div style="margin-left:20%">
<input type="button" class="ice_10" id="sub1"  value="打印" onclick="javascriptr:printme()"/> 
</div>
<div id="div1">
	    
    <div class="biaoti"> 
        <span id="xcwhgzd" style="padding-left:115px;" >现场维护工作单</span>
        <img src="../images/kylogo1.jpg" style="width:181px;height:27px; vertical-align:top; margin-left:10px;" />
    </div>
    
    <div class="config" style="display:none;">
        <div class="barcode1D">
            bar width: <input type="text" id="barWidth" value="1" size="3"><br />
            bar height: <input type="text" id="barHeight" value="28" size="3"><br />
        </div>
        <div class="title" >Type</div>          
        <input type="radio" name="btype" id="code128" value="code128" checked ><label for="code128">code 128</label><br />          
    </div>
    <div id="barcodeTarget" class="barcodeTarget" style="padding:0px;0px;10px;margin-right:20px;width:131px;"></div>
    <canvas id="canvasTarget" width="150" height="100"></canvas>        
    <div style="margin-top:-10px;">
        <span id="work_order_no" >工作单号：</span>
        <span style="margin-left:271px" id="plan_date" ></span>     
        <span style="margin-left:147px" id="mcht_id"></span>
        <!--span style="margin-left:20px" id="manage_ins_name"></span-->
    </div>
<table width="785" border="1" cellspacing="0" style="border-collapse:collapse">
   <tr>
    <td width="41" rowspan="16" class="zuobiaoti"><strong>基本信息</strong></td>
    <td width="82" height="19" align="center" class="sty1">签发人</td>
    <td height="19" colspan="3" class="sty8" id="sender_id">&nbsp;</td>
    <td width="74" height="19" align="center" class="sty8">终端类型</td>
    <td height="19" colspan="3" class="sty9" id="term_type">&nbsp;</td>
  </tr>
   <tr>
	    <td  align="center" class="sty14" rowspan="2">业务类型*</td>
	    <td height="19" colspan="7" class="sty17" >
	    	<input type="checkbox"  id="ywlx_0" disabled="disabled"/>
	        直联POS 
	        &nbsp;<input type="checkbox" id="ywlx_1" disabled="disabled"/>
	        间联POS 
	        &nbsp;<input type="checkbox"  id="ywlx_2" disabled="disabled"/>
	        基本金融菜单 
	        <input type="checkbox"  id="ywlx_4" disabled="disabled"/>
	        消费
	    	<input type="checkbox"  id="ywlx_3" disabled="disabled"/>
	        业主收款 
	        <input type="checkbox"  id="ywlx_8" disabled="disabled"/>
	        COD</td>
	 </tr>
      <tr>
	    
	    <td height="19" colspan="7" class="sty17" >
	    	<input type="checkbox"  id="TMZZ" disabled="disabled"/>
	    	同名还款
	    	<input type="checkbox" id="LCXS" disabled="disabled"/>
	    	理财销售
	        <input type="checkbox"  id="ywlx_6" disabled="disabled"/>
	        助农取款
        
	       </td>
	  </tr>
  
   <tr>
      <td height="19" align="center" class="sty14" rowspan="2">维护项目</td>
    <td colspan="7" class="cuti1" id="rt2" style="text-align:left">
      <input type="checkbox"  id="work_flag7" disabled="disabled"/>
        风险巡检
        
        <input type="checkbox" id="work_flag8" disabled="disabled"/>
        耗材配送
        
        <input type="checkbox" id="work_flag2" disabled="disabled"/>
        技术维护
        
        <input type="checkbox" id="work_flag12" disabled="disabled"/>
		商户培训
        
      <input type="checkbox"  id="work_flag10" disabled="disabled"/>
        商户移机
        
        <input type="checkbox" id="work_flag11" disabled="disabled"/>
        上门撤机
        
        <input type="checkbox"  id="work_flag9" disabled="disabled"/>
        调单
   </td>
   </tr>
   <tr>
    <td colspan="7" class="cuti1" id="rt2" style="text-align:left"><span class="cuti1" style="text-align:left">
      <input type="checkbox"  id="work_flag3" disabled="disabled"/>
        设备更换
        (
        <input type="checkbox"   disabled="disabled"/>
        主机
        
        <input type="checkbox"   disabled="disabled"/>
        键盘
        
        <input type="checkbox"   disabled="disabled"/>
        电源,更换押金
        
        <input type="checkbox"disabled="disabled"/>
        是
        
        <input type="checkbox" disabled="disabled"/>
        否
        )
        
        <input type="checkbox"  disabled="disabled"/>
        升级
        
        (
      	<input type="checkbox"   disabled="disabled"/>
	    设备
        
        <input type="checkbox"   disabled="disabled"/>
        程序
        
        )
        
    	<input type="checkbox" id="work_flag13"  disabled="disabled"/>
		其他
</td>
   </tr>
   <tr>
	    <td height="19" align="center" class="sty14" rowspan="2">机具类型</td>
	    <td height="19" class="sty17"  colspan="7">
	      <input type="checkbox"  id="jjlx_0" disabled="disabled"/>
        普通带键盘
        <input type="checkbox"  id="jjlx_7" disabled="disabled"/>
        普通不带键盘
        <input type="checkbox"  id="jjlx_1" disabled="disabled"/>
        分体POS
        <input type="checkbox"  id="jjlx_2" disabled="disabled"/>
        电话POS
        <input type="checkbox"  id="jjlx_3" disabled="disabled"/>
        移动POS
        <input type="checkbox"  id="jjlx_4" disabled="disabled"/>
        网络POS
        
         </td>
	  </tr>
      <tr>
	    <td height="19" class="sty17"  colspan="7">
	      <input type="checkbox"  id="jjlx_5" disabled="disabled"/>
        MIS
        <input type="checkbox"  id="jjlx_6" disabled="disabled"/>
        移动TD
        <input type="checkbox"  id="jjlx_8" disabled="disabled"/>
        MPOS
        </td>
	  </tr>
    <tr>
      <td height="19" align="center" class="sty14">商户名称*</td>
      <td height="19" colspan="3" class="sty16" id="mcht_name"></td>
      <td width="74" height="19" align="center" class="sty16">实际名称*</td>
      <td height="19" colspan="3" class="sty17" id="mcht_name2"></td>
      </tr>
    <tr>
      <td height="19" align="center" class="sty14">商户地址*</td>
      <td height="19" colspan="7" class="sty17" id="addr2"></td>
      </tr>
   <tr>
      <td height="19" align="center" class="sty14">商户编号*</td>
      <td height="19" colspan="3" class="sty16" id="mcht_no"></td>
      <td width="74" height="19" align="center" class="sty16">联 系 人*</td>
      <td height="19" class="sty17" id="linkman" colspan="3"></td>
      <!--td width="60" class="sty16" > 联系电话*</td>
      <td height="19" class="sty17" id="telno"></td-->
      </tr>
    <tr>
      <td height="19" align="center" class="sty14">终端编号*</td>
      <td height="19" class="sty16" id="terminal_no" colspan="3"></td>
      <!--td width="55" class="sty16" >绑定电话</td>
      <td height="19" class="sty16" id="bind_telno"></td>
      <td width="76" height="19" align="center" class="sty16">手机号码1*</td>
      <td height="19" class="sty16" id="mobile"></td-->
      <td height="19" class="sty16" align="center" >联系电话*</td>
      <td height="19" class="sty17" colspan="3" id="telno"></td>
      </tr>
   <tr>
      <td align="center" class="sty14">PSAM编号*</td>
      <td colspan="3" class="sty16" id="psam_no"></td>
      <td width="74" align="center" class="sty16">手机号码*</td>
      <td class="sty17" id="mobile" colspan="3"></td>
      </tr>
    <tr>
      <td align="center" class="sty14">卡友经理:</td>
      <td class="sty16" id="sales_manager_id" colspan="3"></td>
      <!--td width="22" class="sty16" >联系电话</td>
      <td width="37" class="sty16" id="sales_manager_telno"></td-->
      <td width="74" align="center" class="sty16">银行经理:</td>
      <td class="sty17" id="expand_manager_id" colspan="3"></td>
      <!--td class="sty16"  align="center">工&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号</td>
      <td class="sty17" id="job_num"></td-->
      </tr>
       <tr>
      <td align="center" class="sty14">绑定电话*</td>
      <td colspan="3" class="sty16" id="bind_telno"></td>
      <td width="74" align="center" class="sty16">任务执行人*</td>
      <td class="sty17"  colspan="3"><span id="work_name"></span>(<span id="big_area_name2"></span><span id="work_id" style="margin-left:5px;"></span>)</td>
      </tr>
       <tr>
      <td align="center" class="sty14">开户行</td>
      <td colspan="3" class="sty16" id="settle_bank_name"></td>
      <td width="74" align="center" class="sty16">押金条编号*</td>
      <td class="sty16"></td>
      <td class="sty16">金额：</td>
      <td width="92" class="sty17"></td>
      </tr>
      <tr>
      <td height="20" align="center" class="sty15">终端片区</td>
      <td height="20" colspan="3" class="sty19" id="big_area_name"></td>
      <td height="20" width="74" align="center" class="sty19">实际片区*</td>
      <td height="20" class="sty18"  colspan="3"></td>
      </tr>
    <tr>
        <td width="41" rowspan="3" class="zuobiaoti"><strong>设备信息</strong></td>
        <td height="20" align="center" class="sty1" colspan="1">&nbsp;</td>
        <td height="20" align="center" class="sty8" colspan="1">主机型号</td>
        <td height="20" align="center" class="sty8" colspan="1">主机编号</td>
        <td height="20" align="center" class="sty8" colspan="1">密码键盘型号</td>
        <td height="20" align="center" class="sty8" colspan="2">密码键盘编号</td>
        <td height="20" align="center" class="sty8" colspan="1">附件型号</td>
        <td height="20" align="center" class="sty9" colspan="1">附件编号</td>
    </tr>
    <tr>
        <td height="24" align="center" class="sty14">原设备信息</td>
        <td height="24" align="center" class="sty16" colspan="1">
            <span id="zjxh" ></span>
        </td>
        <td height="24" align="center" class="sty16" colspan="1">
            <span id="zjbh" ></span>
        </td>
        <td height="24" align="center" class="sty16" colspan="1">
            <span id="jpxh" ></span>
        </td>
        <td height="24" align="center" class="sty16" colspan="2">
            <span id="jpbh" ></span>
        </td>
        <td height="24" align="center" class="sty16" colspan="1">
            <span id="fjxh"></span>
        </td>
        <td height="24" align="center" class="sty17" colspan="1">
            <span id="fjbh"></span>
        </td>
    </tr>
    <tr>
        <td height="24" align="center" class="sty15">新设备信息</td>
        <td height="24" align="center" class="sty19" colspan="1">
            <span id="zjxh2" ></span>
        </td>
        <td height="24" align="center" class="sty19" colspan="1">
            <span id="zjbh2" ></span>
        </td>
        <td height="24" align="center" class="sty19"  colspan="1">
            <span id="jpxh2" ></span>
        </td>
        <td height="24" align="center" class="sty19"  colspan="2">
            <span id="jpbh2" ></span>
        </td>
        <td height="24" align="center" class="sty19"  colspan="1">
            <span id="fjxh2"></span>
        </td>
        <td height="24" align="center" class="sty18"  colspan="1">
            <span id="fjbh2"></span>
        </td>
    </tr>
    <tr>
     <td width="41" rowspan="2" class="zuobiaoti"><strong>返库签收</strong></td>
     <td height="20" align="center" class="sty12" rowspan="2">押金条返库签收</td>
     <td width="96" height="19" align="center" class="sty8" ></td>
     <td width="61" height="19" align="center" class="sty8">编号：</td>
     <td width="105" height="19" align="center" class="sty8">
     <span id="deposit_no"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="deposit_amount"></span>
     </td>
     <td height="20" align="center" class="sty12" rowspan="2">设备返库签收</td>
     <td width="107" height="19" align="center" class="sty8"></td>
     <td width="60" height="19" align="center" class="sty8">入库单号：</td>
     <td height="19" align="center" class="sty9"></td>
   </tr>
   <tr>
      <td height="24" align="center" class="sty19" colspan="3">
      <input type="checkbox" disabled="disabled"/>换机不成功返库
      <input type="checkbox" disabled="disabled"/>换机成功返库
      </td>
       <td height="24" align="center" class="sty18" colspan="3">
       <input type="checkbox"  disabled="disabled"/>换机不成功返库
       <input type="checkbox"  disabled="disabled"/>换机成功返库
       </td>
   </tr>

   <tr>
   <td width="41"  class="zuobiaoti"><strong>送纸</strong></td>
    <td height="22" align="center" class="sty12">打印纸类型</td>
    <td height="22" colspan="3" class="sty12">
     	  <input type="checkbox" name="checkbox" id="pt_1" disabled="disabled"/>
          	热敏
	      <input type="checkbox" name="checkbox2" id="pt_0" disabled="disabled"/>
	        热敏(小)
          <input type="checkbox" name="checkbox3" id="pt_2" disabled="disabled"/>
            两联针打
          <input type="checkbox" name="checkbox4" id="pt_4" disabled="disabled"/>
	        三联针打
     </td>
    <td height="22" align="center" valign="middle" class="sty12">打印纸数量</td>
    <td height="22"  class="sty12">&nbsp;</td>
    <td height="22" colspan="2" align="center" valign="middle" class="sty13">
    <input type="checkbox"  disabled="disabled"/>
      卷  
      <input type="checkbox" name="checkbox15" id="checkbox15" disabled="disabled"/>
      叠  
  <input type="checkbox" name="checkbox16" id="checkbox16" disabled="disabled"/>
      箱</td>
  </tr>
   <tr>
    <td width="41"  class="zuobiaoti"><strong>故障描述及处理意见</strong></td>
    <td height="60" colspan="8" align="left" valign="top" class="sty3">故障描述及处理意见：<span id="note" name="note" style="font-weight:bold;"></span></td>
    </tr>

   <tr>
    <td width="41" class="zuobiaoti"><strong>现场处理结果</strong></td>
    <td height="120" colspan="8" valign="top" class="sty3">
    	<div style="height:30px; line-height:30px;"><span id="install_date"></span></div>
        <div style="height:40px; line-height:90px;"></div>
        <div style="margin-left:460px;font-size: 15px;height:28px; line-height:28px;"><strong>执行人签名：</strong></div>
        <span  style="font-size:12px; font-weight: normal; margin-left:550px;">年&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;月&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;日</span>
    </td>
  </tr>
  <tr>
    <td width="41" class="zuobiaoti"><strong>商户确认</strong></td>
    <td colspan="8"  valign="middle" height="116" class="sty3"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <strong> <span class="cuti"><span class="cuti">本人已熟练掌握了POS刷卡机的使用方法及刷卡的注意事项，并承诺签购单保留12个月以<br />
      上且不进行违规移机和变更刷卡机绑定电话。</span></strong>
      <span class="cuti"><strong style="margin-left:440px;">商户签字(盖章):</strong></span><br />
      <br />
      <span style="font-size:12px; font-weight: normal; margin-left:550px;">年&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;月&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;日</span></td>
  </tr>
  <tr>
    <td width="41" class="zuobiaoti"><strong>商户意见及建议</strong></td>
    <td colspan="8"  height="91" class="sty3" align="left" valign="top">您是否对安装人员的服务态度满意？
    <span style="margin-left:280px;">
      <input type="checkbox" name="checkbox" id="checkbox" disabled="disabled"/>
    很满意
    <input type="checkbox" name="checkbox2" id="checkbox2" disabled="disabled"/>
    满意
    <input type="checkbox" name="checkbox3" id="checkbox3" disabled="disabled"/>
    一般
    <input type="checkbox" name="checkbox4" id="checkbox4" disabled="disabled"/>
    不满意
    </span><br /><br />
    意见及建议：
    </td>
  </tr>
   <tr>
     <td width="41" class="zuobiaoti" rowspan="2"><strong>评审</strong></td>
     <td height="26" colspan="8" align="left" valign="middle" class="sty5"><span class="cuti1">
       <input type="checkbox" name="checkbox17" id="checkbox17" disabled="disabled"/>
       </span>合格 <span class="cuti1">
        <input type="checkbox" name="checkbox18" id="checkbox18" disabled="disabled"/>
      </span>不合格&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;说明：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   组长签字：</td>
   </tr>
   <tr>
      <td height="26" colspan="8" align="left" valign="middle" class="sty6"><span class="cuti1">
        <input type="checkbox" name="checkbox19" id="checkbox19" disabled="disabled"/>
      </span>合格 <span class="cuti1">
      <input type="checkbox" name="checkbox20" id="checkbox20" disabled="disabled"/>
      </span>不合格&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;说明：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   评审签字：</td>
   </tr>
  <!--tr>
    <td width="41" class="zuobiaoti"><strong>签购单粘贴处</strong></td>
    <td colspan="8" align="left" valign="top" class="sty3"><span class="cuti2">现场维护要求:<br /></span>
      <span class="cuti2">  1.所有类型的维护，到了商户现场以后，都需要先核实商户地址、营业执照和经营项目是否与维护工作单上一致；<br /></span>
      <span class="cuti2">  2.对于更换主机或者密码键盘的商户，更换完成以后，必须将打印出的测试单与商户之前签购单仔细核对，避免换错机具；<br /></span>
      <span class="cuti2">  3.维护完成以后，必须对POS机的所有功能进行一次全面的测试；<br /></span>
      <span class="cuti2">  4.对于风险商户巡检、到商户现场之前，请不要与商户电话联系，根据地址找不到商户以后，再与商户电话联系；<br /></span>
      <span class="cuti2">  5.所有维护完成以后，请检查机具程序是否为银联二代程序、机具是否支持IC卡插卡交易、密码键盘是否支持非接；<br /></span>
      <span class="cuti2">  6.所有维护完成以后，请打印测试工作单，并将工作单粘贴在“签购单粘贴处”，如果商户不同意，请注明原因；<br /></span>
      <span class="cuti2">  7.维护任务完成以后必须打印IC卡测试单，并粘贴在“签购单粘贴处”，如果商户不同意，请注明原因；<br /></span>
      <span class="cuti2">  8.如果商户POS机信付通开通了消费或者业主收款功能，则需打印消费或者业主收款一分钱测试签购单；</span>
      </td>
  </tr-->
  </table>
</div>

<div style="margin-left:20%"></div>
</body>
</html>'; //这里是文件的内容
file_put_contents($f, $cache);//保存为HTML
    }

}
