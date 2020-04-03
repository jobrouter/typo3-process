define([], function() {
  'use strict';

  var Toggler = {}

  Toggler.init = function() {
    window.addEventListener('load', function() {
      var tableElement = document.getElementById('jobrouter-process-links-table');

      if (!tableElement) {
        return;
      }

      tableElement.addEventListener('click', function(event) {
        var processTableFieldsToggler = event.target.closest('.jobrouter-process-table-fields-count');

        if (!processTableFieldsToggler) {
          return;
        }

        var collapseElement = processTableFieldsToggler.querySelector('.jobrouter-process-table-fields-collapse');
        var expandElement = processTableFieldsToggler.querySelector('.jobrouter-process-table-fields-expand');
        var listElement = processTableFieldsToggler.parentNode.querySelector('.jobrouter-process-table-fields-list');

        if (!collapseElement || !expandElement || !listElement) {
          return;
        }

        if (collapseElement.style.display === 'none') {
          collapseElement.style.display = '';
          expandElement.style.display = 'none';
          listElement.style.display = 'none';
        } else {
          collapseElement.style.display = 'none';
          expandElement.style.display = '';
          listElement.style.display = '';
        }
      });
    });
  };

  Toggler.init();
});
