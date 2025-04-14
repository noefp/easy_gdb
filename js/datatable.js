// This function applies all the features that make up a table such as, filters, pagination and data download buttons in different formats.
//function ( ids tables, ids button to select rows)

function datatable(table_id,select_id) {

    if ($(table_id+' thead tr').length < 2) { //if the table has already been deployed before
  
        //  create data table checkbox column at the first position
        $(table_id+' thead tr:eq(0)').prepend('<th style="max-width: 120px;">Select</th>');
        $(table_id+' tbody tr').each(function() {
          $(this).prepend('<td><input type="checkbox" class="row-select"></td>');
        });
    
  // -------------------------------------------------------------------------------------------------------------------------------
  // ------------ column search ----------------------------------------------------------------------------------------------------
        $(table_id+' thead tr').clone(true).appendTo(table_id+' thead');

    
        $(table_id+' thead tr:eq(0) th').each(function(i) {
          var title = $(this).text();
          if (title !== "Select") {
            $(this).html('<input style="text-align:center; border: solid 1px #808080; border-radius: 4px; width: calc(' + title.length + 'ch + 80px);" type="text" placeholder="Search ' + title + '" />');
            // $(this).html('<input style= "text-align:center; border: solid 1px #808080; border-radius: 4px; padding-right: 0; type="text" placeholder="Search ' + title + '" />');
        $('input', this).on('keyup change', function() {
          if (table.column(i).search() !== this.value || (table.column(i).search() == "" ) ){
            var colIndex = table.colReorder.transpose(i);
            if (table.column(colIndex).search() !== this.value) {
              table
                .column(colIndex)
                .search(this.value)
                .draw();
            }       
          }
        });
      } else {
        var html_element = '<button style=" width:110px; border-radius: 4px; white-space: nowrap; border: solid 1px #808080; padding: 0;" class="btn btn_select_all" id="toggle-select-btn' + select_id + '"><span">Select All</span></button>';
        $(this).html(html_element); 
      }
    });
  }
    
  // -----------------------------------------------------------------------------------------------------------------------
  
    
      var table=$(table_id).DataTable({
        dom:'Bfrtlpi',
        "oLanguage": {
          "sSearch": "Filter by:"
          },
        buttons: [
          {
              "extend": 'copy',
               "exportOptions": {
                "rows": function ( idx, data, node ) {
                  return $(node).find('input.row-select').is(':checked');
                },
                  "columns": function (idx, data, node) {
                    return $(node).is(':visible') && idx !== 0;
                }
              },
              action: function(e, dt, button, config) {
                if ($(table_id+' tbody input.row-select:checked').length > 0) {
                  $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
                } else {
                  alert("Please select rows to copy.");
                }
              }
            },
            {
              "extend": 'csv',
              "exportOptions": {
                "rows": function ( idx, data, node ) {
                  return $(node).find('input.row-select').is(':checked');
                },
                "columns": function (idx, data, node) {
                  return $(node).is(':visible') && idx !== 0;
              }
              },
              action: function(e, dt, button, config) {
                if ($(table_id+' tbody input.row-select:checked').length > 0) {
                  $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                } else {
                  alert("Please select rows to export as CSV.");
                }
              }
            },
            {
              "extend": 'excel',
              "exportOptions": {
                "rows": function ( idx, data, node ) {
                  return $(node).find('input.row-select').is(':checked');
                },
                "columns": function (idx, data, node) {
                  return $(node).is(':visible') && idx !== 0;
              }
              },
              action: function(e, dt, button, config) {
                if ($(table_id+' tbody input.row-select:checked').length > 0) {
                  $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                } else {
                  alert("Please select rows to export as Excel.");
                }
              }
            },
            {
              "extend": 'pdf',
              "orientation": 'landscape',
              "pageSize": 'LEGAL',
              "exportOptions": {
                "rows": function ( idx, data, node ) {
                  return $(node).find('input.row-select').is(':checked');
                },
                "columns": function (idx, data, node) {
                  return $(node).is(':visible') && idx !== 0;
              }
              },
              action: function(e, dt, button, config) {
                if ($(table_id+' tbody input.row-select:checked').length > 0) {
                  $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                } else {
                  alert("Please select rows to export as PDF.");
                }
              }
            },
            {
              "extend": 'print',
              "exportOptions": {
                "rows": function ( idx, data, node ) {
                  return $(node).find('input.row-select').is(':checked');
                },
                "columns": function (idx, data, node) {
                  return $(node).is(':visible') && idx !== 0;
              }
              },
              action: function(e, dt, button, config) {
                if ($(table_id+' tbody input.row-select:checked').length > 0) {
                  $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                } else {
                  alert("Please select rows to print.");
                }
              }
            },
            {
              extend: 'colvis',
              // text: 'Columns visivility',
              columns: ':not(:first-child)', // not allow hiding the first column
              className: 'colvis-dropdown',
              prefixButtons: [
                  {
                      text: 'Hide all',
                      action: function (e,dt) {
                          dt.columns(':not(:first-child)').visible(false); //hides all columns except the first
                      },
                      className: 'columns-show',
                  },
                  {
                      text: 'Show all',
                      action: function (e,dt) {
                          dt.columns().visible(true); // show all columns
                      },
                      className: 'columns-show',
                  }
              ]
          }
        
          ],

      
        "sScrollX": "100%",
        "sScrollXInner": "110%",
        "bScrollCollapse": true,
        retrieve: true,
        colReorder: true,
        colReorder: {
          fixedColumnsLeft: 1 // Prevent the first column from rearranging
      },
      columnDefs: [
          { targets: 0, orderable: false, searchable: false }
      ],
      "drawCallback": function( settings ) {
      // $('#body').css("display","inline");
      // $(".td-tooltip").tooltip();
      $(".dataTables_filter input").css("border-radius","5px");
  
        $("table.dataTable tbody tr").hover(
          function() {
              // Al pasar el mouse
              $(this).css("background-color", "#d1d1d1");
          }, function() {
              // Al retirar el mouse
              $(this).css("background-color", "");
          }
        );
      },
    });
  
    $(".dataTables_filter").addClass("float-right");
    $(".dataTables_info").addClass("float-left");
    $(".dataTables_paginate").addClass("float-right");
  
  
     // Initialize selection state
     $(table_id+' tbody input.row-select').prop('checked', false);
    updateToggleButton();
    toggleExportButtons();
  
    $(table_id+' tbody').on('click', 'input.row-select', function() {
      $(this).closest('tr').toggleClass('selected', this.checked);
      toggleRowHighlighting();  
      updateToggleButton();
      toggleExportButtons();
    });
  
    $('#toggle-select-btn'+select_id).on('click', function() {
      var selectedCount = $(table_id+' tbody input.row-select:checked').length;
      $(table_id+' tbody input.row-select').prop('checked', selectedCount === 0);
      toggleRowHighlighting();
      updateToggleButton();
      toggleExportButtons();
    });
  
    function toggleRowHighlighting() {
      $(table_id+' tbody tr').each(function() {
        var row = $(this);
        var isChecked = row.find('input.row-select').is(':checked');
        row.toggleClass('selected', isChecked);
      });
    }
  
  // ------------------------------------------------------------------------
    function updateToggleButton() {
      var selectedCount = $(table_id+' tbody input.row-select:checked').length;
      $('#toggle-select-btn'+select_id).text(selectedCount > 0 ? 'Unselect All' : 'Select All');
    }
  // ------------------------------------------------------------------------
    function toggleExportButtons() {
      var selectedCount = $(table_id+' tbody input.row-select:checked').length;
      $('.dt-button').prop('disabled', selectedCount === 0);
    }
  }
  