import { Controller } from '@hotwired/stimulus';
import jquery from 'jquery';

var test2 = {};
/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    initialize() {
    }
    connect() {
        jquery( document ).ready(function() {
            jquery('.datatables').each(function (el) {
              var ahah = jquery(this).attr('id')
                if(!jquery.fn.DataTable.isDataTable('#' + ahah)){
                test2[ahah]= jquery(this).DataTable({
                  "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/German.json"
                  },
                  "columnDefs": jquery(this).data('columndef'),
                  "processing": true,
                  "serverSide": true,
                  "paging": true,
                  "info": true,
                  "searching": true,
                  "responsive": true,
                  //TODO: supprimer les boutons
                  "buttons": [],
                  "pageLength": 25,
                  "lengthMenu": [[25, 50, 100], [25, 50,100]],
                  "searching": false,
                  "dom": "tBlifp",
                  "ajax": {
                    "url": jquery(this).data('url'),
                    "type": "POST",
                    "data": function (d) {
                      d.request = jquery('#' + ahah).data('params');
                      d.request[0].testouille = jquery('#' + ahah).data('columndef');
                    },
                  },
                  rowReorder: {
                    dataSrc: 'position'
                  },     
                });
                test2[ahah].on('draw', function () {
                  jquery('#loader').hide();
                  jquery('#loader').css('display', 'none');
                })
                test2[ahah].on('row-reorder', function (e, diff, edit) {
                  var data = {};
                  for (var i = 0, ien = diff.length; i < ien; i++) {
                    var rowData = test2[ahah].row(diff[i].node).data();
                    data[diff[i].newPosition] = +diff[i].oldPosition;
                  }
                  jquery.ajax({
                    url: jquery(this).data('url') + '/modifyPos',
                    data: { data: data, entity: jquery('#' + ahah).data('params') },
                    method: "POST",
                    success: function (data) {
            
                    }
                  })
                })        .on( 'draw.dt', function () {
                  console.log( 'Loading' );
                //Here show the loader.
                //jquery('#loader').show();
              } )
              .on( 'init.dt', function () {
                  console.log( 'Loaded' );
                //Here hide the loader.
                jquery('#loader').hide();
              } )
                window.ronchonchon = test2;
              }
            });
          
            //setTimeout(filterDatatables, 500);
          })
    }

    link(e) {
      var url = e.currentTarget.dataset.url
      window.location.href = url
    }

    showHalfDay(e) {
      if(jquery('#calendar_startDate').val() == jquery('#calendar_endDate').val()){
        jquery('#calendar_endMorning').hide()
      }else{
        jquery('#calendar_endMorning').show()
      }
    }

    filterDatatables(e){
      console.log(jquery('.filters  input'))
      var params = test2['history'].ajax.params()
      if (typeof params['request'][0]['params'] == 'undefined') {
        params['request'][0]['params'] = {}
      }
      var paramInit = { ...params['request'][0]['params'] }
    
      jquery('.filterTable').each(function (el) {
        console.log(el);
        if (jquery(this).val().length > 0) {
          params['request'][0]['params'][jquery(this).data('searchfield')] = jquery(this).val()
        }
      })
      test2['history'].ajax.params(params)
      test2['history'].ajax.reload();
      //params['request'][0]['params'] = paramInit
      //table.draw();
    }

    changeMonth(e){
      var month = jquery('#selectMonth').val()
      var year = jquery('#selectYear').val()
      var finalString = month + '/' + year
      var actualUrl = window.location.href
      var beginString= actualUrl.substring(0, actualUrl.length - 7);
      window.location.href = beginString + finalString

    }
}
