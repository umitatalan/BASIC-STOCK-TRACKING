    <h3>İşlemler</h3>
    <br />
    <button class="btn btn-success" onclick="add_transaction()"><i class="glyphicon glyphicon-plus"></i> İşlem Tanımla</button>
    <br />
    <br />
    <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th>Tarih</th>
          <th>Müşteri</th>
          <th>Ürün</th>
          <th>Açıklama</th>
          <th>Miktar</th>
          <th style="width:125px;">İşlemler</th>
        </tr>
      </thead>
      <tbody>
      </tbody>

      <tfoot>
        <tr>
          <th>Tarih</th>
          <th>Müşteri</th>
          <th>Ürün</th>
          <th>Açıklama</th>
          <th>Miktar</th>
          <th style="width:125px;">İşlemler</th>
        </tr>
      </tfoot>
    </table>

  <script type="text/javascript">

    var save_method; //for save method string
    var table;

    $('ul li a').removeClass('active');
    $('#menuTransaction').addClass('active');

    $(document).ready(function() {
      table = $('#table').DataTable({ 
        
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.3/i18n/tr.json',
        },

        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo site_url('transaction/ajax_list')?>",
            "type": "POST"
        },

        //Set column definition initialisation properties.
        "columnDefs": [
        { 
          "targets": [ -1 ], //last column
          "orderable": false, //set not orderable
        },
        ],

      });

      $("select.Customer").select2({
        language: "tr",
        dropdownParent: $('#modal_form'),
        ajax: {
          url: "<?php echo site_url('person/ajax_list')?>",
          dataType: 'json',
          type: 'post',
          data: (params) => {
            return {
              'search[value]': params.term,
              start: 0,
              length: 50,
              draw: 1,
              withid : 1
            }
          },
          processResults: (data, params) => {
            const results = data.data.map(item => {
              return {
                id: item[0],
                text: item[1] + ' ' + item[2],
              };
            });
            return {
              results: results,
            }
          },
        },
      });
      $('select.Customer').change(function() {
        $('#CustomerId').val($(this).val());
      });

      $("select.Product").select2({
        language: "tr",
        dropdownParent: $('#modal_form'),
        ajax: {
          url: "<?php echo site_url('product/ajax_list')?>",
          dataType: 'json',
          type: 'post',
          data: (params) => {
            return {
              'search[value]': params.term,
              start: 0,
              length: 50,
              draw: 1,
              withid : 1
            }
          },
          processResults: (data, params) => {
            const results = data.data.map(item => {
              return {
                id: item[0],
                text: item[1],
              };
            });
            return {
              results: results,
            }
          },
        },
      });
      $('select.Product').change(function() {
        $('#ProductId').val($(this).val());
      });

      $('select[name=Direction]').change(function() {
        $('#Description').val($('select[name=Direction] option:selected').text());
      }).trigger('change');
    });
    
    function datetimeLocal(datetime) {
        const dt = new Date(datetime);
        dt.setMinutes(dt.getMinutes() - dt.getTimezoneOffset());
        return dt.toISOString().slice(0, 16);
    }
    const dateTime = document.getElementById('DateTime');
    dateTime.value = datetimeLocal(new Date());

    $('[name=DateTime]').prop('value', datetimeLocal(new Date()));

    function add_transaction()
    {
      save_method = 'add';
      $('#form')[0].reset(); 
      $('#modal_form').modal('show'); 
      $('.modal-title').text('İşlem Tanımla'); 
    }

    function edit_transaction(id)
    {
      save_method = 'update';
      $('#form')[0].reset();

      $.ajax({
        url : "<?php echo site_url('transaction/ajax_edit/')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
           
            $('[name="id"]').val(data.TransactionId);
            $('[name="DateTime"]').val(data.DateTime);
            $('[name="CustomerId"]').val(data.CustomerId);

            var customerFullName = data.CustomerName + ' ' + data.CustomerSurname;
            var customerOption = new Option(customerFullName, data.CustomerId, true, true);
            $('select.Customer').append(customerOption).val(data.CustomerId);

            $('[name="ProductId"]').val(data.ProductId);

            var productOption = new Option(data.ProductName, data.ProductId, true, true);
            $('select.Product').append(productOption).val(data.ProductId);

            $('[name="Direction"]').val(data.DirectionId);
            $('[name="Amount"]').val(data.Amount);
            
            $('#modal_form').modal('show'); 
            $('.modal-title').text('İşlem Düzenle'); 
            
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Veriler alınırkan bir hata oluştu! Hata: ' + textStatus);
        }
    });
    }

    function reload_table()
    {
      table.ajax.reload(null,false);
    }

    function save()
    {
      var url;
      if(save_method == 'add') 
      {
          url = "<?php echo site_url('transaction/ajax_add')?>";
      }
      else
      {
        url = "<?php echo site_url('transaction/ajax_update')?>";
      }

          $.ajax({
            url : url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(data)
            {
               $('#modal_form').modal('hide');
               reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Ekleme / Düzenlemede Hata! ' + textStatus);
            }
        });
    }

    function delete_transaction(id)
    {
      if(confirm('Bu veriyi silmek istediğinize emin misiniz?'))
      {
          $.ajax({
            url : "<?php echo site_url('transaction/ajax_delete')?>/"+id,
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
               $('#modal_form').modal('hide');
               reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Veri silmede hata! ' + textStatus);
            }
        });
         
      }
    }

  </script>

  <!-- Bootstrap modal -->
  <div class="modal fade" id="modal_form" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3 class="modal-title">İşlem</h3>
        </div>
        <div class="modal-body form">
          <form action="#" id="form" class="form-horizontal">
            <input type="hidden" value="" name="id"/> 
            <input type="hidden" id='CustomerId' name="CustomerId"/> 
            <input type="hidden" id='ProductId' name="ProductId"/>
            <input type="hidden" id='Description' name="Description"/>
            <div class="form-body">
              <div class="form-group">
                <label class="control-label col-md-3">Tarih</label>
                <div class="col-md-9">
                  <input id='DateTime' name="DateTime" placeholder="yyyy-mm-dd" class="form-control" type="datetime-local">
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-3">Müşteri</label>
                <div class="col-md-9">
                  <select class="Customer" style='width:100%'></select>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-3">Ürün</label>
                <div class="col-md-9">
                  <select class="Product" style='width:100%'></select>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-3">Tip</label>
                <div class="col-md-9">
                   <select name="Direction" class="form-control" style='width:100%'>
                      <option value='-1'>Satış</option>
                      <option value='1'>Alış</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-3">Miktar</label>
                <div class="col-md-9">
                <input name="Amount" placeholder="Miktar" class="form-control" type="text">
                </div>
              </div>
            </div>
          </form>
            </div>
              <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Kaydet</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">İptal</button>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
  </body>
</html>