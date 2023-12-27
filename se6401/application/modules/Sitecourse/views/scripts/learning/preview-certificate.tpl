
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Merienda:wght@700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

<style type="text/css">
    #global_page_sitecourse-learning-preview-certificate #global_content_simple {
        width: 100%;
    }
    .print_certification {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        width: 100%;
        max-width: 65%;
        margin: 0 auto 30px;
    }
    .print_certification button.print_certificate {
        padding: 0 28px;
        height: 45px;
        line-height: 45px;
        border-radius: 100px;
    }
</style>

<div id='certificate'>
    <span><?php echo $this->bodyHTML; ?>    
</div>
<span class="print_certification">
    <button class="print_certificate" type="button" onclick="PrintDiv();" />Print</button>
</span>


<script type="text/javascript">     
    function PrintDiv() {    
       var divToPrint = document.getElementById('certificate');
       var popupWin = window.open('', '_blank', 'layout:landscape; margin:minimum; scale:customized(80)');
       popupWin.document.open();
       popupWin.document.write('<html><head><link href="https://fonts.googleapis.com/css2?family=Merienda:wght@700&display=swap" rel="stylesheet"></head><body onload="window.print()">' + divToPrint.innerHTML + '</html>');
        popupWin.document.close();
            }
 </script>





