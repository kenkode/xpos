<?php


function asMoney($value) {
  return number_format($value, 2);
}

?>
<html >



<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<style type="text/css">

table {
  max-width: 100%;
  background-color: transparent;
}
th {
  text-align: left;
}
.table {
  width: 100%;
  margin-bottom: 2px;
}
hr {
  margin-top: 1px;
  margin-bottom: 2px;
  border: 0;
  border-top: 2px dotted #eee;
}

body {
  font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-size: 12px;
  line-height: 1.428571429;
  color: #333;
  background-color: #fff;
}



 @page { margin: 170px 30px; }
 .header { position: fixed; left: 0px; top: -150px; right: 0px; height: 150px;  text-align: center; }
 .content {margin-top: -100px; margin-bottom: -150px}
 .footer { position: fixed; left: 0px; bottom: -180px; right: 0px; height: 50px;  }
 .footer .page:after { content: counter(page, upper-roman); }



</style>

</head>

<body>

  <div class="header">
     <table >

      <tr>


       
        <td style="width:150px">

            <img src="{{public_path().'/uploads/logos/'.$organization->logo}}" alt="logo" width="80%">

    
        </td>

        <td>
        <strong>
          {{ strtoupper($organization->name)}}<br>
          </strong>
          {{ $organization->phone}} |
          {{ $organization->email}} |
          {{ $organization->website}}<br>
          {{ $organization->address}}
       

        </td>
        

      </tr>


      <tr>

        <hr>
      </tr>



    </table>
   </div>

<br>

<div class="footer">
     <p class="page">Page <?php $PAGE_NUM ?></p>
   </div>

<?php
  $d=strtotime($from);
  $d1=strtotime($to);
?>

	<div class="content" style='margin-top:0px;'>
<div align="center"><strong>Checkout details for period between {{date("F j, Y", $d).' and '.date("F j, Y", $d1)}} </strong></div><br>
<table class="table table-bordered" border='1' cellspacing='0' cellpadding='0'>

      <tr>
        
        <td width='20'><strong># </strong></td>
        <td><strong>Item </strong></td>
        <td><strong>Date Out </strong></td>
        <td><strong>Expected Date Back</strong></td>
        <td><strong>Checked out by</strong></td>
        <td><strong>Remarks</strong></td>
      </tr>
      <?php $i =1; ?>
      @foreach($checkouts as $check)
      <tr>
       <td td width='20'>{{$i}}</td>
       <td>{{ $check->item->name}}</td>
       <td>{{ $check->date_out }}</td> 
       <td>{{ $check->date_expected_back }}</td>
       <td>{{ $check->checked_out_by }}</td> 
       <td>{{ $check->remarks_out }}</td>  
      </tr>
      <?php $i++; ?>
   
    @endforeach

     
    </table>

<br><br>
  
</div>


</body>

</html>



