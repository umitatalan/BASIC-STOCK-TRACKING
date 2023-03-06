    <h3>Ürünler</h3>
    <br />
    <button class="btn btn-success" onclick="add_product()"><i class="glyphicon glyphicon-plus"></i> Ürün Tanımla</button>
    <br />
    <br />
    <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th>Adı</th>
          <th>Bakiye</th>
          <th style="width:125px;">İşlemler</th>
        </tr>
      </thead>
      <tbody>
      </tbody>

      <tfoot>
        <tr>
          <th>Adı</th>
          <th>Bakiye</th>
          <th style="width:125px;">İşlemler</th>
        </tr>
      </tfoot>
    </table>

  <script type="text/javascript">

    $('ul li a').removeClass('active');
    $('#menuProduct').addClass('active');

    var save_method;
    var table;
    $(document).ready(function() {
      table = $('#table').DataTable({ 
        
        "processing": true, 
        "serverSide": true, 
        
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.3/i18n/tr.json',
        },

        "ajax": {
            "url": "<?php echo site_url('product/ajax_list')?>",
            "type": "POST"
        },

        "columnDefs": [
        { 
          "targets": [ -1 ], 
          "orderable": false,
        },
        ],

      });
    });

    function add_product()
    {
      save_method = 'add';
      $('#form')[0].reset(); 
      $('#modal_form').modal('show'); 
      $('.modal-title').text('Ürün Tanımla'); 
    }

    function edit_product(id)
    {
      save_method = 'update';
      $('#form')[0].reset();

      $.ajax({
        url : "<?php echo site_url('product/ajax_edit/')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
           
            $('[name="id"]').val(data.id);
            $('[name="Name"]').val(data.Name);
            $('[name="Balance"]').val(data.Balance);
            
            $('#modal_form').modal('show'); 
            $('.modal-title').text('Ürün Bilgi Düzenle'); 
            
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
          url = "<?php echo site_url('product/ajax_add')?>";
      }
      else
      {
        url = "<?php echo site_url('product/ajax_update')?>";
      }

          $.ajax({
            url : url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(data)
            {
              transaction_save(data.id);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Ekleme / Düzenlemede Hata! ' + textStatus);
            }
        });
    }

    function transaction_save(productId){
      $.ajax({
            url : '<?php echo site_url('transaction/ajax_add')?>',
            type: "POST",
            data: {
                CustomerId: 0,
                ProductId: productId,
                Direction: 1,
                Description: 'Sayım',
                Amount: $('[name=Balance]').val()
            },
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

    function delete_product(id)
    {
      if(confirm('Bu veriyi silmek istediğinize emin misiniz?'))
      {
          $.ajax({
            url : "<?php echo site_url('product/ajax_delete')?>/"+id,
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
  <div class="modal fade" id="modal_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Kapat"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Ürün<nav></nav></h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="form" class="form-horizontal">
          <input type="hidden" value="" name="id"/> 
          <div class="form-body">
            <div class="form-group">
              <label class="control-label col-md-3">Adı</label>
              <div class="col-md-9">
                <input name="Name" placeholder="Adı" class="form-control" type="text" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Bakiye</label>
              <div class="col-md-9">
                <input name="Balance" placeholder="Bakiye" class="form-control" type="text" required>
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
 
  </body>
</html>