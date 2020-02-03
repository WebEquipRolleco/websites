<div id="panel_OA" class="panel">
  <div class="panel-heading">
    <i class="icon-star"></i>
    {l s="Gestion des OA" d='Admin.Global'}
    <div class="panel-heading-action">
      <a href="#reload_oa" id="reload_OA" class="list-toolbar-btn">
        <i class="process-icon-refresh"></i>
      </a>
    </div>
  </div>
  <div id="ajax_OA"></div>
</div>

<script>
  $(document).ready(function() {
    loadOA();

    $(document).on('change', '#custom_send', function() {
      if($(this).is(':checked')) {
        $('#custom_centent').show();
        $('#normal_content').hide();

        $('#specific_object').prop('required', true);
      }
      else {
        $('#specific_object').prop('required', false);

        $('#custom_centent').hide();
        $('#normal_content').show(); 
      }
    });

    $(document).on('change', '.send_supplier', function() {
      checkForDocuments();
    });

    $(document).on('change', '.specific_document', function() {
      checkForDocuments();
    });

    /**
    * Valide ou empêche la validation de l'envoi des documents
    **/
    window.checkForDocuments = function() {

      var has_supplier = false;
      var allow = true;

      $('.send_supplier').each(function() {
        var element = $(this);

        if(element.is(':checked')) {
          has_supplier = true;
          var BL = $('#doc_BL_'+element.val()).is(':checked');
          var BC = $('#doc_BC_'+element.val()).is(':checked');

          if(!BL && !BC)
            allow = false;
        }

      });

      if(!has_supplier)
        allow = false;

      $('#send_documents').prop('disabled', !allow);
    }

    $('#reload_OA').on('click', function(e) {
      e.preventDefault();
      loadOA();
    });

    /**
    * Enregistrer nouvel OA
    **/
    $(document).on('click', '#save_new_oa', function(e) {
      e.preventDefault();

      $.ajax({
        url: "{$link->getAdminLink('AdminOrders')}",
        data: { ajax:true, action:"new_oa", id_order:{$order->id}, id_supplier:$('#new_oa_id_supplier').val(), code:$('#new_oa_code').val() },
        dataType: "json",
        success : function(response) {
          $('#ajax_OA').html(response.view);
        }
      });
    });

    /**
    * Modification OA
    **/
    $(document).on('click', '#save_oa', function(e) {
      var id_oa = $(this).val();

      $.ajax({
        url: "{$link->getAdminLink('AdminOrders')}",
        data: { ajax:true, action:"save_oa", id_order:{$order->id}, id_oa:id_oa, id_supplier:$('#id_supplier_'+id_oa).val(), code:$('#code_'+id_oa).val() },
        dataType: "json",
        success : function(response) {
          $('#ajax_OA').html(response.view);
        }
      });
    });

    /**
    * Suppression OA
    **/
    $(document).on('click', '#delete_oa', function(e) {
      $.ajax({
        url: "{$link->getAdminLink('AdminOrders')}",
        data: { ajax:true, action:"delete_oa", id_order:{$order->id}, id_oa:$(this).val() },
        dataType: "json",
        success : function(response) {
          $('#ajax_OA').html(response.view);
        }
      });
    });

    function loadOA() {
      $.ajax({
        url: "{$link->getAdminLink('AdminOrders')}",
        data: { ajax:true, action:"load_oa", id_order:{$order->id} },
        dataType: "json",
        success : function(response) {
          $('#ajax_OA').html(response.view);
        }
      });
    }

  });
</script>