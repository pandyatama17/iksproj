<script type="text/javascript">
$(window).on('load',function(event)
{
  @if (Session::has('excel'))
    event.preventDefault();
    $.when(window.location.assign("{{url('/')}}/delivery/export&id={{Session::get('excel')}}")).done(function() {
      setTimeout(function(){ $('.page-loader').removeClass('show'); }, 100);
    });
  @else
    setTimeout(function(){ $('.page-loader').removeClass('show'); }, 100);
  @endif
});
$.fn.datepicker.dates['id'] = {
    days: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
    daysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
    daysMin: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
    months: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
    monthsShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
    today: "Hari Ini",
    clear: "Clear",
    format: "mm/dd/yyyy",
    titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
    weekStart: 0
};
$(document).ready(function()
{
  @if (Session::has('message'))
    toastr.{{Session::get('message-type')}}("{{Session::get('message')}}");
    Swal.fire("{{Session::get('message-title')}}","{{Session::get('message')}}","{{Session::get('message-type')}}");
  @endif
  $("#blendingCol").hide();
  $('.datatable-responsive').DataTable({
    "responsive": true
  });
  var journalTable = $("#journalTable").DataTable({
    "responsive": true, "lengthChange": false, "autoWidth": false,
    'columnDefs': [ {
            'orderable': false,
            // 'className': 'select-checkbox',
            // 'targets':   0,
            // 'width': "3%"
        } ],
    "select": {
            "style": "multi",
             // 'selector': 'td:first-child'
        },
    "buttons": [
        {extend: "excel", text:"<i class='fa fa-file-excel'></i> Excel", className: "btn-sm btn-success",exportOptions: {columns: ':visible'}},
        {extend: "pdf", text:"<i class='fa fa-file-pdf'></i> PDF", className: "btn-sm btn-danger",exportOptions: {columns: ':visible'}},
        {extend: "print", text:"<i class='fa fa-print'></i> Print", className: "btn-sm btn-info",exportOptions: {columns: ':visible'}},
        {extend:"colvis", text: "<i class='fa fa-list'></i> Tampilkan Kolom",className: "btn-sm"}]
  });
  // journalTable.buttons().container().appendTo('#journalTable_wrapper .col-md-6:eq(0)');
  journalTable.buttons().container().appendTo('#journalExportsWrapper');

  var selectedAll = false;
    $("#journalSelectAll").on( "click", function(e) {
      journalTable.rows().deselect();
      if ($(this).is( ":checked" )) {
        if (selectedAll != true) {
          journalTable.rows().select();
          selectedAll = true;
          $("#allDataBtn").addClass("btn-primary").removeClass("btn-secondary");
          $("#journalActionsWrapper").find("button").attr('disabled',true);
        }
        else {
          journalTable.rows().deselect();
          selectedAll = false;
          $("#allDataBtn").addClass("btn-secondary").removeClass("btn-primary");
          $("#journalActionsWrapper").find("button").attr('disabled',false);
        }
      }
    });
    $("#journalSelectActive").on( "click", function(e) {
      if ($(this).is( ":checked" )) {
          selectedAll = false;
          $("#allDataBtn").addClass("btn-secondary").removeClass("btn-primary");
          journalTable.rows().deselect();
          journalTable.rows('.active').select();
          $("#journalActionsWrapper").find("button").attr('disabled',true);
      }
    });
    $("#journalSelectInactive").on( "click", function(e) {
      if ($(this).is( ":checked" )) {
          selectedAll = false;
          $("#allDataBtn").addClass("btn-secondary").removeClass("btn-primary");
          journalTable.rows().deselect();
          journalTable.rows('.inactive').select();
          $("#journalActionsWrapper").find("button").attr('disabled',false);
      }
    });
  journalTable.on( 'select', function ( e, dt, type, indexes ) {
    if ( type === 'row' ) {
      var rows = journalTable.rows({selected:true}).indexes();
      var selectedData = journalTable.cells(rows, 6).data().toArray().toString();
      // var data = $.parseJSON(selectedData);
      // var data = JSON.stingify(selectedData);
      console.log(selectedData);
      $("#deliveryIndexes").val(selectedData);
    }
  });
    $('[data-toggle="tooltip"]').tooltip({
                "html": true,
                "delay": {"show": 500, "hide": 0},
            });
  $("#journalFormSubmit").on("click",function(e)
  {
    // e.preventDefault();
    var datas = journalTable.cells(journalTable.rows({selected:true}).indexes(), 6).data().toArray().length + 1;
    Swal.fire({
      title : "Konfirmasi hapus data",
      text : "apakah anda yakin akan menghapus ("+datas+") data?",
      icon: "warning",
      showCancelButton  : true,
      cancelButtonColor : '#DC143C',
      cancelButtonText  : "Batalkan"
    }).then((confirm) => {
      if(confirm.isConfirmed){
        $("#journalFormSubmitButton").trigger("click");
        // $('#journalForm').submit();
        // window.location.href("");
      }
    });
  });
  $('.datatable-sm').dataTable({
    "dom": '<"pull-left"f><"pull-right"l>tip',
    "bPaginate": true,
    "pageLength" : 5,
    "responsive" : false,
    "bLengthChange": false,
    "bFilter": true,
    "bInfo": false,
    "bAutoWidth": false
  });
  $(".datepicker").datepicker({
    calendarWeeks: true,
    todayHighlight: true,
    autoclose: true,
    format: "dd-mm-yyyy",
    language : "id"
  });
  $(".select2").select2({

    dropdownParent : $('.modal')
  });
  $(".icheck").iCheck({
     checkboxClass: 'icheckbox_flat-blue',
     radioClass: 'iradio_flat-blue',
  });
  // $(".poolKind").on('ifClicked', function(event){
  //     alert("alert");
  // });
  $('input[type=radio][name=poolKind]').change(function() {
      // $("#typeNameLabel").text("Nama "+$(this).val());
      $("#txtPoolName").val($(this).val()+" ");
  });
  @if (str_contains(url()->current(), '/management/journaling'))

    var start = '{{$startdate}}', end = '{{$enddate}}';
    // function cb(start,end) {;8ee
    //       $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    //   }
    var drp = $('#daterange').daterangepicker({

      locale: {
        "customRangeLabel": "Pilih Tanggal",
        format: 'DD-MM-YYYY',
        separator: " -sampai- "
      },
      minDate : moment('{{$startdate}}', 'DD-MM-YYYY'),
      maxDate : moment(),
      showDropdowns: true,
      ranges : {
        'Semua Data' : [moment('{{$startdate}}','DD-MM-YYY'), moment()],
      },
    });
    drp.startDate = '{{$startdate}}';
    drp.endDate = '{{$enddate}}';
    // $('input[name="date_picker"]').data('daterangepicker').setMinDate(moment());
  @endif
  $('.phoneCol').text(function(i, text) {
    return text.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
  });
});

$(".url-redirect").on('click',function(e) {
  e.preventDefault();
  var url = $(this).attr('href');
  // $('.control-sidebar').trigger('click');
  $('.page-loader').addClass('show');
  setTimeout(function(){ window.location.href = url }, 100);
});
$(".url-unavailable").on('click',function(e) {
  e.preventDefault();
  Swal.fire("Dalam Pengembangan","fitur ini belum tersedia!",'warning')
});
$("#transportSelect").on('change',function(event)
{
    const url = "{{url('/')}}/ajaxCall/drivers&transport=" + $(this).val();
    $.ajax({
      url : url,
      type: 'GET',
      dataType: 'HTML',
      success: function(response)
      {
        $("#driverCol").html(response);
      }
    })
});
$("#carSelect").on('change',function(event)
{
    const url = "{{url('/')}}/ajaxCall/driverDetails&driverID=" + $(this).val();
    $.ajax({
      url : url,
      type: 'GET',
      dataType: 'HTML',
      success: function(response)
      {
        var driver = $.parseJSON(response);
        $("#transportTxt").val(driver.transport);
        $("#driverNameTxt").val(driver.name);
        $("#driverIDHidTXT").val(driver.id);
        $("#driverLicenseHidTXT").val(driver.license_plate_no);
        $("#driverNameTxt").attr('readonly',false);
      }
    })
});
// $("#addDOModal").on('shown.bs.modal',function() {
//   var donum = $("#DONumberTxt");
//   const lastIndex = $("#indexDOCount").val();
//   var newIndex = parseInt(lastIndex) + 1;
//   const index = zeroFill(newIndex,3);
//   const code = donum.data('code') + index;
//
//   donum.val(code);
// });
$("#addDOButton").on('click',function() {
  var donum = $("#DONumberTxt");
  const lastIndex = $("#indexDOCount").val();
  var newIndex = parseInt(lastIndex) + 1;
  const index = zeroFill(newIndex,3);
  // const code = donum.data('code') + index;
  const code = index;
  setTimeout(function(){ $("#blendingCheck").iCheck('uncheck');}, 1);
  donum.val(code);
  $("#addDOModal").modal('show');
});
$("#blendingCheck").on('ifUnchecked',function(event)
{
  $("#blendingCol :input").val('');
});
$("#blendingCheck").on('ifChanged', function(event){
  $("#blendingCol").slideToggle();
});
$("#addDOModal").on('hidden.bs.modal',function() {
  $("#addDOModal :input").not('[name="_token"]').not('[readonly="true"]').val('');
});
// $('#customerTxt').autocomplete({
//   serviceUrl: '/ajaxCall/getReference&header=3',
//   appendTo : '#suggestions-container',
//   onSelect: function (ref) {
//     alert('You selected: ' + ref.body);
//   }
// });
$('#customerTxt').autocomplete({
    // serviceUrl: '/tracking/ajaxCall/getReference&header=3',
    serviceUrl: '{{url('/')}}/ajaxCall/getReference&header=3',
    dataType: 'json',
    responseTime: 10,
    type: 'GET',
    ajaxSettings:{
      beforeSend: function(jqXHR, settings) {
          $("#customerTxt").addClass('pulse');
        },
        success: function() {
          $("#customerTxt").removeClass('pulse');
        }
    },
    autoSelectFirst: true
});
$('#codeTxt').autocomplete({
    serviceUrl: '{{url('/')}}/ajaxCall/getReference&header=1',
    dataType: 'json',
    responseTime: 10,
    type: 'GET',
    ajaxSettings:{
      beforeSend: function(jqXHR, settings) {
          $("#codeTxt").addClass('pulse');
        },
        success: function() {
          $("#codeTxt").removeClass('pulse');
        }
    },
    autoSelectFirst: true
});
$('#senderTxt').autocomplete({
    serviceUrl: '{{url('/')}}/ajaxCall/getReference&header=3',
    dataType: 'json',
    responseTime: 10,
    type: 'GET',
    ajaxSettings:{
      beforeSend: function(jqXHR, settings) {
          $("#senderTxt").addClass('pulse');
        },
        success: function() {
          $("#senderTxt").removeClass('pulse');
        }
    },
    autoSelectFirst: true
});
$('#recipientTxt').autocomplete({
    serviceUrl: '{{url('/')}}/ajaxCall/getReference&header=3',
    dataType: 'json',
    responseTime: 10,
    type: 'GET',
    ajaxSettings:{
      beforeSend: function(jqXHR, settings) {
          $("#recipientTxt").addClass('pulse');
        },
        success: function() {
          $("#recipientTxt").removeClass('pulse');
        }
    },
    autoSelectFirst: true
});
$('#FreightLoadTxt').autocomplete({
    serviceUrl: '{{url('/')}}/ajaxCall/getReference&header=2',
    dataType: 'json',
    responseTime: 10,
    type: 'GET',
    ajaxSettings:{
      beforeSend: function(jqXHR, settings) {
          $("#FreightLoadTxt").addClass('pulse');
        },
        success: function() {
          $("#FreightLoadTxt").removeClass('pulse');
        }
    },
    autoSelectFirst: true
});
$(".editDO").on('click',function(event)
{
  var do_id = $(this).data('doid');
  $.get({
    url : '{{url('/')}}/ajaxCall/getDO&id='+do_id,
    dataType : 'JSON',
    beforeSend : pageload(),
    success: function(response){
      // var obj = $.parseJSON(response);
      console.log(response);
      $("#doIDTxt").val(response.id);
      $("#codeTxt").val(response.code);
      $("#customerTxt").val(response.customer_name);
      $("#DONumberTxt").val(response.do_number);
      $("#tonnageTxt").val(response.tonnage);
      $("#fareTxt").val(response.fare);
      if (response.blending_origin) {
        $('#blendingCheck').iCheck('check');
        $('#blendingOriginTxt').val(response.blending_origin);
        $('#blendingFareTxt').val(response.blending_fare);
        $('#blendingTonnageTxt').val(response.blending_tonnage);
      }
      else {
        $('#blendingCheck').iCheck('uncheck');
      }
      $("#carSelect").val(response.driver_id).trigger('change');
      pageload();
      $('#addDOModal').modal('show');
    }
  });
});
$("#addTransportBtn").on('click',function() {
    $("#transportModal").modal('show');
});
$("#blendingCheck").on('ifChecked',function() {
  $("#blendingRefSelect").prop('disabled',false);
});
$("#blendingCheck").on('ifUnchecked',function() {
  $("#blendingRefSelect").prop('selectedIndex',0).trigger('change');
  $("#blendingRefSelect").prop('disabled',true);
})
$("#deliverySelect").on('change',function() {
  var url = "{{url('/')}}/ajaxCall/getDeliveryDetails&id="+$(this).val();
  var code = $(this).find('option:selected').text();
  $.get({
    url: url,
    dataType: 'json',
    beforeSend: function(jqXHR, settings) {
        $(".page-loader").addClass('show');
    },
    success: function(response) {
      $("#adminTxtFalse").val(response.admin);
      $("#customerNameTxtFalse").val(response.customer_name);
      $("#freightLoadTxtFalse").val(response.freight_load);
      $("#poolTxtFalse").val(response.pool);
      $("#dateTxtFalse").val(response.date);
      $(".false-input").prop('disabled',true);
      // $(".page-loader").removeClass('show');
      $("#DOIndex").val('1');
      $("#do-form").html('');
      newDOChild($("#deliverySelect").val(),code,$("#DOIndex").val());
      if ($(window).width() <= 768)
      {
        $("#header-card").CardWidget('collapse');
      }
      $("#form-footer").slideDown();
    }
  });
  $("#addDOChild").on('click',function()
  {
      var id = $("#deliverySelect").val();
      var code = $("#deliverySelect").find('option:selected').text();
      var currRow = $("#DOIndex").val();
      newDOChild(id,code,currRow);
  });
});
$('body').on('click','a.finish-delivery',function(event) {
    event.preventDefault();
    Swal.fire({
      title : 'Konfirmasi Penyelesaian',
      html  : 'apakah anda yakin ingin menyelesaikan rekapan <b>' + $(this).data('code') + '</b>?',
      icon  : 'warning',
      showCancelButton  : true,
      cancelButtonColor : '#DC143C',
      cancelButtonText  : "Batalkan"
    }).then((confirm) => {
      if(confirm.isConfirmed){
        if ($(this).data('exported') == true)
        {
          // Swal.fire('Belom bisa wkjwkw', '', 'success')
          Swal.fire({
            title : 'Konfirmasi',
            html  : 'ketik "selesai" untuk mengkonfirmasi penyelesaian rekap <b>' + $(this).data('code') + '</b>',
            input : 'text',
            showCancelButton : true,
            cancelButtonText : 'Batalkan',
            preConfirm  : (hapus) =>{
            if (hapus.toUpperCase() == 'SELESAI') {
              // Swal.fire('Belom bisa wkjwkw', '', 'success')
              var url = "{{url('/')}}/delivery/finish&id=" + $(this).data('id');
              $(".page-loader").addClass('show');
              window.location.href = url;
            }
            else if (hapus.toUpperCase() != 'SELESAI') {
              Swal.fire('Inputan anda salah!', '', 'error');
            }}
          })
        }
        else {
          Swal.fire({
            title : 'Belum Diexport',
            text  : 'Rekapan ini belum di export, silahkan lakukan export terlebih dahulu, atau pilih "Export & Selesaikan"',
            icon  : 'warning',
            showCancelButton  : true,
            cancelButtonColor : '#DC143C',
            cancelButtonText  : "Batalkan",
            confirmButtonText : "Export & Selesaikan",
            // showDenyButton    : true
          }).then((confirm) => {
            if(confirm.isConfirmed){
              window.location.href = "{{url('/')}}/delivery/exportFinish&id="+ $(this).data('id');
              // Swal.fire('Belom bisa wkjwkw', '', 'success');
            }
          });
        }
      }
    });
});
$(".ds-url").on('click',function(event){
  event.preventDefault();
  var transport_id = $(this).data('id');
  $.get({
    url : $(this).attr('href'),
    dataType : 'html',
    beforeSend : pageload(),
    success : function(response) {
      $("#driversCol").html(response);
      $("#transportID").val(transport_id);
      pageload();
    }
  })
});

$(".triggerEditUserInfo").on('click',function(event)
{
  var data = $(this).data();
  pageload();
  $("#txtHidUser").val(data.id);
  $("#txtUserName").val(data.name);
  $("#txtUserEmail").val(data.email);
  if (data.pool == 0) {
    $("#selectPool").attr('disabled','true');
    // $("#selectPool").hide();
  }
  else {
    $("#selectPool").removeAttr('disabled');
    // $("#selectPool").show();
  }
  pageload();
  $("#userInfoModal").modal('show');
});

var table = $('#delivery-master-table').DataTable({
        "processing": true,
        "language" : '<div class="loader-overlay show page-loader"></div><div class="spanner show page-loader"><div class="loader"></div>  <p>Mohon menungggu, halaman sedang dimuat.</p></div>',
        "serverSide": true,
        "responsive": true,
        "ajax": {
            "url" : '{{route('get_deliveries_json')}}',
            "dataType" : 'json',
            "type" : 'post',
            "data" : {_token : '{{csrf_token()}}'}
        },
        "columns": [
            {"data" : "code"},
            {"data" : "date"},
            {"data" : "customer"},
            {"data" : "pool"},
            {"data" : "sender"},
            {"data" : "recipient"},
            {"data" : "freight_load"},
            {"data" : "tonnage"},
            {"data" : "rit"},
            {"data" : "options"},
        ],
        columnDefs: [
        {
            targets: [0,1,2,6,8],
            className: 'align-middle'
        }]
    });
// $("#journalForm").on('submit',function(event)
// {
//   event.preventDefault();
//   $.post({
//     url: $(this).attr('action'),
//     data: $(this).serialize(),
//     dataType: 'HTML',
//     beforeSend: function(jqXHR, settings) {
//         $(".page-loader").addClass('show');
//     },
//     success: function(response) {
//       $("#journalCol").html(response);
//       $(".page-loader").removeClass('show');
//       // $('body, html').animate({
//       //   scrollTop: $("#do-row-"+index).offset().top
//       // }, 600);
//     }
//   });
// });
function pageload()
{
    if($(".page-loader").hasClass('show'))
    {
        $(".page-loader").removeClass('show');
    }
    else {
        $(".page-loader").addClass('show');
    }
    // if (action == 'show'){
    //   $(".page-loader").addClass('show');
    // }
    // else {
    //   $(".page-loader").removeClass('show');
    // }
}
function newDOChild(id,code,index)
{
  var url = "{{url('/')}}/ajaxCall/newDOLine&id="+id+"&code="+code+"&index="+index;

  $.get({
    url: url,
    dataType: 'html',
    beforeSend: function(jqXHR, settings) {
        $(".page-loader").addClass('show');
    },
    success: function(response) {
      $("#do-form").append(response);
      $(".page-loader").removeClass('show');
      index++;
      $("#DOIndex").val(index);
      // $('body, html').animate({
      //   scrollTop: $("#do-row-"+index).offset().top
      // }, 600);
    }
  });
}

function zeroFill( number, width )
{
  width -= number.toString().length;
  if ( width > 0 )
  {
    return new Array( width + (/\./.test( number ) ? 2 : 1) ).join( '0' ) + number;
  }
  return number + ""; // always return a string
}
</script>
